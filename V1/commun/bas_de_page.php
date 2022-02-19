      <!-- RETOUR EN HAUT PAGE PRESENTATION  -->
      <div class="col mt-3 mb-2">             
        <div class="col-12 text-right mt-4 mr-2">
            <a href="#" class="text-primary cursor-alias"><i class="fas fa-caret-up"> Retour en haut</i></a>
        </div>
        <!-- START Bootstrap-Cookie-Alert -->
        <div class="col-12 cookiealert text-center p-1" role="alert">
            <b>Vous aimez les cookies?</b> &#x1F36A; <br />Nous les utilisons pour faire fonctionner le site correctement. <a class="text-warning" href="https://www.cnil.fr/fr/cookies-les-outils-pour-les-maitriser" target="_blank">En savoir plus</a>

            <button type="button" class="btn btn-primary btn-sm acceptcookies">
                Ok
            </button>
        </div>
        <!-- END Bootstrap-Cookie-Alert -->
      </div>
      <!-- facebook SDK -->
      <div id="fb-root"></div>
      <script async crossorigin="anonymous" rel="preconnect" src="https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v9.0" nonce="tdrnOXSZ"></script>
      <!-- END facebook SDK -->
        <footer class="col-11 mx-auto bg-secondary border-0 p-2">
          <div class="col-12 d-md-flex mt-4">
            <div class="col-12 col-md-6 col-lg-8 d-flex flex-wrap p-0">
              <div class="col-6 col-lg-3 text-warning text-center text-sm-left">
                Informations
                    <ul class="list-unstyled ml-2 text-left">
                      <li><a class="text-white text-decoration-none" href="/mentions-legales/"> Mentions légales et CGU</a></li>
                      <li><a class="text-white text-decoration-none" href="/conditions-generales-de-vente/"> CGV</a></li>
                    </ul>
              </div>
              <div class="col-6 col-lg-3 text-warning text-center text-sm-left">
                Remerciements
                    <ul class="list-unstyled ml-2 text-left">
                      <li><a rel="noreferrer" class="text-white text-decoration-none cursor-alias" href="https://coop5pour100.com/" target="_blank"> La Coop 5 pour 100</a></li>
                      <li><a rel="noreferrer" class="text-white text-decoration-none cursor-alias" href="https://www.linkedin.com/in/wetta-rene" target="_blank"> WETTA René</a></li>
                    </ul>
              </div>
              <div class="col-6 col-lg-3 text-warning text-center text-sm-left">
                On en parle
                    <ul class="list-unstyled ml-2 text-left">
                      <li><a class="text-white text-decoration-none" href="/on-en-parle/medias/"> Médias</a></li>
                      <?php 
                        if($GLOBAL['versionSITE'] < 2 || isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                          echo '<li><a class="text-white text-decoration-none" href="/on-en-parle/livre-d-or/"> Livre d\'or</a></li>';
                        }
                      ?>
                    </ul>
              </div>
              <div class="col-6 col-lg-3 text-warning text-center text-sm-left">
                Utile
                    <ul class="list-unstyled ml-2 text-left">
                      <li><a class="text-white text-decoration-none" href="/contact/"> Contact</a></li>
                      <li><a class="text-white text-decoration-none" href="/bouteille-a-la-mer/"> Bouteille à la mer</a></li>
                    </ul>
              </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4 mx-auto text-center p-0">
              <div class="col-12 mx-auto m-4 m-lg-0 pb-lg-3 p-0">
                <div class="fb-page" data-href="https://www.facebook.com/refaitesvosjeux" rel="noreferrer" data-tabs="" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/refaitesvosjeux" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/refaitesvosjeux">Facebook - Refaites vos jeux</a></blockquote></div>
              </div>
            </div>
          </div>
          <div class="col-12 text-center small text-white">
            Site créé par: <a rel="noreferrer" href="https://www.je-developpe.fr" target="_blank" class="cursor-alias text-white">Je-Développe</a><br />
          </div>
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

