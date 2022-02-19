<?php
@session_start ();
include_once("../../config.php");
if($GLOBAL['versionSITE'] >= 2){
    require_once("../controles/fonctions/memberOnline.php");
}
// utilisateur non loggé
$titreDeLaPage = "Liste des souhaits d'achat | ".$GLOBALS['titreDePage'];
$descriptionPage = "Votre liste d'achat de jeux complet !";
include_once("../../bdd/connexion-bdd.php");
require("../../bdd/table_config.php");
$tva = $donneesConfig[6]['valeur'];
$adhesionRVJ = $donneesConfig[28]['valeur'];

$sqlClient = $bdd -> prepare("SELECT * FROM users WHERE validKey = ?");
$sqlClient-> execute(array($_SESSION['sessionId']));
$donneesClient = $sqlClient->fetch();

$sqlListeMessages = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND qte > ?");
$sqlListeMessages-> execute(array($_SESSION['sessionId'],0));
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
    $_SESSION['alertMessage'] = "La liste des achats est vide !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /catalogue-des-jeux-complets/");
    exit();
}

//SI CAPTCHA GOOGLE NE MARCHE PAS
//require("./captcha/captcha.php");
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
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
    <div class="col h1 text-center mt-5">Mon panier</div>
    <div class="col h5 text-center text-danger animated faster fadeInRight">
        <i class="fas fa-rss"></i> ATTENTION / IMPORTANT !<br />
        Uniquement pour de la vente à emporter sur Caen à la Coop 5 pour 100 !
    </div>
    <!-- TABLE DES DEMANDES -->
    <div class="row mt-2 overflow-auto">
        <table class="table table-sm table-striped mt-4 col-11 mx-auto">
            <thead class="thead-dark text-center">
                <tr>
                    <th>Action</th>
                    <th>Image</th>
                    <th>Jeu</th>
                    <th>Demande</th>
                    <th>Prix U.<br/>TTC</th>
                    <th>Qté</th>
                    <th>Total TTC</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $totalHT = 0;
                while($donneesListeMessages){  
                    //information du jeu complet
                    $requeteJeuxComplet = "SELECT * FROM jeux_complets WHERE idJeuxComplet = ".$donneesListeMessages['idJeu'];
                    $sqlJeuxComplet = $bdd -> query($requeteJeuxComplet);
                    $donneesJeuxComplet = $sqlJeuxComplet-> fetch();

                    //on recupere tout de la boite de jeu
                    $sqlJeux = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = (SELECT idCatalogue FROM jeux_complets WHERE idJeuxComplet = ".$donneesListeMessages['idJeu'].")");
                    $donneesJeux = $sqlJeux -> fetch();
                    //on cherche l'image du jeu
                    $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$donneesJeux['idCatalogue']);
                    $donneesImage = $sqlImage->fetch();
                    ?>
                    <tr>
                    <td class="text-center align-middle">
                        <a href="/achats/delete/<?php echo $donneesListeMessages['idListeMessages'];?>/" class="btn btn-danger p-1"><i class="fas fa-trash-alt" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Supprimer de la liste"></i></a>
                    </td>
                    <td class="text-center align-middle"><?php echo '<div class="divImgTableauListeMessage"><img class="img-thumbnail" src="data:image/jpeg;base64,'.$donneesImage['image'].'"/></div>'; ?></td>
                    <td class="text-center align-middle"><?php echo $donneesJeux['nom']."<br/>".$donneesJeux['editeur']."<br/>".$donneesJeux['annee']; ?></td>
                    <td class="text-center align-middle"><?php echo $donneesListeMessages['message']; ?></td>
                    <td class="text-center align-middle"><?php echo number_format(($donneesJeuxComplet['prixHT'] * $tva)/100,2,",",' '); ?> €</td>
                    <td class="text-center align-middle"><?php echo $donneesListeMessages['qte']; ?></td>
                    <td class="text-center align-middle"><?php echo number_format(($donneesListeMessages['qte'] * $donneesJeuxComplet['prixHT'] * $tva)/100,2,",",' '); ?> €</td>
                    </tr>
                <?php
                $totalHT += $donneesListeMessages['qte'] * $donneesJeuxComplet['prixHT'];
                $donneesListeMessages = $sqlListeMessages->fetch();
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="row mt-2">
        <div class="col-11 col-sm-9 col-md-6 mx-auto">
            <table class="table table-sm table-striped mt-4 text-center">
                <thead>
                    <th>Sous total HT</th>
                    <th>Adhésion RVJ en HT</th>
                    <th>Total HT</th>
                    <th>Total TVA</th>
                    <th>Total TTC</th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php
                        
                                echo number_format($totalHT/100,2,","," ");
                                $totalHTavecAdhesion = $totalHT + $adhesionRVJ;
                                $totalTTC = ($totalHT+ $adhesionRVJ) * $tva;
                                $adhesionRVJTTC = $adhesionRVJ * $tva;
                                $TVA = $totalTTC - $totalHTavecAdhesion;
                            ?>
                        </td>
                        <td>
                            <?php
                                if($donneesClient['isAssociation'] > time() || $donneesClient['isAssociation'] == NULL){
                                    echo '<input type="hidden" name="association" value="'.$adhesionRVJ.'">'.number_format($adhesionRVJTTC/100,2,",","");
                                }else{
                                    echo '<input type="hidden" name="association" value="0.00"> OUI<br/><small>Jusqu\'au '.date("d.m.Y à G:i",$donneesClient['isAssociation']).'</small>';
                                }
                            ?>
                        </td>
                        <td><?php echo number_format(($totalHTavecAdhesion/100),2,",",""); ?></td>
                        <td><?php echo number_format(($TVA/100),2,",",""); ?></td>
                        <td><?php echo number_format(($totalTTC/100),2,",",""); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- PARTIE FORMULAIRE -->
    <div class="row mt-4">
        <form class="col" method="post" action="/panier/panierJeuxComplet/ctrl/ctrl-panierAchats.php" name="contactretrait">
            <div class="col d-flex flex-column flex-lg-row">
                <!-- EXPEDITION / RETRAIT -->
                <div class="col col-sm-10 col-md-9 mx-auto mt-3 mt-lg-0 col-lg-6">
                    <div class="col h4 text-center">Retrait:</div>
                    <ul class="list-group">
                        <!-- <div class="list-group-item">
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
                        </div> -->
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="row ml-2">
                                        <div class="col-1"><input type="radio" id="retrait" name="port" value="retrait_caen1" checked onclick="deleteExpedition()"></div>
                                        <div class="col-11"><label for="retrait">Uniquement en retrait à La Coop 5 pour 100, 33 route de Trouville, 14 000 Caen (prévoir un délai de maximum 7 jours).</label></div>
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
                <?php
                    if($donneesClient['isAssociation'] > time() || $donneesClient['isAssociation'] == NULL){
                        echo '<input type="hidden" name="association" value="'.$adhesionRVJ.'">';
                    }else{
                        echo '<input type="hidden" name="association" value="0.00">';
                    }
                ?>
                <div class="col-12 text-center">
                    <input type="hidden" id="recaptchaResponse" name="recaptcha-response">
                    <button type="submit" class="btn btn-success border border-primary">Acheter</button>
                </div>
                <div class="col-12 text-right small mt-3 mt-sm-0">
                    <a href="/achats/delete/" class="text-danger">SUPPRIMER MES SOUHAITS</a>
                </div>
                <div class="col text-danger text-left mt-3 mt-sm-0">
                    <sup>(1)</sup> Obligatoire.
                </div>
            </div>
        </form>
    </div>
</div>
<?php
require_once("../../captcha/captchaGoogle.php");
require_once("../../commun/bas_de_page.php");
?>