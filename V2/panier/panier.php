<?php
@session_start ();
include_once("../config.php");

// utilisateur non loggé
$titreDeLaPage = "Panier | ".$GLOBALS['titreDePage'];
$descriptionPage = "Votre panier de demande de pièce pour compléter vos jeux et/ou achat de jeux d'occasion!";
include_once("../bdd/connexion-bdd.php");
require("../bdd/table_config.php");
$tva = $donneesConfig[6]['valeur'];
$adhesionRVJ = $donneesConfig[28]['valeur'];

$sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idUser = ?");
$sqlClient-> execute(array($_SESSION['sessionId']));
$donneesClient = $sqlClient->fetch();
//si encore membre association alors cela vaut 0
if($donneesClient['isAssociation'] > time()){
    $adhesionRVJ = "0.00";
}

$sqlListeAchats = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND qte > ? AND statut = ?");
$sqlListeAchats-> execute(array($_SESSION['sessionId'],0,0));
$donneesListeAchats = $sqlListeAchats->fetch();
$countAchats = $sqlListeAchats -> rowCount();

$sqlListeDemandes = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND qte IS NULL AND statut = ?");
$sqlListeDemandes-> execute(array($_SESSION['sessionId'],0));
$donneesListeDemandes = $sqlListeDemandes->fetch();
$countDemandes = $sqlListeDemandes -> rowCount();

$sqlOptgroup = $bdd -> prepare("SELECT * FROM pays WHERE actif = 1 AND rattachement = ? ORDER BY nom_fr_fr ASC");
$sqlOptgroup-> execute(array("FR-"));
$donneesOptGroup = $sqlOptgroup-> fetch();

$sqlPays = $bdd-> prepare("SELECT * FROM pays WHERE actif = 1 AND rattachement = ? ORDER BY nom_fr_fr ASC");
$sqlPays-> execute(array("NONE"));
$donneesPays = $sqlPays-> fetchAll();

//si y a plus rien en achat et en demande
if($countDemandes < 1 && $countAchats < 1){
    $_SESSION['alertMessage'] = "Votre panier est vide !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /");
    exit();
}

include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
?>

<script type="text/javascript">
    
    function deleteExpedition(){
        $('input[name=expeditionOption]').prop('checked', false);
        $('input[name=expeditionOption]').prop('required', false);
        $('#colAdresseLivraison').css("display", "none");
        $('#href_bouton_panier').replaceWith('<button id="href_bouton_panier" type="submit" class="btn btn-success border border-primary">Demander un devis</button>');
    }

    function checkExpedition(){
        // $('input[name=expeditionOption]').prop('required', true);
        $('#colAdresseLivraison').css("display", "block");
        $('#href_bouton_panier').replaceWith('<a href="/membre/adresses/" id="href_bouton_panier" class="btn btn-danger">Merci de compléter les données manquantes afin de pouvoir continuer...</a>');
    }

    
    function checkExpeditionOption1(){
        document.getElementById("envoi").click();
        $('#colAdresseLivraison').css("display", "block");
    }
    function checkExpeditionOption2(){
        document.getElementById("envoi").click();
        $('#colAdresseLivraison').css("display", "block");
    }
</script>

<div class="container-fluid mt-5">
    <div class="col h1 text-center mt-4">Panier</div>

    <div class="col-12 mt-5 h5 text-center text-danger animated faster fadeInRight">
        Pour toute première commande, une adhésion au service de 2€ vous sera facturée.<br/> 
        Cette adhésion est valable 1 an.
        <a href="/conditions-generales-de-vente/#cgvAdhesion" data-html="true" data-toggle="tooltip" data-placement="top">
            <i class="fas fa-question-circle text-info p-2"></i>
        </a>
    </div>
    <?php
        if($countAchats > 0){ //SI ON ACHETE DES JEUX OCCASION 
            $hrefControlPanier = "../panier/ctrl/ctrl-panier-payer.php";
            $texteBoutonPaiement = "Payer";
            require_once('./includes/tableau-des-achats.php');
        }

        if($countDemandes > 0){ //SI ON ACHETE DES PIECES DETACHEES 
            $hrefControlPanier = "../panier/ctrl/ctrl-panier.php";
            $texteBoutonPaiement = "Demander un devis";
            require_once('./includes/tableau-des-pieces-detachees.php');
        }

        if($countAchats > 0 && $countDemandes >= 0){ //SI ON ACHETE DES JEUX OCCASION ET OU PAS DE PIECE
            require_once('./includes/formulaire-achats.php');
        }
        if($countAchats == 0 && $countDemandes > 0){ //SI ON ACHETE QUE DES PIECES
            require_once('./includes/formulaire-pieces-detachees.php');
        }
    ?>
</div>
<?php
require_once("../captcha/captchaGoogle.php");
require_once("../commun/bas_de_page.php");
?>