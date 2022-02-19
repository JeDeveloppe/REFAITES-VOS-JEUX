<?php
@session_start ();
//SI ON EST DEJA IDENTIFIE
if(isset($_SESSION['levelUser'])){
    $_SESSION['alertMessage'] = "Déja connecté(e) !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /");
    exit();  
}
include_once("../../config.php");
$titreDeLaPage = "Connexion | ".$GLOBALS['titreDePage'];
$descriptionPage = "";
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
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/design/favicon.png" />
    <!-- Icone tactile -->
    <link rel="apple-touch-icon" href="/images/design/logoTactile.png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="/css/<?php echo $GLOBAL['versionCSS']; ?>/bootstrap/theme-bootstrap.css">
    <!-- CSS du site -->
    <link rel="stylesheet" type="text/css" href="/css/<?php echo $GLOBAL['versionCSS']; ?>/design.css?ts='.time().'">
    <!-- Add fontawesome pictures -->
    <link rel="stylesheet" href="/fontawesome/css/all.min.css">

   
    <?php
        echo '
        <!-- CSS ADMIN du site -->
        <link rel="stylesheet" type="text/css" href="/css/'.$GLOBAL['versionCSS'].'/design-admin.css?ts='.time().'">
        <!-- CSS IMPRESSION -->
        <link rel="stylesheet" type="text/css" href="/css/'.$GLOBAL['versionCSS'].'/impression.css" media="print">';
    ?>
</head>
<body class="m-0 p-0">
  <?php
    $textPanier = "";
    $activationPanier = "btn disabled";
    $lienUrlPanier = "#";
    ?>
    <div class="d-flex flex-column justify-content-around min-vh-100">
      <a href="/accueil/"><img class="position-absolute logoTop" src="/images/design/refaitesvosjeux.png" alt="Refaites vos jeux"></a>
      <div class="container-fluid mt-5 pt-5 vh-100">
          <div class="row mt-5">
              <div class="col-xl-6 mx-auto">
                  <div class="card col p-0 mt-3 m-1">
                      <div class="card-header bg-dark text-white"><i class="fas fa-sign-in-alt"></i> Connexion [ADMIN]</div>
                      <div class="card-body">
                          <p class="card-text">
                              <form class="form-signin" action="/administration/connexion/ctrl/ctrl-connexion.php" method="get">
                                  <div class="form-group">
                                      <input type="email" class="form-control" id="exampleInputEmail1" name="mail" aria-describedby="emailHelp" placeholder="Votre adresse mail..." require>
                                  </div>
                                  <div class="form-group">
                                      <input type="password" class="form-control" id="exampleInputPassword" name="passwordUser" aria-describedby="passwordHelp" placeholder="Votre mot de passe..." require>
                                  </div>
                                  <button class="btn btn-primary" type="submit">Connexion</button>
                              </form>
                          </p>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
</body>
</html>