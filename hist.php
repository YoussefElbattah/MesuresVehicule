<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Performances des courses </title>
    <?php
    $serveur = "localhost";
    $login = "root";
    $pass = "";
    try{
        $connexion = new PDO("mysql:host=$serveur;dbname=projet", $login, $pass);
        $connexion ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $requete = $connexion->prepare("SELECT Vitesse_moyenne,Accéleration_moyenne,id_course,Date_fin FROM course ORDER BY id_course DESC LIMIT 0,3  ");
        $requete->execute();
        $resultat1 = $requete->fetchAll();
        for($i = 0 ;$i<sizeof($resultat1);$i++){
            $vit[$i] = $resultat1[$i][0];
        }
        for($i = 0 ;$i<sizeof($resultat1);$i++){
            $acc[$i] = $resultat1[$i][1];
            $requete1 = $connexion->prepare("SELECT Date FROM vitesse WHERE vitesse.id_course = ".$resultat1[$i][2] );
            $requete1->execute();
            $resultat = $requete1->fetchAll();
            $originalTime = new DateTimeImmutable($resultat1[$i][3]);
            $targedTime = new DateTimeImmutable($resultat[0][0]);
            $interval = $originalTime->diff($targedTime);
            $temps = $interval->format("%H:%I:%S");
            $parsed = date_parse($temps);
            $min = $parsed['hour']*60 + $parsed['minute']  + $parsed['second'] /60 ;
            $hour = $parsed['hour'] + $parsed['minute']/60  + $parsed['second'] /3600 ;
            //echo $hour;
            $durée[$i] = $min;
            $distance[$i] = $resultat1[$i][0]*$hour;
        }
        // print_r($distance);
        //print_r($resultat);
        //  print_r($durée);
  
     }
    catch(PDOException $e){
        echo 'echec : '.$e->getMessage();
    }
 
?>
    <style>
      * {
        margin: 0;
        padding: 0;
        font-family: sans-serif;
      }
      .chartMenu {
        padding: 10px;
        font-size: 20px;
        color: lightsteelblue;
        padding: 20px;
        margin-top: 0;
        background-color: whitesmoke;
        text-align: center;
        font-style: italic;
        font-family: 'Gill Sans', 'Gill Sans MT', 'Calibri', 'Trebuchet MS', sans-serif;
      }
      .chartCard {
        width: 100vw;
        height: calc(100vh - 40px);
        background: rgba(54, 162, 235, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .chartBox {
        width: 500px;
        padding: 20px;
        border-radius: 20px;
        border: solid 3px rgba(54, 162, 235, 1);
        background: white;
      }
    </style>
  </head>
  <body>
    <div class="chartMenu">
      <h1>Caisse à savon</h1>
    </div>
    <div class="chartCard">
      <div class="chartBox">
        <canvas id="myChart"></canvas>
      </div>
      <div class="chartBox">
        <canvas id="myChart_acc"></canvas>
      </div>
      <div class="chartBox">
        <canvas id="myChart_durée"></canvas>
      </div>
      <div class="chartBox">
        <canvas id="myChart_distance"></canvas>
      </div>
    </div>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>
    <script>
        const yValues_vit = <?php { echo json_encode($vit);}
 ?>;
const yValues_acc = <?php { echo json_encode($acc);}
 ?>;
 const yValues_du = <?php { echo json_encode($durée);}
 ?>;
 const yValues_distance = <?php { echo json_encode($distance);}
 ?>;
    // setup Vitesse
    const data = {
      labels: ["Course 1","Course 2","Course 3"],
      datasets: [{
        label: 'Vitesse moyenne en km/h',
        data: yValues_vit,
        backgroundColor: [
          'rgba(255, 26, 104, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
        ],
        borderColor: [
          'rgba(255, 26, 104, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
        ],
        borderWidth: 1
      }]
    };

    // config 
    const config = {
      type: 'bar',
      data ,
      options: {
        aspectRatio : 1,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    };

    // render init block
    const myChart = new Chart(
      document.getElementById('myChart'),
      config
    );

    // setup Accélération
    const data_acc = {
      labels: ["Course 1","Course 2","Course 3"],
      datasets: [{
        label: 'Accélération moyenne en m/s²',
        data: yValues_acc,
        backgroundColor: [
          'rgba(255, 26, 104, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
        ],
        borderColor: [
          'rgba(255, 26, 104, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
        ],
        borderWidth: 1
      }]
    };

    // config 
    const config_acc = {
      type: 'bar',
      data : data_acc,
      options: {
        aspectRatio : 1,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    };

    // render init block
    const myChart_acc = new Chart(
      document.getElementById('myChart_acc'),
      config_acc
    );

    // //setup Durée
    const data_durre = {
      labels: ["Course 1","Course 2","Course 3"],
      datasets: [{
        label: 'Durée en minutes',
        data: yValues_du,
        backgroundColor: [
          'rgba(255, 26, 104, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
        ],
        borderColor: [
          'rgba(255, 26, 104, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
        ],
        borderWidth: 1
      }]
    };

    // config 
    const config_duree = {
      type: 'bar',
      data : data_durre,
      options: {
        aspectRatio : 1,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    };

    // render init block
    const myChart_durée = new Chart(
      document.getElementById('myChart_durée'),
      config_duree
    );

    const data_distance = {
      labels: ["Course 1","Course 2","Course 3"],
      datasets: [{
        label: 'Distance en km',
        data: yValues_distance,
        backgroundColor: [
          'rgba(255, 26, 104, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
        ],
        borderColor: [
          'rgba(255, 26, 104, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
        ],
        borderWidth: 1
      }]
    };

    // config 
    const config_distance = {
      type: 'bar',
      data : data_distance,
      options: {
        aspectRatio : 1,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    };

    // render init block
    const myChart_distance = new Chart(
      document.getElementById('myChart_distance'),
      config_distance
    );
    </script>

  </body>
</html>