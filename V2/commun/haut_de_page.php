<?php
@session_start ();
require($_SERVER["DOCUMENT_ROOT"]."/config.php");
require_once($_SERVER["DOCUMENT_ROOT"].'/controles/fonctions/modeTravauxOn.php');
require($_SERVER["DOCUMENT_ROOT"]."/bdd/connexion-bdd.php");
//require($_SERVER["DOCUMENT_ROOT"]."/controles/fonctions/validation_donnees.php");
//require($_SERVER["DOCUMENT_ROOT"]."/sitemap/sitemap.generateur.php");
require($_SERVER["DOCUMENT_ROOT"]."/bdd/table_config.php");

if(!isset($_SESSION['sessionId'])){
    function random_strings($length_of_string) 
    { 
        // String of all alphanumeric character 
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@'; 
    
        // Shufle the $str_result and returns substring 
        // of specified length 
        return substr(str_shuffle($str_result),0, $length_of_string); 
    } 
    //on creer une clefUnique tant que cela n'existe pas dans la base
    do{
        $validKey = random_strings(64);
        $verifValidKey = $bdd -> prepare("SELECT idUser FROM listeMessages WHERE idUser = ? ");
        $verifValidKey->execute(array($validKey));
        $donneesValidKey = $verifValidKey->rowCount();
    }
    while($donneesValidKey = 0);
    $_SESSION['sessionId'] = $validKey;
}

//pour annuler animation une fois sortie du menu membre
if(!preg_match('#/membre/#',$_SERVER['REQUEST_URI']) && isset($_SESSION['animationMenuMembreBonjour'])){
  unset($_SESSION['animationMenuMembreBonjour']);
}
?>
<!DOCTYPE html>
<html lang="fr" class="m-0 p-0">
  <head>
    <?php
    //SI PAS DANS LA SESSION ADMINISTRATEUR
    if(!preg_match('#/admin/#',$_SERVER['REQUEST_URI'])){
    //suivi google analytics et autre (pub)
    require($_SERVER['DOCUMENT_ROOT']."/google-analytics.php");
    //META FACEBOOK
    require($_SERVER['DOCUMENT_ROOT']."/commun/meta-og.php");
    }
    ?>
    <title><?php echo $titreDeLaPage ?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0">
    <meta name="description" content="<?php echo $descriptionPage; ?>">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/design/favicon.png" />
    <!-- Icone tactile -->
    <link rel="apple-touch-icon" href="/images/design/logoTactile.png" />
    <!-- Add fontawesome pictures -->
    <!-- <script src="https://kit.fontawesome.com/440b1b0c24.js" crossorigin="anonymous"></script> -->
    <link rel="stylesheet" href="/fontawesome/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="/css/<?php echo $GLOBAL['versionCSS']; ?>/bootstrap/theme-bootstrap.css">
 

   
    <?php
    //SI PAS DANS LA SESSION ADMINISTRATEUR
    if(!preg_match('#/admin/#',$_SERVER['REQUEST_URI'])){
      echo '
      <!-- CSS du site -->
      <link rel="stylesheet" type="text/css" href="/css/'.$GLOBAL['versionCSS'].'/design.css?ts='.time().'">
      <!-- CSS des animations -->
      <link rel="stylesheet" type="text/css" href="/css/'.$GLOBAL['versionCSS'].'/animation.css">
      <!-- ODOMETRE accueil -->
      <link rel="stylesheet" href="/css/'.$GLOBAL['versionCSS'].'/odometre/odometre.css" />';
      }else{
        echo '
        <!-- CSS ADMIN du site -->
        <link rel="stylesheet" type="text/css" href="/css/'.$GLOBAL['versionCSS'].'/design-admin.css?ts='.time().'">
        <!-- CSS IMPRESSION -->
        <link rel="stylesheet" type="text/css" href="/css/'.$GLOBAL['versionCSS'].'/impression.css" media="print">';
      }
    ?>
