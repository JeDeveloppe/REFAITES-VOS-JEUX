<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");
include('../../../config.php');
include('../../../bdd/connexion-bdd.php');
require('../../../controles/fonctions/validation_donnees.php');

$url = valid_donnees($_GET['url']);

$sqlBouteille = $bdd -> prepare("SELECT * FROM bouteille_mer WHERE idBouteille =  ? ");
$sqlBouteille-> execute(array($_GET['bouteille']));
$donneesBouteille = $sqlBouteille -> fetch();

//CONTENUE DU MAIL
$contentBouteille = '
<!-- LINE -->
<!-- Set line color -->
<tr>
    <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;" class="line">
        <p><b>Bouteille à la mer</b></p>
        <hr color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
    </td>
</tr>';

//CONTENUE DU MAIL
$contentBouteille .= '
<!-- PARAGRAPH -->
<!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
<tr>
    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 560px; font-size: 12px; font-weight: 400; line-height: 100%;
        padding-top: 25px; padding-bottom: 25px;
        color: #000000;
        font-family: sans-serif;" class="paragraph">
            <table border="0" cellpadding="0" cellspacing="0" align="center"
            bgcolor="#FFFFFF"
            width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
            max-width: 560px; margin-top:5px" class="container">
            <th valign="top" style="padding-top: 10px">Votre bouteille à la mer du '.date("d.m.Y",$donneesBouteille['time']).':</th>
            <tr>
                <td align="center" style="padding-top: 30px;">
                <p>'.$donneesBouteille['nom'].' '.$donneesBouteille['editeur'].'</p>
                </td>
            </tr>
            <tr>
            <td align="center" style="padding-top: 30px;">
            <p>Bonjour, </p>
            <p>Un jeu avec des caractéristiques très proches viens d\'être mis en ligne par le service.</p>
            </td>
        </tr>
            </table>
    </td>
</tr>';


// PARTIE DES BOUTONS
$contentBouteille .= '
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
                <a href="'.$url.$donneesBouteille['idBouteille'].'/visite/" target="_blank" style="text-decoration: underline;">
                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 240px; min-width: 120px; border-collapse: collapse; border-spacing: 0; padding: 0;">
                    <tr>
                        <td align="center" valign="middle" style="padding: 12px 24px; margin: 0; text-decoration: underline; border-collapse: collapse; border-spacing: 0; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; -khtml-border-radius: 4px;"
                        bgcolor="#28a745"><a target="_blank" style="text-decoration: underline;
                        color: #FFFFFF; font-family: sans-serif; font-size: 17px; font-weight: 400; line-height: 100%;"
                        href="'.$url.$donneesBouteille['idBouteille'].'/visite/">
                            Voir le jeu en ligne<br/>
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

//TRAITEMENT DU MAIL ET REDIRECTION
require_once("../../../mails/mail_envoiBouteille.php");
?>