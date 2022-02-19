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
    <link rel="stylesheet" type="text/css" href="/css/2.00/bootstrap/theme-bootstrap.css">
    <!-- Add fontawesome pictures -->
    <link rel="stylesheet" href="/fontawesome/css/all.min.css">
    <!-- CSS du site -->
    <link rel="stylesheet" type="text/css" href="/css/2.00/design.css?ts=<? echo time(); ?>">
     <!-- page travaux -->
     <link rel="stylesheet" type="text/css" href="/css/2.00/travaux.css">
    <!-- CSS des animations -->
    <link rel="stylesheet" type="text/css" href="/css/2.00/animation.css">
  
</head>
<body class="m-0 p-0">
  <?php
    $textPanier = "";
    $activationPanier = "btn disabled";
    $lienUrlPanier = "#";
  ?>
    <div class="d-flex flex-column justify-content-between min-vh-100">
      <a href="#"><img class="position-absolute logoTop" src="/images/design/refaitesvosjeux.png" alt="Refaites vos jeux"></a>
        <nav class="navbar text-right navbar-expand-md navbar-light mt-3 p-2 border-0">
          <a href="#" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Accueil du site"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarColor02">
              <div class="col-12 d-flex justify-content-end">
                <ul class="navbar-nav h6"> 
                  <li class="nav-item mx-md-1 border-bottom">
                    <a class="nav-link text-primary" href="#">Pièces détachées</a>
                  </li>
                  <li class="nav-item mx-md-1 border-bottom">
                    <a class="nav-link text-primary" href="#">Jeux d'occasion</a>
                  </li>
                  <li class="nav-item mx-md-1 border-bottom">
                    <a class="nav-link text-primary" href="#">Donner ses jeux</a>
                  </li>
                  <li class="nav-item mx-md-1 border-bottom">
                    <a class="nav-link text-primary" href="#">Nous soutenir</a>
                  </li>
                  <?php
                    if(isset($_SESSION['levelUser'])){
                      echo '<li class="nav-item dropdown mx-1 border-bottom">
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
                        echo '<li class="nav-item border-bottom mx-1" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Espace membre">
                        <a class="nav-link text-primary" href="#"><i class="fas fa-user text-vos"></i></a>
                        </li>
                        ';
                      }
                  ?>
                  <li class="nav-item mx-md-1 border-bottom mt-1 mt-md-0" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Panier">
                        <a class="nav-link text-primary position-relative <?php echo $textPanier; ?>" href="<?php echo $lienUrlPanier; ?>"><i class="fas fa-shopping-bag text-refaites <?php if($nbreDeMessage > 0){echo 'fa-box-open-scale';}?>"></i><b class="text-right"><?php echo $textPanier; ?></b></a>
                      </li>
                </ul>
              </div>
          </div>
        </nav>
        
        <?php
          include_once($_SERVER['DOCUMENT_ROOT']."/commun/alertMessage.php");
        ?>
        
      <div class="container">
        <div class="row align-items-center">
          <div class="col-12 col-lg-6 text-center" style="width:500px; height:550px; magin: auto;">
            <svg width="500" height="600">
              <!-- I found it more accurate to create a larger version and then 
              scale it down to a more usable size, and give the rotations space to do their thing -->
              <g class="puzzle_wrapper" transform="translate(170,100) scale(0.4) ">
                  <g class="puzzle">
                    <g class="wrapper blue_wrapper">
                      <polygon class="blue" points="164, 285  303,445 488,285 326,0" />
                  </g>
                    <g class="wrapper yellow_wrapper">
                      <polygon class="yellow" points="488, 565  652,565 488,285 350,405" />
                  </g>
                    <g class="wrapper green_wrapper">
                      <polygon class="green" points="164, 565  488, 565  350, 405"/>
                  </g>
                  <g class="wrapper red_wrapper">
                      <polygon class="red" points="0,565  164, 565  303,445  164, 285" />
                  </g>
                </g>
              </g>
            </svg>
          </div>
          <div class="col-12 mt-5 mt-lg-0 col-lg-6 h3">
                Réouverture du site après quelques modifications... merci de votre compréhension !
          </div>
        </div>
      </div>
                  
        <ul class="col-11 mx-auto nav justify-content-center mt-5 bg-secondary ulFooter">
          <li class="nav-item dropup">
            <a class="nav-link dropdown-toggle text-white" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">QUI SOMMES NOUS ?</a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="#">Génèse</a>
              <a class="dropdown-item" href="#">Avenir</a>
            </div>
          </li>
          <li class="nav-item dropup">
            <a class="nav-link dropdown-toggle text-white" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">NOS PARTENAIRES</a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="#">A l'origine</a>
            </div>
          </li>
          <li class="nav-item dropup">
            <a class="nav-link dropdown-toggle text-white" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">ILS PARLENT DE NOUS</a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="#">Les médias</a>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link text-decoration-none text-white" href="#">CONTACT</a>
          </li>
          <li class="nav-item nav-item-facebook mr-2">
            <a class="nav-link text-white" href="https://www.facebook.com/refaitesvosjeux" rel="noreferrer" target="_blank">Nous suivre sur <img class="img-facebook" src="/images/design/facebookLogo.png" alt=""></a>
          </li>
        </ul>
        <footer class="col-12 mx-auto">
          <ul class="col-10 mx-auto py-2 d-flex flex-wrap justify-content-center list-unstyled m-0">
            <li class="mx-4"><a class=" text-decoration-none" href="#"> Mentions légales et CGU</a></li>
            <li class="mx-4">Site créé par: <a rel="noreferrer" href="https://www.je-developpe.fr" target="_blank" class="cursor-alias">Je-Développe</a></li>
            <li class="mx-4"><a class=" text-decoration-none" href="#"> CGV</a></li>
          </ul>
        </footer>

        <?php
          //DESTRUCTION SESSION PAIEMENT PAYPLUG SI ELLE EXISTE
          if(isset($_SESSION['payment_id'])){
            unset($_SESSION['payment_id']);
          }
          //POUR SUIVI XITI UNIQUEMENT DANS LA PARTIE NON ADMINISTRATEUR
          if(!preg_match('#/admin/#',$_SERVER['REQUEST_URI'])){ 
            $url = $_SERVER['REQUEST_URI'];
            $urlPropre = str_replace(array("?","="),"_",$url);
            $Xitipage = substr($urlPropre, 1);
            ?>
            <div class="col-12 text-center">
                <a href="http://www.xiti.com/xiti.asp?s=617554" title="WebAnalytics" target="_blank">
                <script type="text/javascript">
                let Xitipage = <?php echo json_encode($Xitipage);?>;
                Xt_param = 's=617554&p='+Xitipage;
                try {Xt_r = top.document.referrer;}
                catch(e) {Xt_r = document.referrer; }
                Xt_h = new Date();
                Xt_i = '<img width="80" height="15" border="0" alt="" ';
                Xt_i += 'src="https://logv2.xiti.com/g.xiti?'+Xt_param;
                Xt_i += '&hl='+Xt_h.getHours()+'x'+Xt_h.getMinutes()+'x'+Xt_h.getSeconds();
                if(parseFloat(navigator.appVersion)>=4)
                {Xt_s=screen;Xt_i+='&r='+Xt_s.width+'x'+Xt_s.height+'x'+Xt_s.pixelDepth+'x'+Xt_s.colorDepth;}
                document.write(Xt_i+'&ref='+Xt_r.replace(/[<>"]/g, '').replace(/&/g, '$')+'" title="Audience Internet Gratuite">');
                </script>
                <noscript>
                Mesure d'audience ROI statistique webanalytics par <img width="80" height="15" src="https://logv2.xiti.com/g.xiti?s=617554&p=<?php echo $Xitipage;?>" alt="WebAnalytics" />
                </noscript></a>
            </div>
          <?php
          }
        ?>
        <div class="col-12 text-center small mt-1">
          Version <span data-html="true" data-toggle="tooltip" data-placement="right" title="<?php echo $GLOBAL['versionCSS']; ?> | <?php echo $GLOBAL['versionJS']; ?>"><?php echo $GLOBAL['versionSITE']; ?> </span>
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