<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");
include('../../../config.php');
include('../../../bdd/connexion-bdd.php');
include('../../../bdd/table_config.php');
$tva = $donneesConfig[6]['valeur'];


if(isset($_GET['relance'])){
    $sqlUpdateTimeSendMail = $bdd-> prepare("UPDATE documents SET time_mail_devis = ?, end_validation = ? , relance_devis = ? WHERE numero_devis = ?");
    $sqlUpdateTimeSendMail-> execute(array(time() ,time() + $donneesConfig[11]['valeur'], 1, $_GET['devis']));
}else{
    $sqlUpdateTimeSendMail = $bdd-> prepare("UPDATE documents SET time_mail_devis = ? WHERE numero_devis = ?");
    $sqlUpdateTimeSendMail-> execute(array(time(),$_GET['devis']));
}

$sqlDevis = $bdd -> prepare("SELECT * FROM documents WHERE numero_devis =  ? ");
$sqlDevis-> execute(array($_GET['devis']));
$donneesDevis = $sqlDevis -> fetch();

$sqlLignesDocument = $bdd -> prepare("SELECT * FROM documents_lignes WHERE idDocument = ?");
$sqlLignesDocument-> execute(array($donneesDevis['idDocument']));
$donneesLignesDocument = $sqlLignesDocument->fetchAll();

$sqlLignesDocumentAchat = $bdd -> prepare("SELECT * FROM documents_lignes_achats WHERE idDocument = ?");
$sqlLignesDocumentAchat-> execute(array($donneesDevis['idDocument']));
$donneesLignesDocumentAchat = $sqlLignesDocumentAchat->fetchAll();

//on regarde si y a deja une image enregistrer
$sqlVerifImageDocument = $bdd-> query("SELECT * FROM documents_images WHERE idDocuments = ".$donneesDevis['idDocument']);
$countVerifImageDocument = $sqlVerifImageDocument-> rowCount();
    if($countVerifImageDocument == 1){
        $donneesVerifImageDocument = $sqlVerifImageDocument-> fetch();
        $affichageImage = '<p><img
        border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: block;
        color: #000000;" src="data:image/jpeg;base64,'.$donneesVerifImageDocument['image'].'" alt="'.$donneesVerifImageDocument['nom'].'"
        width="150" height="150"></p>';
    }else{
        $affichageImage = "";
    }

$sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = ?");
$sqlClient-> execute(array($donneesDevis['idUser']));
$donneesClient = $sqlClient->fetch();


switch($donneesDevis['expedition']){
    case 'mondialRelay':
        $texteExpedition = "Expédition par Mondial Relay";
    break;
    case 'colissimo':
        $texteExpedition = "Expédition par Colissimo";
    break;
    case 'retrait_caen1':
        $texteExpedition = "Retrait à la Coop 5 pour 100 à Caen";
    break;
    case 'poste':
        $texteExpedition = "Expédition par La Poste";
    break;
}

if($donneesDevis['totalTVA'] == ""){
    $texteTVA = "0.00";
}else{
    $texteTVA = number_format($donneesDevis['totalTVA'] / 100,"2",".","");
}

