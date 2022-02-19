<?php
@session_start ();
include_once("../config.php");
if($GLOBAL['versionSITE'] >= 2){
    require_once("../controles/fonctions/memberOnline.php");
}
// utilisateur non loggé
$titreDeLaPage = "Liste de demande(s) | ".$GLOBALS['titreDePage'];
$descriptionPage = "Votre liste de demande de pièce pour compléter vos jeux !";
include_once("../bdd/connexion-bdd.php");
require("../bdd/table_config.php");

$sqlListeMessages = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ?");
$sqlListeMessages-> execute(array($_SESSION['sessionId']));
$donneesListeMessages = $sqlListeMessages->fetch();
$countDemandes = $sqlListeMessages -> rowCount();

$sqlOptgroup = $bdd -> prepare("SELECT * FROM pays WHERE actif = 1 AND rattachement = ? ORDER BY nom_fr_fr ASC");
$sqlOptgroup-> execute(array("FR-"));
$donneesOptGroup = $sqlOptgroup-> fetch();

$sqlPays = $bdd-> prepare("SELECT * FROM pays WHERE actif = 1 AND rattachement = ? ORDER BY nom_fr_fr ASC");
$sqlPays-> execute(array("NONE"));
$donneesPays = $sqlPays-> fetchAll();

//si y a plus rien dans la table on retourne au catalogue
if($countDemandes < 1){
    $_SESSION['alertMessage'] = "La liste des demandes est vide !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /catalogue/");
    exit();
}

//SI CAPTCHA GOOGLE NE MARCHE PAS
//require("./captcha/captcha.php");
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
?>

<script type="text/javascript">
    function deleteExpedition(){
        $('input[name=expeditionOption]').prop('checked', false);
        $('input[name=expeditionOption]').prop('required', false);
    }

    function checkExpedition(){
        $('input[name=expeditionOption]').prop('required', true);
    }

    function checkExpeditionOption1(){
        document.getElementById("envoi").click();
    }
    function checkExpeditionOption2(){
        document.getElementById("envoi").click();
    }
</script>

