<!-- VOIR README -->
<?php
//liste les pages
if(preg_match('#contact#',$_SERVER['REQUEST_URI'])){
    $pageStatGoogleCapcha = "contact";
}else if(preg_match('#catalogue-pieces-detachees/#',$_SERVER['REQUEST_URI'])){
    $pageStatGoogleCapcha = "demandeJeu";  
}else if(preg_match('#accessoires/#',$_SERVER['REQUEST_URI'])){
    $pageStatGoogleCapcha = "demandeAccessoires";  
}else if(preg_match('#demande-devis#',$_SERVER['REQUEST_URI'])){
    $pageStatGoogleCapcha = "demandeDevis";  
}else if(preg_match('#livre-d-or#',$_SERVER['REQUEST_URI'])){
    $pageStatGoogleCapcha = "livreOr";  
}else if(preg_match('#bouteille#',$_SERVER['REQUEST_URI'])){
    $pageStatGoogleCapcha = "bouteilleALaMer";  
}else if(preg_match('#/inscription/#',$_SERVER['REQUEST_URI'])){
    $pageStatGoogleCapcha = "inscription";  
}else if(preg_match('#/panier/#',$_SERVER['REQUEST_URI'])){
    $pageStatGoogleCapcha = "panier";  
}
?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $GLOBAL['cleSiteGoogleCaptcha']; ?>"></script>
<script>
let siteKeyGoogle = <?php echo json_encode($GLOBAL['cleSiteGoogleCaptcha']); ?>;
let pageDuSite = <?php echo json_encode($pageStatGoogleCapcha); ?>;
    grecaptcha.ready(function() {
        grecaptcha.execute(siteKeyGoogle, {action: pageDuSite}).then(function(token) {
            document.getElementById('recaptchaResponse').value = token
        });
    });
</script>