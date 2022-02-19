<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");
include('../../../config.php');
include('../../../bdd/connexion-bdd.php');
require('../../../controles/fonctions/validation_donnees.php');
require("../../../bdd/table_config.php");

$doc = valid_donnees($_GET['doc']);

//MISE A JOUR DATE ENVOI
$update = time()."|SANS";
//on met la date de l'envoi
$sqlUpdateColissimo = $bdd -> prepare("UPDATE documents SET envoyer = ? WHERE idDocument = ?");
$sqlUpdateColissimo-> execute(array($update,$doc)); 



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
            Bonjour,<p>Le service vient tout juste de déposer votre commande à cette adresse:</p>
            <p>'.$donneesConfig[9]['valeur'].'</p>
            <p>Bonne journée</p>
        </td>
        </tr>
        </table>
    </td>
</tr>';

//TRAITEMENT DU MAIL ET REDIRECTION
require_once("../../../mails/mail_retraitCommande.php");
?>