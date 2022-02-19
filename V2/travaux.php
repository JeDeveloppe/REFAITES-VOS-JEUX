<?php
@session_start ();
require($_SERVER["DOCUMENT_ROOT"]."/config.php");
?>
<!doctype html>
<html lang="fr" class="m-0 p-0">
  <head>
    <?php
    require($_SERVER['DOCUMENT_ROOT']."/google-analytics.php");
    //META FACEBOOK
    require($_SERVER['DOCUMENT_ROOT']."/commun/meta-og.php");
    ?>
    <title>REFAITES VOS JEUX</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0">
    <meta name="description" content="<?php echo $descriptionPage; ?>">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/design/favicon.png" />
    <!-- Icone tactile -->
    <link rel="apple-touch-icon" href="/images/design/logoTactile.png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="/css/<?php echo $GLOBAL['versionCSS']; ?>/bootstrap/theme-bootstrap.css">
    <!-- Add fontawesome pictures -->
    <link rel="stylesheet" href="/fontawesome/css/all.min.css">
    <!-- CSS du site -->
    <link rel="stylesheet" type="text/css" href="/css/<?php echo $GLOBAL['versionCSS']; ?>/design.css?ts=<? echo time(); ?>">
     <!-- page travaux -->
     <link rel="stylesheet" type="text/css" href="/css/<?php echo $GLOBAL['versionCSS']; ?>/travaux.css">
    <!-- CSS des animations -->
    <link rel="stylesheet" type="text/css" href="/css/<?php echo $GLOBAL['versionCSS']; ?>/animation.css">
  
</head>
  <body class="m-0 p-0">
    <div class="container-fluid">
      <div class="row vh-100">
        <div class="col-sm-6 bg-refaites d-flex flex-wrap align-items-center">
          <div class="col-12 h3 text-white py-5 py-md-2">
            <p>Le site est actuellement en maintenance.</p>
            <p class="mt-5 h5">Nous devrions être de retour dans quelques minutes...<br/>
            Merci pour votre patience.</p> </div>
          </div>
        <div class="col-sm-6 d-flex justify-content-center align-items-center">
          <img class="col-12 col-md-8 mx-auto" src="/images/design/refaitesvosjeux.png" alt="">
        </div>
      </div>
   
 
 
                  
  

       
        <!-- Jquery for Ajax -->
        <script defer src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!-- Bootstrap bundle contain JS -->
        <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
         <!-- Insertion du fichier js pour quelques scripts -->
        <script defer type="text/javascript" src="/js/<?php echo $GLOBAL['versionJS'];?>/scripts.js"></script>
    </div>
  </body>
</html>