</head>
<body class="m-0 p-0">
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v12.0" nonce="DUztskPr"></script>
  <?php
    //SI PAS DANS LA SESSION ADMINISTRATEUR
    if(!preg_match('#/admin#',$_SERVER['REQUEST_URI'])){
      require_once($_SERVER['DOCUMENT_ROOT']."/bdd/connexion-bdd.php");
        $sqlListeMessagesHtDePage = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND statut = 0");
        $sqlListeMessagesHtDePage-> execute(array($_SESSION['sessionId']));
        $nbreDeMessage = $sqlListeMessagesHtDePage ->rowCount();
        if($nbreDeMessage > 0){
          $textPanier = $nbreDeMessage;
          $activationPanier = "";
          $lienUrlPanier = "/panier/";
        }else{
          $textPanier = "";
          $activationPanier = "btn disabled";
          $lienUrlPanier = "#";
        }
    ?>
    <div class="d-flex flex-column justify-content-around min-vh-100">
      <a href="/accueil/"><img class="position-absolute logoTop img-thumbnail border-secondary" src="/images/design/refaitesvosjeux.png" alt="Refaites vos jeux"></a>
        <nav class="navbar text-right navbar-expand-md navbar-light mt-4 py-2 bg-vos border-0">
          <a href="/accueil/" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Accueil du site"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarColor02">
              <div class="col-12 d-flex justify-content-end">
                <ul class="navbar-nav font-weight-bold"> 
                  <li class="nav-item mx-md-1">
                    <a class="nav-link" href="/catalogue-pieces-detachees/">Pièces détachées</a>
                  </li>
                  <li class="nav-item mx-md-1">
                    <a class="nav-link" href="/catalogue-jeux-occasion/">Jeux d'occasion</a>
                  </li>
                  <li class="nav-item mx-md-1">
                    <a class="nav-link" href="/don-de-jeux/partenaires/france/">Donner ses jeux</a>
                  </li>
                  <li class="nav-item mx-md-1">
                    <a class="nav-link" href="/nous-soutenir/">Nous soutenir</a>
                  </li>
                  <?php
                    if(isset($_SESSION['levelUser'])){
                      echo '<li class="nav-item dropdown mx-1">
                          <a class="nav-link text-primary dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user text-success"></i></a>
                          <div class="dropdown-menu dropdown-menu-right">
                              <a class="dropdown-item" href="/membre/dashboard/">Mon espace</a>';
                              if($_SESSION['levelUser'] == 4){
                                echo '<a class="dropdown-item" href="/admin/accueil/"><i class="fas fa-user-cog"> Admin</i></a>';
                              }
                              echo '<a class="dropdown-item" href="/logout/"><i class="fas fa-hourglass-end text-danger"></i> Déconnexion</a>
                          </div>
                        </li>';
                      }else{
                        echo '<li class="nav-item mx-1" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Espace membre">
                        <a class="nav-link text-primary" href="/connexion/"><i class="fas fa-user text-jeux"></i></a>
                        </li>
                        ';
                      }
                  ?>
                  <li class="nav-item mx-md-1 mt-1 mt-md-0" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Panier">
                        <a class="nav-link text-primary position-relative <?php echo $textPanier; ?>" href="<?php echo $lienUrlPanier; ?>"><i class="fas fa-shopping-bag text-refaites<?php if($nbreDeMessage > 0){echo 'fa-box-open-scale';}?>"></i><b class="text-right"><?php echo $textPanier; ?></b></a>
                      </li>
                </ul>
              </div>
          </div>
        </nav>
        <?php
        //message de pause, vacances ou autre...
        if($donneesConfig[27]['valeur'] != "?"){
          echo '<div class="marquee-rtl col-10 col-md-7 mx-auto overflow-hidden mt-2 mb-n2">
                    <div><i class="fas fa-info-circle text-info"></i> '.$donneesConfig[27]['valeur'].'</div>
                  </div>';
        }
   }//ON EST DANS LA PARTIE ADMINISTRATEUR
    else{
      //on inclus le menu administration
      require($_SERVER['DOCUMENT_ROOT']."/administration/menuAdmin.php");
    } //FIN DE LA PARTIE ADMINISTRATEUR
    ?>