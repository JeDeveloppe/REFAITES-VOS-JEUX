<?php
@session_start ();
//mode travaux
require($_SERVER["DOCUMENT_ROOT"]."/config.php");
require_once($_SERVER["DOCUMENT_ROOT"].'/controles/fonctions/modeTravauxOn.php');
require($_SERVER["DOCUMENT_ROOT"]."/bdd/connexion-bdd.php");
//require($_SERVER["DOCUMENT_ROOT"]."/controles/fonctions/validation_donnees.php");
require($_SERVER["DOCUMENT_ROOT"]."/sitemap/sitemap.generateur.php");
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
        $verifValidKey = $bdd -> prepare("SELECT * FROM clients WHERE idUser = ? ");
        $verifValidKey-> execute(array($validKey));
        $donneesValidKey = $verifValidKey -> fetch();
    }
    while($donneesValidKey == 1);
    $_SESSION['sessionId'] = $validKey;
}
?>
<!doctype html>
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
    <?php
    //SI ON EST DANS UNE SESSION DE PAIEMENT
    if(preg_match('#/PAIEMENT/#',$_SERVER['REQUEST_URI'])){
      //UNIQUEMENT POUR BANQUE POSTALE
      if(preg_match('#/PAIEMENT/BANQUEPOSTALE/#',$_SERVER['REQUEST_URI'])){
        require($_SERVER['DOCUMENT_ROOT']."/PAIEMENT/BANQUEPOSTALE/include_header.php");
        }
    }
    ?>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/design/favicon.png" />
    <!-- Icone tactile -->
    <link rel="apple-touch-icon" href="/images/design/logoTactile.png" />
    <?php
    //Timeline CSS -->
    if(preg_match('#/medias/#',$_SERVER['REQUEST_URI'])){
      echo '<link rel="stylesheet" href="/css/'.$GLOBAL['versionCSS'].'/timeline/timeline.min.css">';
    }
    ?>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="/css/<?php echo $GLOBAL['versionCSS']; ?>/bootstrap/theme-bootstrap.css">
    <!-- Add fontawesome pictures -->
    <link rel="stylesheet" href="/fontawesome/css/all.min.css">
    <?php
    //SI PAS DANS LA SESSION ADMINISTRATEUR
    if(!preg_match('#/admin/#',$_SERVER['REQUEST_URI'])){
      echo '
      <!-- CSS du site -->
      <link rel="stylesheet" type="text/css" href="/css/'.$GLOBAL['versionCSS'].'/design.css?ts='.time().'">';
        if(!preg_match('#/catalogue/#',$_SERVER['REQUEST_URI'])){
          echo '
          <!-- CSS des animations -->
          <link rel="stylesheet" type="text/css" href="/css/'.$GLOBAL['versionCSS'].'/animation.css">
          <!-- ODOMETRE accueil -->
          <link rel="stylesheet" href="/css/'.$GLOBAL['versionCSS'].'/odometre/odometre.css" />';
        }
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
<?php
  if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
    echo '<div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v12.0" nonce="DUztskPr"></script>';
  }