<div class="container-fluid">
    <div class="col h1 text-center mt-4">Mes demandes</div>
    <div class="col h5 text-center text-danger animated faster fadeInRight">
        <i class="fas fa-rss"></i> Pensez à regrouper vos demandes !<br />
        Le forfait de base (<?php echo str_replace(".",",",$donneesConfig[5]['valeur']);?> €) n'est compté qu'une fois lorsque vous commandez des pièces pour plusieurs jeux.
    </div>
    <!-- TABLE DES DEMANDES -->
    <div class="row mt-2 overflow-auto">
        <table class="table table-sm table-striped mt-4 col-11 mx-auto">
            <thead class="thead-dark text-center">
                <tr>
                    <th scope="col">Action</th>
                    <th scope="col">Image</th>
                    <th scope="col">Jeu / Accessoire</th>
                    <th scope="col">Demande</th>
                    <th scope="col">Image(s) d'exemple</th>
                </tr>
            </thead>
            <tbody>
            <?php
                while($donneesListeMessages){

                    //on recupere tout de la boite de jeu
                    $sqlJeux = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = ".$donneesListeMessages['idJeu']);
                    $donneesJeux = $sqlJeux -> fetch();
                    //on cherche l'image du jeu
                    $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$donneesJeux['idCatalogue']);
                    $donneesImage = $sqlImage->fetch();
                    //on recupere les images d'exemple s'il y en a
                    $sqlImageExemple = $bdd -> query("SELECT * FROM listeMessages_images WHERE idListeMessages = ".$donneesListeMessages['idListeMessages']);
                    $countImageExemple = $sqlImageExemple->rowCount();

                    if($countImageExemple < 1){
                        $textImageExemple = '<div class="text-danger">Non fournie</div>';
                    }elseif($countImageExemple == 1){
                        $textImageExemple = '<div class="text-success">Une fournie</div>';
                    }else{
                        $textImageExemple = '<div class="text-success">2 fournies</div>';
                    }
                    ?>
                    <tr>
                    <td class="text-center align-middle">
                        <a href="/demande-devis/delete/<?php echo $donneesListeMessages['idListeMessages'];?>/" class="btn btn-danger p-1"><i class="fas fa-trash-alt" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Supprimer de la liste"></i></a>
                    </td>
                    <td class="text-center align-middle"><?php echo '<div class="divImgTableauListeMessage"><img class="img-thumbnail" src="data:image/jpeg;base64,'.$donneesImage['image'].'"/></div>'; ?></td>
                    <td class="text-center align-middle"><?php echo $donneesJeux['nom']."<br/>".$donneesJeux['editeur']."<br/>".$donneesJeux['annee']; ?></td>
                    <td class="text-center align-middle"><?php echo $donneesListeMessages['message']; ?></td>
                    <td class="text-center align-middle"><?php echo $textImageExemple; ?></td>
                    </tr>
                <?php
                $donneesListeMessages = $sqlListeMessages->fetch();
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- PARTIE FORMULAIRE -->
    <div class="row mt-4">
        <form class="col" method="post" action="/panier/ctrl/ctrl-panier.php" name="contactretrait">
            <div class="col d-flex flex-column flex-lg-row">
                <!-- CONTACT -->
                <div class="col col-sm-10 col-md-9 mx-auto col-lg-6 m-0">
                    <div class="col h4 text-center">Contact:</div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="">Nom:</span>
                        </div>
                        <input type="text" name="nom" class="form-control" <?php if(isset($_SESSION['nom'])){echo 'value="'.$_SESSION['nom'].'"';}?> pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlength="30" placeholder="Nom" required>
                        <div class="input-group-prepend">
                            <span class="input-group-text border-left-0" id="">Prénom:</span>
                        </div>
                        <input type="text" name="prenom" class="form-control" <?php if(isset($_SESSION['prenom'])){echo 'value="'.$_SESSION['prenom'].'"';}?> pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlength="30" placeholder="Prénom" required>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Adresse</span>
                        </div>
                        <input type="text" name="adresse" class="form-control" placeholder="Numéro et rue" aria-label="Username" aria-describedby="basic-addon1" <?php if(isset($_SESSION['adresse'])){echo 'value="'.$_SESSION['adresse'].'"';}?> required>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="">Code postale:</span>
                        </div>
                        <input type="text" id="codePostale" name="cp" class="form-control col-2" <?php if(isset($_SESSION['cp'])){echo 'value="'.$_SESSION['cp'].'"';}?> pattern="[a-zA-Z0-9 -]{4,11}" placeholder="CP" maxlength="11" required>
                        <div class="input-group-prepend">
                            <span class="input-group-text border-left-0" id="">Ville:</span>
                        </div>
                        <input type="text" name="ville" class="form-control" <?php if(isset($_SESSION['ville'])){echo 'value="'.$_SESSION['ville'].'"';}?> pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlenght="30" placeholder="Ville" required>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Pays</span>
                        </div>
                        <select name="pays" id="choixPays" class="custom-select" required>
                            <option value="">Destination...</option>
                            <option value="FR">France Métropolitaine</option>
                            <optgroup label="DOM- TOM">
                                <?php
                                while($donneesOptGroup){
                                    echo '<option value="'.$donneesOptGroup['alpha2'].'">'.$donneesOptGroup['nom_fr_fr'].'</option>';
                                $donneesOptGroup = $sqlOptgroup-> fetch();
                                }
                                ?>
                            </optgroup>
                        <?php                        
                            foreach($donneesPays as $pays){
                                echo '<option value="'.$pays['alpha2'].'"';
                                    if(isset($_SESSION['pays']) AND $_SESSION['pays'] == $pays['nom_fr_fr']){
                                        echo 'selected';
                                    }
                                echo '>'.$pays['nom_fr_fr'].'</option>';
                            }
                        ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">@</span>
                        </div>
                        <input type="email" name="email" class="form-control" placeholder="Adresse email valide merci !" aria-label="Username" aria-describedby="basic-addon1" <?php if(isset($_SESSION['email'])){echo 'value="'.$_SESSION['email'].'"';}?> required>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-phone-square-alt"></i></span>
                        </div>
                        <input type="text" name="telephone" class="form-control" placeholder="Format: 0000000000" pattern="[0-9]{10,14}" aria-label="Username" aria-describedby="basic-addon1" minlength="10" maxlength="14" <?php if(isset($_SESSION['telephone'])){echo 'value="'.$_SESSION['telephone'].'"';}?> required>
                    </div>
                </div>
                <!-- EXPEDITION / RETRAIT -->
                <div class="col col-sm-10 col-md-9 mx-auto mt-3 mt-lg-0 col-lg-6">
                    <div class="col h4 text-center">Expédition / retrait:</div>
                    <ul class="list-group">
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row ml-2">
                                        <div class="col-1"><input type="radio" id="envoi" name="port" value="expedition" onclick="checkExpedition()" checked="" required=""></div>
                                        <div class="col-11"><label for="envoi">Je souhaite un envoi à mon domicile.</label></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="row ml-4">
                                        <div class="col-12">Pour les pièces dont l’épaisseur est supérieure à 3 cm je choisis l'option:</div>
                                        <div class="col-12 mt-2">
                                            <div class="row ml-4">
                                                <div class="col-1"><input type="radio" id="mondialrelay" name="expeditionOption" value="mondialrelay" onclick="checkExpeditionOption1()" checked=""></div>
                                                <div class="col-11"><label for="mondialrelay">Je souhaite l'envoi par Mondial Relay (retrait dans le point relais de mon choix - à partir de <?php echo str_replace(".",",",$donneesConfig[23]['valeur']);?>€).</label></div>
                                            </div>            
                                        </div>
                                    </div>
                                    <div class="row ml-4">
                                        <div class="col-12">
                                            <div class="row ml-4">
                                                <div class="col-1"><input type="radio" id="colissimo" name="expeditionOption" value="colissimo" onclick="checkExpeditionOption2()"></div>
                                                <div class="col-11"><label for="colissimo">Je souhaite l'envoi par Colissimo (livré directement à mon domicile - à partir de <?php echo str_replace(".",",",$donneesConfig[22]['valeur']);?>€).</label></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="row ml-2">
                                        <div class="col-1"><input type="radio" id="retrait" name="port" value="retrait_caen1" onclick="deleteExpedition()"></div>
                                        <div class="col-11"><label for="retrait">Je souhaite retirer ma commande à La Coop 5 pour 100, 33 route de Trouville, 14 000 Caen (pas de frais de port - prévoir un délai de maximum 7 jours).</label></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </ul>
                </div>
            </div>
            <!-- CAPTCHA ET BOUTONS -->
            <div class="col mt-4 text-center">
                <div class="col text-center mb-3">
                    <input id="lu" type="checkbox" name="conditionBienOk" value="lu" required> <label for="lu">J'ai lu et j'accepte les</label> <a href="/mentions-legales/#cgu">conditions générales d'utilisation</a>.<sup>(1)</sup>
                </div>
                <div class="col-12 text-center">
                    <input type="hidden" id="recaptchaResponse" name="recaptcha-response">
                    <button type="submit" class="btn btn-success border border-primary">Demander un devis</button>
                </div>
                <div class="col-12 text-right small mt-3 mt-sm-0">
                    <a href="/demande-devis/delete/" class="text-danger">SUPPRIMER MA LISTE</a>
                </div>
                <div class="col text-danger text-left mt-3 mt-sm-0">
                    <sup>(1)</sup> Obligatoire.
                </div>
            </div>
        </form>
    </div>
</div>
<?php
require_once("../captcha/captchaGoogle.php");
require_once("../commun/bas_de_page.php");
?>