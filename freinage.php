<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caisse à savon</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/chart.min.js"></script>
    <?php
    $serveur = "localhost";
    $login = "root";
    $pass = "";
    try{
        $connexion = new PDO("mysql:host=$serveur;dbname=projet", $login, $pass);
        $connexion ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $requete = $connexion->prepare("SELECT id_course FROM course ORDER BY id_course DESC LIMIT 0,1");
        $requete->execute();
        $resultat1 = $requete->fetch();
        // echo $resultat1[0];
        $requete1 = $connexion->prepare("SELECT Frein,Date FROM frein WHERE frein.id_course = ".$resultat1[0] );
        $requete1->execute();
        $resultat = $requete1->fetchAll();
        for($i = 0 ;$i<sizeof($resultat);$i++)
        {
            $productname[$i]  = $resultat[$i][0];
            $sales[$i] = $resultat[$i][1];
        }
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
      .header {
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
        width: 900px;
        padding: 20px;
        border-radius: 20px;
        border: solid 3px rgba(54, 162, 235, 1);
        background: white;
      }
    </style>

</head>
<body>
    

<header class="header"><h1>Caisse à savon</h1></header>

    <div class="chartCard">
      <div class="chartBox">
        <canvas id="myChart"></canvas>
        
      </div>
    </div>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>
<script>
const xValues = <?php echo json_encode($sales); ?>;
const yValues = <?php echo json_encode($productname) ?>;

const data = {
      labels: xValues,
      datasets: [{
        label: 'Courbe de freinage',
        data: yValues,
        backgroundColor: [
          'rgba(255, 26, 104, 0.2)',
        ],
        borderColor: [
          'rgba(255, 26, 104, 1)',
        ],
        borderWidth: 1
      }]
    };

    // config 
    const config = {
      type: 'line',
      data ,
      options: {
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
</script>
</body>
</html>