?>
<?php
  //SI PAS DANS LA SESSION ADMINISTRATEUR
  if(!preg_match('#/admin#',$_SERVER['REQUEST_URI'])){?>
  <div class="d-flex flex-column justify-content-between min-vh-100">
    <a href="/accueil/"><img class="img-thumbnail position-absolute logoTop border border-primary" src="/images/design/refaitesvosjeux.png" alt="Refaites vos jeux"></a>
        <nav class="navbar text-right navbar-expand-lg navbar-light bg-vos mt-3 p-2 border-0">
        <a href="/accueil/" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Accueil du site"></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarColor02">
            <div class="col pl-5">
              <ul class="navbar-nav ml-4">
                <li class="nav-item dropdown ml-5">
                  <a class="nav-link text-primary dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Catalogues</a>
                  <div class="dropdown-menu">
                      <a class="dropdown-item" href="/catalogue/">Les jeux incomplets</a>
                      <?php
                        if($GLOBAL['versionSITE'] >= 2 || isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                          echo '<a class="dropdown-item" href="/catalogue-des-jeux-complets/">Les jeux complets</a>';
                        }
                      ?>
                      <!-- <a class="dropdown-item" href="/accessoires/">Accessoires de jeux</a> -->
                  </div>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link text-primary dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Le projet</a>
                  <div class="dropdown-menu">
                      <a class="dropdown-item" href="/projet/la-genese/">Genèse</a>
                      <a class="dropdown-item" href="/projet/avenir/">Avenir du projet</a>
                  </div>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link text-primary dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Comment ça marche ?</a>
                  <div class="dropdown-menu">
                      <a class="dropdown-item" href="/comment-ca-marche/passez-une-commande/">Passer une commande</a>
                      <a class="dropdown-item" href="/comment-ca-marche/expedition-retrait/">Expédition et retrait</a>
                      <a class="dropdown-item" href="/comment-ca-marche/tarifs/">Tarifs</a>
                  </div>
                </li>
                <?php
                  //SI VERSION 2 ou ADMIN CONNECTE
                  if($GLOBAL['versionSITE'] >= 2 || isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                    if(isset($_SESSION['levelUser'])){
                      echo '<li class="nav-item dropdown">
                        <a class="nav-link text-primary dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Mon compte</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">Lien</a>';
                            if($_SESSION['levelUser'] == 4){
                              echo '<a class="dropdown-item" href="/admin/accueil/"><i class="fas fa-user-cog"> Admin</i></a>';
                            }
                            echo '<a class="dropdown-item" href="/logout/"><i class="fas fa-power-off text-danger"></i> Déconnexion</a>
                        </div>
                      </li>';
                    }else{
                      echo '<li class="nav-item">
                      <a class="nav-link text-primary" href="/connexion-inscription/"> Connexion</a>
                      </li>
                      <li class="nav-item">
                      <a class="nav-link text-primary" href="/connexion-inscription/#inscription"> Inscription</a>
                      </li>';
                    }
                  }
                ?>
              </ul>
            </div>
            <div class="text-right">
              <ul class="navbar-nav text-right"> 
                <li class="nav-item pr-3 pr-lg-0">
                  <a class="nav-link text-primary" href="/partenaires/">Partenaires</a>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link text-primary dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Don de jeux</a>
                  <div class="dropdown-menu">
                      <a class="dropdown-item" href="/don-de-jeux/">Comment ?</a>
                      <!-- <a class="dropdown-item" href="/don-de-jeux/les-demandes/">Les demandes</a> -->
                  </div>
                </li>
      
                  <?php
                    require_once($_SERVER['DOCUMENT_ROOT']."/bdd/connexion-bdd.php");
                    $sqlListeMessagesHtDePage = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND qte IS NULL");
                    $sqlListeMessagesHtDePage-> execute(array($_SESSION['sessionId']));
                    $donneesListeMessagesHtDePage  = $sqlListeMessagesHtDePage ->fetch();
                    $nbreDeMessage = $sqlListeMessagesHtDePage ->rowCount();

                    if($nbreDeMessage == 0){
                      $link = "#";
                      $titleLink = "(vide)";
                    }else{
                      $link = "/demande-devis/";
                      $titleLink = "Voir...";
                    }

                  if($GLOBAL['versionSITE'] >= 2 || isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                    $sqlListeAchats = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND qte > ?");
                    $sqlListeAchats-> execute(array($_SESSION['sessionId'],0));
                    $donneesListeAchats  = $sqlListeAchats ->fetch();
                    $nbreAchats = $sqlListeAchats ->rowCount();

                    if($nbreAchats == 0){
                      $linkAchat = "#";
                      $titleLinkAchat = "(vide)";
                    }else{
                      $linkAchat = "/achats/";
                      $titleLinkAchat = "Voir...";
                    }
                  ?>
                  <li class="nav-item dropdown">
                    <a class="nav-link text-primary dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Panier et demandes</a>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="<?php echo $linkAchat; ?>" data-html="true" data-toggle="tooltip" data-placement="left" title="<?php echo $titleLinkAchat; ?>">Mon panier (<?php echo $nbreAchats; ?>)</a>
                      <a class="dropdown-item" href="<?php echo $link; ?>" data-html="true" data-toggle="tooltip" data-placement="left" title="<?php echo $titleLink; ?>">Mes demandes (<?php echo $nbreDeMessage; ?>)</a>
                    </div>           
                  </li>
                  <?php
                  }
                  else{
                    echo '<li class="nav-item text-primary pr-3 pr-lg-0">
                    <a class="nav-link text-primary" href="'.$link.'" data-html="true" data-toggle="tooltip" data-placement="bottom" title="'.$titleLink.'">Mes demandes ('.$nbreDeMessage.')</a>
                    </li>';
                  }
                  ?>
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
        if($nbreDeMessage != 0 && !preg_match('#/demande-devis/#',$_SERVER['REQUEST_URI'])){
          echo '<div class="position-fixed col-11 mx-auto text-right d-flex justify-content-end" style="z-index:1; top:12%; right:0%">
                <div class="jumbotron bg-vos p-1">
                  <div class="col-12 text-center"><a class="text-decoration-none" href="/demande-devis/"><i class="fas fa-list-alt fa-2x text-info"></i></a></div>
                </div>
                </div>';
        }
   }//ON EST DANS LA PARTIE ADMINISTRATEUR
  else{ ?>
      <div class="d-flex flex-column flex-lg-row min-vh-100">
        <!-- partie du menu -->
        <div class="col-12 col-lg-2 p-0 bg-info border-right border-primary">
          <?php 
          require($_SERVER['DOCUMENT_ROOT']."/administration/menuAdmin.php");
          ?>
        </div>
        <!-- partie CORPS admin -->
        <div class="col-12 col-lg-10 bg-vos p-0"> 
  <?php 
  } //FIN DE LA PARTIE ADMINISTRATEUR
  ?>