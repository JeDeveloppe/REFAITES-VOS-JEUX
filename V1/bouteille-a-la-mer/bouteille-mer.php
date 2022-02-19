<?php
@session_start ();
require("../captcha/captcha.php");
include_once("../config.php");
$titreDeLaPage = "Bouteille à la mer | ".$GLOBALS['titreDePage'];
$descriptionPage = "Vous ne trouvez pas votre jeu dans le catalogue ? Le service a des arrivages régulier, déposez une bouteille à la mer pour être prévenu si votre jeu rentre dans notre catalogue !";
require('../commun/haut_de_page.php');
require('../commun/alertMessage.php');

require("../bdd/connexion-bdd.php");
$sqlBouteille = $bdd -> query("SELECT * FROM bouteille_mer");
$nbrBouteille = $sqlBouteille->rowCount();
?>
<div class="container-fluid pt-4">
    <div class="row">
        <div class="card col-11 col-md-8 col-lg-6 p-0 mx-auto mt-5">
            <div class="card-header bg-dark text-white text-center"><h1>Bouteille à la mer <i class="fas fa-water text-info"></i></h1></div>
            <div class="card-body">
            <p class="col-10 mx-auto text-justify text-danger my-3">
            Ce formulaire sert à être averti si le jeu que vous cherchez se voit un jour ajouté au catalogue !
            </p>
                <form method="post" action="/bouteille-a-la-mer/ctrl/ctrl-bouteille-mer.php" class="col-11 col-sm-8 mx-auto">
                    <div class="form-group text-center">
                        <label for="nomDuJeu">Nom du jeu:</label>
                        <input type="text" class="form-control" name="nom" id="nomDuJeu" placeholder="Visible sur la boite" pattern="[A-Z0-9 ]{4,30}" value="<?php if(isset($_SESSION['nom'])){echo $_SESSION['nom'];}?>" required>
                        <small class="text-danger">Entre 4 et 30 caractères en MAJUSCULE et sans caractères spéciaux... </small>
                    </div>
                    <div class="form-group text-center">
                        <label for="nomEditeur">Nom de l'éditeur:</label>
                        <input type="text" class="form-control" name="editeur" id="nomEditeur" placeholder="Visible sur la boite, sinon mettre ?" pattern="[A-Z0-9 ?]{1,30}" value="<?php if(isset($_SESSION['editeur'])){echo $_SESSION['editeur'];}?>" required>
                        <small class="text-danger">En MAJUSCULE, jusqu'à 30 caractères ou seul le caractère: ? </small>
                    </div>
                    <div class="form-group text-center">
                        <label for="nomEditeur">Année du jeu:</label>
                        <input type="text" class="form-control" name="annee" id="anneeJeu" placeholder="Visible sur la boite, sinon mettre ?" pattern="[0-9?]{1,4}" value="<?php if(isset($_SESSION['annee'])){echo $_SESSION['annee'];}?>" required>
                        <small class="text-danger">4 chiffres ou seul le caractère: ? </small>
                    </div>
                    <div class="form-group text-center">
                        <label for="adresseMail">Votre adresse mail:</label>
                        <input type="email" class="form-control" name="email" id="adresseMail" placeholder="Une adresse valide pour vous prévenir..." value="<?php if(isset($_SESSION['email'])){echo $_SESSION['email'];}?>" required>
                    </div>
                    <div class="col text-center mb-3">
                        <input id="lu" type="checkbox" name="conditionBienOk" value="lu" required> <label for="lu">J'ai lu et j'accepte les</label> <a href="/mentions-legales/#cgu">conditions générales d'utilisation</a>.<sup>(1)</sup>
                    </div>

                    <div class="col text-center">
                        <input type="hidden" id="recaptchaResponse" name="recaptcha-response">
                        <button type="submit" class="btn btn-success"><i class="fas fa-wine-bottle"></i> Jeter</button>
                    </div>
                    <div class="col text-danger text-left mt-3 mt-sm-0">
                        <sup>(1)</sup> Obligatoire.
                    </div>
                </form>

            </div> 
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-11 mx-auto text-center">
            <div class="col-12 h3 p-0">Nombre de bouteilles déja lancées : <div id="odometerbouteilleLancee" class="odometer"></div></div>
        </div>
    </div>
</div>
<script>
/*
 * ODOMETRE
 */
let bouteilleLancee = <?php echo json_decode($nbrBouteille); ?>;

if(bouteilleLancee < 10){
    odometerbouteilleLancee.innerHTML = 4;
}else if(bouteilleLancee > 9 && bouteilleLancee < 100){
    odometerbouteilleLancee.innerHTML = 31;
}else if(bouteilleLancee > 99 && bouteilleLancee < 1000){
    odometerbouteilleLancee.innerHTML = 300;
}else if(bouteilleLancee > 999 && bouteilleLancee < 10000){
    odometerbouteilleLancee.innerHTML = 1983;
}else if(bouteilleLancee > 9999 && bouteilleLancee < 100000){
    odometerbouteilleLancee.innerHTML = 22220;
}
setTimeout(function(){
    odometerbouteilleLancee.innerHTML = bouteilleLancee;
}, 2500);
</script>
<script src="/js/<?php echo $GLOBAL['versionJS'];?>/odometre.js"></script>
<?php
require("../captcha/captchaGoogle.php");
include_once("../commun/bas_de_page.php");
?>