//CONTENUE DU MAIL
$contentDevis = '
<!-- LINE -->
<!-- Set line color -->
<tr>
    <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;" class="line">
        <p>DEVIS '.$_GET['devis'].' (Valable jusqu\'au '.date("d.m.Y à G:i",$donneesDevis['end_validation']).')</p>
        <hr color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
    </td>
</tr>';


$contentDevis .= '
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
            <th valign="top" style="padding-top: 10px">Adresse de facturation:</th>
            <tr>
            <td align="left" style="padding-top: 10px; padding-left: 50%;">'.$donneesDevis['adresse_facturation'].'</td>
            </tr>
            </table>
    </td>
</tr>
<!-- PARAGRAPH -->
<tr>
    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 500px; font-size: 12px; font-weight: 400; line-height: 100%;
        padding-top: 5px; padding-bottom: 5px;
        color: #000000;
        font-family: sans-serif;" class="paragraph">
            <table border="1rem solid" cellpadding="0" cellspacing="0" align="center"
            width="500" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
            max-width: 500px; margin-top:5px" class="container">
            <th valign="middle" style="padding-top: 10px; padding-bottom: 10px; width: 80%; bgcolor=#AFFFFF ">Achat(s) / Demande(s)</th>
            <th align="center" valign="top" style="padding-top: 10px; padding-bottom: 10px;">Prix HT</th>';

            //lignes des achats
            foreach($donneesLignesDocumentAchat as $ligneAchat){
                $sqlJeuAchat = $bdd-> query("SELECT * FROM catalogue WHERE idCatalogue =".$ligneAchat['idCatalogue']);
                $donneesJeuAchat = $sqlJeuAchat-> fetch();

                $contentDevis .='     
                <tr>
                <td align="left" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 80%; font-size: 14px;
                    padding-top: 10px; padding-bottom: 10px;
                    color: #000000;
                    font-family: sans-serif;">
                        <p>Jeu d\'occasion:<br/><b>'.$donneesJeuAchat['nom'].' '.$donneesJeuAchat['editeur'].'</b></p>
                        <p>'.$ligneAchat['detailsComplet'].'</p>     
                </td>
                <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 20%; font-size: 14px;
                padding-top: 10px; padding-bottom: 10px;
                color: #000000;
                font-family: sans-serif;">
                        '.number_format(($ligneAchat['prix'] * $tva / 100),"2",".","").'
                </td>
                </tr>'; 
            }

            //ligne des jeux au detail
            foreach($donneesLignesDocument as $ligne){
                $sqlJeu = $bdd-> query("SELECT * FROM catalogue WHERE idCatalogue =".$ligne['idJeu']);
                $donneesJeu = $sqlJeu-> fetch();

                $contentDevis .='     
                <tr>
                <td align="left" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 80%; font-size: 14px;
                    padding-top: 10px; padding-bottom: 10px;
                    color: #000000;
                    font-family: sans-serif;">
                        <p><b>Votre demande pour le jeu '.$donneesJeu['nom'].':</b> '.$ligne['question'].'</p>

                        <p><b>Ma réponse:</b> '.$ligne['reponse'].'</p>
                        '.$affichageImage.'
                </td>
                <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 20%; font-size: 14px;
                padding-top: 10px; padding-bottom: 10px;
                color: #000000;
                font-family: sans-serif;">
                        '.number_format(($ligne['prix'] * $tva / 100),"2",".","").'
                </td>
                </tr>'; 
            }

$contentDevis .= '
            <tr>
                <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 2%; width: 40%; font-size: 14px; font-weight: bold;
                    padding-top: 10px; padding-bottom: 10px;
                    color: #000000;
                    font-family: sans-serif;" class="header">
                        Adhésion au service:<br/><i>Valable 1 an</i>
                </td>
                <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px;
                    padding-top: 10px; padding-bottom: 10px;
                    color: #000000;
                    font-family: sans-serif;" class="header">
                            '.number_format(($donneesDevis['prix_preparation'] * $tva / 100),"2",".","").'
                </td>
                </tr>
                <tr>
                <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 2%; width: 40%; font-size: 14px; font-weight: bold;
                    padding-top: 10px; padding-bottom: 10px;
                    color: #000000;
                    font-family: sans-serif;" class="header">
                        '.$texteExpedition.':
                </td>
                <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px;
                    padding-top: 10px; padding-bottom: 10px;
                    color: #000000;
                    font-family: sans-serif;" class="header">
                        '.number_format(($donneesDevis['prix_expedition'] * $tva / 100),"2",".","").'
                </td>
            </tr>
        </table>
    </td>
</tr>
<!-- PARAGRAPH -->
<!-- PARTIE DES TOTAUX -->
<tr>
    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 260px; font-size: 12px; font-weight: 400; line-height: 100%;
        padding-top: 10px; padding-bottom: 10px;
        color: #000000;
        font-family: sans-serif;" class="paragraph">
            <table border="1" cellpadding="0" cellspacing="0" align="center"
            bgcolor="#FFFFFF"
            width="260" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
            max-width: 260px;" class="container">
            <tr>
            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 53%; font-size: 14px;
            padding-top: 10px; padding-bottom: 10px;
            color: #000000;
            font-family: sans-serif;" class="header">TOTAL HT:
            </td>
            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px;
            padding-top: 10px; padding-bottom: 10px;
            color: #000000;
            font-family: sans-serif;" class="header">
                    '.number_format(($donneesDevis['totalHT'] * $tva) / 100,"2",".","").'
            </td>
            </tr>
            <tr>
            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px;
            padding-top: 10px; padding-bottom: 10px;
            color: #000000;
            font-family: sans-serif;" class="header">TVA:
            </td>
            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px;
            padding-top: 10px; padding-bottom: 10px;
            color: #000000;
            font-family: sans-serif;" class="header">
                    '.$texteTVA.'
            </td>
            </tr>
            <tr>
            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px; font-weight:bold;
            padding-top: 10px; padding-bottom: 10px;
            color: #000000;
            font-family: sans-serif;" class="header">TOTAL TTC:
            </td>
            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px; font-weight:bold;
            padding-top: 10px; padding-bottom: 10px;
            color: #000000;
            font-family: sans-serif;" class="header">
                    '.number_format(($donneesDevis['totalTTC'] * $tva) / 100,"2",".","").'
            </td>
            </tr>
            </table>
    </td>';



// PARTIE TVA si 0 ou pas
if($donneesConfig[6]['valeur'] == 1){
$contentDevis .= '
    <!-- PARAGRAPH -->
    <tr>
        <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 560px; font-size: 12px; font-weight: 400; line-height: 100%;
            padding-top: 10px; padding-bottom: 10px;
            color: #000000;
            font-family: sans-serif;" class="paragraph">
                <table border="0" cellpadding="0" cellspacing="0" align="center"
                bgcolor="#FFFFFF"
                width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                max-width: 560px;" class="container">
                <tr>
                <td align="left" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 53%; font-size: 14px;
                padding-top: 10px; padding-bottom: 10px;
                color: #000000;
                font-family: sans-serif;" class="header">TVA non applicable, article 293B du code général des impôts. 
                </td>
                </tr>
                </table>
        </td>
    </tr>';
}

//SI Y A UN COMMENTAIRE
if($donneesDevis['commentaire'] != ""){
    $contentDevis .= '
    <!-- PARAGRAPH -->
    <!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
    <tr>
        <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 500px; font-size: 12px; line-height: 100%;
            padding-top: 5px; padding-bottom: 25px;
            color: #000000;
            font-family: sans-serif;" class="paragraph">
                <table border="0" cellpadding="0" cellspacing="0" align="center"
                bgcolor="#FFFFFF"
                width="500" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                max-width: 500px; margin-top:5px" class="container">
                <tr>
                <td align="left" style="padding: 10px;"><p>Commentaire:</p>'.$donneesDevis['commentaire'].'</td>
                </tr>
                </table>
        </td>
    </tr>';
}

// PARTIE DES BOUTONS
$contentDevis .= '
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
                <a href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/accept.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'" style="text-decoration: underline;" alt="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/accept.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'">
                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 240px; min-width: 120px; border-collapse: collapse; border-spacing: 0; padding: 0;">
                    <tr>
                        <td align="center" valign="middle" style="padding: 12px 24px; margin: 0; text-decoration: underline; border-collapse: collapse; border-spacing: 0; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; -khtml-border-radius: 4px;"
                        bgcolor="#28a745"><a target="_blank" style="text-decoration: underline;
                        color: #FFFFFF; font-family: sans-serif; font-size: 17px; font-weight: 400; line-height: 100%;"
                        href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/accept.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'">
                            Accepter et payer le devis<br/>
                        </a>
                        </td>
                    </tr>
                    </table>
                </a>
            </td>
            <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 50%;
                padding-top: 25px;
                padding-bottom: 5px;" class="button"><a
                href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/refuse.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'" style="text-decoration: underline;">
                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 240px; min-width: 120px; border-collapse: collapse; border-spacing: 0; padding: 0;"><tr><td align="center" valign="middle" style="padding: 12px 24px; margin: 0; text-decoration: underline; border-collapse: collapse; border-spacing: 0; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; -khtml-border-radius: 4px;"
                        bgcolor="#dc3545"><a target="_blank" style="text-decoration: underline;
                        color: #FFFFFF; font-family: sans-serif; font-size: 17px; font-weight: 400; line-height: 100%;"
                        href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/refuse.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'">
                            Annuler la demande
                        </a>
                </td></tr></table></a>
            </td>
            </tr>
            </table>
    </td>
</tr>';

$contentDevis .= '
<!-- PARAGRAPH -->
<!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
<tr>
    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 500px; font-size: 12px; line-height: 100%;
        padding-top: 5px; padding-bottom: 25px;
        color: #000000;
        font-family: sans-serif;" class="paragraph">
            <table border="0" cellpadding="0" cellspacing="0" align="center"
            bgcolor="#FFFFFF"
            width="500" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
            max-width: 500px; margin-top:5px" class="container">
            <tr>
                <td align="left" style="padding: 10px;">
                    <p>Si les boutons n\'apparaîssent pas:</p>
                    <p><a href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/accept.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'">Cliquez sur ce lien si vous souhaitez accepter et payer le devis.</a></p>
                    <p><a href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/refuse.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'">Cliquez sur ce lien si vous souhaitez annuler la demande.</a></p>
                </td>
            </tr>
            </table>
    </td>
</tr>';

$contentDevis .= '
<!-- PARAGRAPH -->
<!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
<tr>
    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 500px; font-size: 12px; line-height: 100%;
        padding-top: 5px; padding-bottom: 25px;
        color: #000000;
        font-family: sans-serif;" class="paragraph">
            <table border="0" cellpadding="0" cellspacing="0" align="center"
            bgcolor="#FFFFFF"
            width="500" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
            max-width: 500px; margin-top:5px" class="container">
            <tr>
            <td align="left" style="padding: 10px;"><p>INFORMATION:</p>Le moyen le plus simple reste le paiement par carte bancaire, mais si vous souhaitez un autre moyen de paiement, il vous suffit de répondre à ce mail et nous trouverons une solution !</td>
            </tr>
            </table>
    </td>
</tr>';

$contentDevis .= '
<!-- PARAGRAPH -->
<tr>
    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 560px; font-size: 12px; font-weight: 400; line-height: 100%;
        padding-top: 10px; padding-bottom: 10px;
        color: #000000;
        font-family: sans-serif;" class="paragraph">
            <table border="0" cellpadding="0" cellspacing="0" align="center"
            bgcolor="#FFFFFF"
            width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
            max-width: 560px;" class="container">
            <tr>
            <td align="center" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 53%; font-size: 14px;
            padding-top: 10px; padding-bottom: 10px;
            color: #000000;
            font-family: sans-serif;" class="header">
            <a target="_blank" style="text-decoration: none;"
				href="'.$GLOBALS['urlService'].'"><img border="0" vspace="0" hspace="0"
                src="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/logo-'.$GLOBAL['servicePaiement'].'.png"
                width="300" height="auto"
                alt="logo du service de paiement" title="Payement sécurisé" style="
                color: #000000;
                font-size: 10px; margin: 0; padding: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: block;" /></a>
            </td>
            </tr>
            </table>
    </td>
</tr>';

//TRAITEMENT DU MAIL ET REDIRECTION
require_once("../../../mails/mail_envoiDevis.php");
?>