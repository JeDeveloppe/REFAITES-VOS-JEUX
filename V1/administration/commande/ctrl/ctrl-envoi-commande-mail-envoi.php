<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");
include('../../../config.php');
include('../../../bdd/connexion-bdd.php');
require('../../../controles/fonctions/validation_donnees.php');

$doc = valid_donnees($_GET['doc']);

//si y a un numéro colissimo
if(isset($_GET['numeroColissimo'])){
    $numeroColissimo = valid_donnees($_GET['numeroColissimo']);
    //control longueur champs saisi entre 11 et 15
    if(strlen($numeroColissimo) < 11 || strlen($$numeroColissimo) > 15){
        $_SESSION['alertMessage'] = "Numéro incorrect, doit être entre 11 et 15 caractères...";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();   
    }

    $update = time()."|".$numeroColissimo;
    //on met a jour le numero de colissimo
    $sqlUpdateColissimo = $bdd -> prepare("UPDATE documents SET envoyer = ? WHERE idDocument = ?");
    $sqlUpdateColissimo-> execute(array($update,$doc));
}else{
    $update = time()."|SANS";
    //on met la date de l'envoi
    $sqlUpdateColissimo = $bdd -> prepare("UPDATE documents SET envoyer = ? WHERE idDocument = ?");
    $sqlUpdateColissimo-> execute(array($update,$doc)); 
}


//on cherche les infos utile pour ecriture du mail
$sqlDevis = $bdd -> prepare("SELECT * FROM documents WHERE idDocument =  ? AND etat = ?");
$sqlDevis-> execute(array($_GET['doc'],2));
$donneesDevis = $sqlDevis -> fetch();

$sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = ?");
$sqlClient-> execute(array($donneesDevis['idUser']));
$donneesClient = $sqlClient->fetch();

//CONTENUE DU MAIL
$contentEnvoi = '
<!-- PARAGRAPH -->
<!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
<tr>
    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 560px; font-size: 14px; font-weight: 400; line-height: 100%;
        padding-top: 25px; padding-bottom: 25px;
        color: #000000;
        font-family: sans-serif;" class="paragraph">
        <table border="0" cellpadding="0" cellspacing="0" align="center"
        bgcolor="#FFFFFF"
        width="500" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
        max-width: 500px; margin-top:5px" class="container">
        <tr>
        <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 50%;
            padding-top: 5px;
            padding-bottom: 5px;" class="button">
            Bonjour,<p>Le service vient tout juste d\'expédier votre commande.</p>
            <p>Vous allez bientôt la recevoir à l\'adresse communiquée lors de votre demande.</p>
            <p>Bonne journée</p>
        </td>
        </tr>
        </table>
    </td>
</tr>';

// PARTIE BOUTON SUIVI COLISSIMO
if(isset($_GET['numeroColissimo'])){
$contentEnvoi .= '
<!-- PARAGRAPH -->
<tr>
    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 500px; font-size: 12px; font-weight: 400; line-height: 100%;
        padding-top: 10px; padding-bottom: 10px;
        color: #000000;
        font-family: sans-serif;" class="paragraph">
            <table border="0" cellpadding="0" cellspacing="0" align="center"
            bgcolor="#FFFFFF"
            width="500" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
            max-width: 500px; margin-top:5px" class="container">
            <tr>
            <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 50%;
                padding-top: 25px;
                padding-bottom: 5px;" class="button">
                <a href="https://www.laposte.fr/particulier/outils/suivre-vos-envois?code='.$numeroColissimo.'" target="_blank" style="text-decoration: underline;">
                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 240px; min-width: 120px; border-collapse: collapse; border-spacing: 0; padding: 0;">
                    <tr>
                        <td align="center" valign="middle" style="padding: 12px 24px; margin: 0; text-decoration: none; border-collapse: collapse; border-spacing: 0; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; -khtml-border-radius: 4px;"
                        bgcolor="#fd7e14"><a target="_blank" style="text-decoration: none;
                        color: #FFFFFF; font-family: sans-serif; font-size: 17px; font-weight: 400; line-height: 100%;"
                        href="https://www.laposte.fr/particulier/outils/suivre-vos-envois?code='.$numeroColissimo.'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16">
                        <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                        </svg> Suivre mon colis
                        </a>
                        </td>
                    </tr>
                    </table>
                </a>
            </td>
            </tr>
            </table>
    </td>
</tr>';
}

//TRAITEMENT DU MAIL ET REDIRECTION
require_once("../../../mails/mail_envoiCommande.php");
?>