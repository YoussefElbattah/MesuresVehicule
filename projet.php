<!DOCTYPE html>
<html lang="fr">
    <head>
            <meta  charset="UTF-8"  /> 
            <meta  name="description"   content="Vous pouvez visualiser vos données de vitesse dans cette page"/>
            <meta name="author" content="Youssef ELBATTAH" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/> 
            <link rel="stylesheet" href="projet.css">
            <!-- <meta http-equiv="refresh" content="3"> -->
            
   </head>
   
   <body>
   <?php
    $serveur = "localhost";
    $login = "root";
    $pass = "";
    try{
        $connexion = new PDO("mysql:host=$serveur;dbname=projet", $login, $pass);
        $connexion ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e){
        echo 'echec : '.$e->getMessage();
    }
   //  header("refresh : 3");
?>
       <header class="header"><h1>Caisse à savon</h1></header>
       <div class="vitesse" >
         <h2>Vitesse</h2>
         <img src="images/vitesse.png" alt="vitesse_logo" />
         <br>
            <span>
               <?php
                  $requete1 = $connexion->prepare("SELECT Vitesse FROM vitesse ORDER BY id DESC LIMIT 0,1");
                  $requete1->execute();
                  $resultat_vit = $requete1->fetch();
                  echo $resultat_vit["0"]." km/h";
               ?>
            </span>
       </div>
       <div class="acceleration">
          <h2>Accéleration</h2>
          <img src="images/acceleration.png" alt="acceleration_logo" />
          <br>
          <span> 
            <?php
               $requete2 = $connexion->prepare("SELECT Accéleration FROM acceleration ORDER BY id DESC LIMIT 0,1");
               $requete2->execute();
               $resultat_acc = $requete2->fetch();
               echo $resultat_acc["0"]." m/s²";
          ?>
           </span>
       </div>
       <div class = "frein" >
        <h2>Frein</h2>
        <img src="images/Frein.png" alt="frein_logo" />
        <br>
        <span>
          <?php
               $requete3 = $connexion->prepare("SELECT Frein FROM frein ORDER BY id DESC LIMIT 0,1");
               $requete3->execute();
               $resultat_fr = $requete3->fetch();
               echo $resultat_fr["0"]." % ";
          ?>
         </span>
       </div>
          <a href="hist.php" class="historique" target = "_blank" style="background-color: rgb(46, 43, 38);color: rgb(233, 231, 227);text-decoration: none;font-style: italic;font-weight: bold;position: absolute;right: 10%;top: 10%;">Historique des courses</a>
          <a href="freinage.php" class="freinage" target = "_blank" 
          style = "background-color:rgb(46, 43, 38);
                  color: rgb(233, 231, 227);
                  text-decoration: none;
                  font-style: italic;
                  font-weight: bold;
                  position: absolute;
                  left: 10%;
                  top: 10%;"
                  >Courbe de freinage</a>
       <!-- <footer>© COPYRIGHT 2023 - Tous Droits Réservés</footer> -->
   </body>
</html>