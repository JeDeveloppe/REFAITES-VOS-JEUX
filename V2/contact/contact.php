<?php
@session_start ();
require("../captcha/captcha.php");
include_once("../config.php");
$titreDeLaPage = "Contactez- moi sur différent sujet: partenariat, presse... | ".$GLOBALS['titreDePage'];
$descriptionPage = "Si vous avez la moindre question sur le site, une demande de partenariat ou autre, n'hésitez pas !";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
include_once("../bdd/connexion-bdd.php");
include_once("../bdd/table_config.php");

//DONNEES DU SELECT CONTENU DANS $SELECT
$SELECT = $donneesConfig[1]['valeur'];



$infos = explode(",", $SELECT); //on sépare les mots par les virgules
$nbr_resultat = count($infos);
$sujets = array();

for($i = 0; $i < $nbr_resultat; $i++){
    array_push($sujets, $infos[$i]); 
}
?>

<div class="container-fluid mt-5">
    <div class="card col-11 col-sm-10 col-md-8 col-lg-6 p-0 mx-auto mt-5">
        <div class="card-header bg-dark text-white text-center">Contacter le service</div>
        <div class="card-body">
        <p class="col text-center text-danger small my-3">
        Ce formulaire ne peut être utilisé pour formuler des demandes de pièces. Tous les jeux disponibles sont référencés dans le catalogue.
        </p>
   
            <form method="post" action="/contact/ctrl/ctrl-formContact.php">
                <div class="form-group text-center col-10 col-lg-7 mx-auto">
                    <label for="exampleFormControlInput1">Votre adresse mail:</label>
                    <input type="email" class="form-control" name="email" placeholder="Une adresse valide..." required>
                </div>

                <div class="form-group text-center">
                    <label for="exampleFormControlSelect2">Sujet de votre demande:</label><br />
                    <select class="custom-select col-10 col-lg-7" name="objet" required>
                        <option value="">...</option>
                        <?php
                            foreach($sujets as $sujet){
                                echo '<option value="'.$sujet.'">'.$sujet.'</option>';
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group col-11 col-lg-8 mx-auto">
                    <label for="exampleFormControlTextarea1">Votre demande:</label>
                    <textarea class="form-control" rows="3" name="content" placeholder="Minimum 30 caractères..." minlength="30" required></textarea>
                </div>
                <div class="col text-center">
                    <input type="hidden" id="recaptchaResponse" name="recaptcha-response">
                    <button type="submit" class="btn btn-success">Envoyer</button>
                </div>
            </form>

        </div> 
    </div>
</div>
<?php
require("../captcha/captchaGoogle.php");
require("../commun/bas_de_page.php");
?>
