        <!-- RETOUR EN HAUT PAGE PRESENTATION  -->          
        <div class="col-12 text-right mt-4 mr-2">
            <a href="#" class="text-primary cursor-alias"><i class="fas fa-caret-up"> Retour en haut</i></a>
        </div>
        <!-- START Bootstrap-Cookie-Alert -->
        <div class="alert text-center cookiealert" role="alert">
            <b>Vous aimez les cookies?</b> &#x1F36A; Nous les utilisons pour faire fonctionner le site correctement. <a class="text-warning" href="https://www.cnil.fr/fr/cookies-les-outils-pour-les-maitriser" target="_blank">En savoir plus</a>

            <button type="button" class="btn btn-primary btn-sm acceptcookies">
                Ok
            </button>
        </div>
        <!-- END Bootstrap-Cookie-Alert -->

        <div class="col-12 text-center small mt-1">
          <hr class="border border-primary">
          Version <span data-html="true" data-toggle="tooltip" data-placement="right" title="<?php echo $GLOBAL['versionCSS']; ?> | <?php echo $GLOBAL['versionJS']; ?>"><?php echo $GLOBAL['versionSITE']; ?> </span>
        </div>
        <!-- Jquery for Ajax -->
        <script defer src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!-- Bootstrap bundle contain JS -->
        <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
         <!-- Insertion du fichier js pour quelques scripts -->
        <script defer type="text/javascript" src="/js/<?php echo $GLOBAL['versionJS'];?>/scripts.js"></script>
    
      </div>
    </div>
  </body>
</html>

