<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");
include('../../../config.php');
include('../../../bdd/connexion-bdd.php');
include('../../../bdd/table_config.php');
include('../../../controles/fonctions/validation_donnees.php');

$client = valid_donnees($_GET['client']);
$panier = valid_donnees($_GET['panier']);

$sqlClient = $bdd->prepare("SELECT * FROM clients WHERE idClient = ?");
$sqlClient->execute(array($client));
$donneesClient = $sqlClient->fetch();


//CONTENUE DU MAIL
$contentDevis = '
<!-- LINE -->
<!-- Set line color -->
<tr>
    <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;" class="line">
        <p>Désolé...</p>
        <hr color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
    </td>
</tr>';

$contentDevis .= '
<!-- PARAGRAPH -->
<!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
<tr>
    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 560px; font-size: 12px; font-weight: 400; line-height: 100%;
        padding-top: 25px; padding-bottom: 45px;
        color: #000000;
        font-family: sans-serif;" class="paragraph">
            <table border="0" cellpadding="0" cellspacing="0" align="center"
            bgcolor="#FFFFFF"
            width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
            max-width: 560px; margin-top:5px" class="container">
            <tr>
                <td align="left" style="padding-top: 10px; padding-left: 25px; padding-right: 25px; padding-bottom: 10px;">
                <p>J\' ai bien reçu votre demande de pièce, malheureusement après mes recherches je dois vous signaler que je n\'ai pas ce que vous m\' avez demandé en stock.</p>
                <p>Ne perdons pas espoir, le service reçoit de nouveaux jeux régulièrement.</p>
                <p>Je vous invite à surveiller les futurs arrivages sur la page Facebook.</p>
                <p align="right" style="padding-top: 30px;">Amicalement, Antoine.</p>
                </td>
            </tr>
            </table>
    </td>
</tr>';

//TRAITEMENT DU MAIL ET REDIRECTION
require_once("../../../mails/mail_envoiDevis0.php");
?>