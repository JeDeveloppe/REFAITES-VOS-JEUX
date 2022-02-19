      <!-- RETOUR EN HAUT PAGE PRESENTATION  -->   
      <div class="col-12 text-right mt-5 mr-2">
          <a href="#" class="text-primary cursor-alias"><i class="fas fa-caret-up"> Retour en haut</i></a>
      </div>
      <!-- START Bootstrap-Cookie-Alert -->
      <div class="col-12 cookiealert text-center p-1" role="alert">
          <div class="col-12">
            <b>Vous aimez les cookies?</b> &#x1F36A; <br />
            Nous les utilisons pour faire fonctionner le site correctement.
            <a class="text-warning" href="https://www.cnil.fr/fr/cookies-les-outils-pour-les-maitriser" target="_blank">
            En savoir plus
            </a>
          </div>
          <div class="col-12">
            <button type="button" class="btn btn-primary btn-sm acceptcookies">
              Ok
            </button>
          </div>
      </div>
      <!-- END Bootstrap-Cookie-Alert -->


      <ul class="col-11 mx-auto nav justify-content-center mt-2 bg-secondary ulFooter">
        <li class="nav-item">
          <a class="nav-link text-decoration-none text-white" href="/projet/qui-sommes-nous/">Qui sommes-nous ?</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-decoration-none text-white" href="/comment-ca-marche/tarifs/">Tarifs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-decoration-none text-white" href="/contact/">Contact</a>
        </li>
        <li class="nav-item nav-item-facebook mr-2">
          <a class="nav-link text-white" href="https://www.facebook.com/refaitesvosjeux" rel="noreferrer" target="_blank">Nous suivre sur <img class="img-facebook" src="/images/design/facebookLogo.png" alt=""></a>
        </li>
      </ul>
      <footer class="col-12 mx-auto">
        <ul class="col-10 mx-auto py-2 d-flex flex-wrap justify-content-center list-unstyled m-0">
          <li class="mx-4"><a class=" text-decoration-none" href="/mentions-legales/"> Mentions légales et CGU</a></li>
          <li class="mx-4">Site créé par: <a rel="noreferrer" href="https://www.je-developpe.fr" target="_blank" class="cursor-alias">Je-Développe</a></li>
          <li class="mx-4"><a class=" text-decoration-none" href="/conditions-generales-de-vente/"> CGV</a></li>
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
          <?php } ?>
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

