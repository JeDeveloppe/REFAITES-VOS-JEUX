<?php
@session_start ();
// ATTENTION il faut les variables du fichier précèdent
//on include les templates pour avoir acces aux variables
require_once($_SERVER['DOCUMENT_ROOT'].'/mails/templates/headers/headerCommun.php');
//$texteCorp_du_mail viens du fichier  /ADMIN/ gestion_annonce.php

//////////////////////////////////////
//  $contentDemandeDevis est dans verifFormListeMessage
//////////////////////////////////////
//require_once($_SERVER['DOCUMENT_ROOT'].'/mails/templates/corpsDemandeDevis.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/mails/templates/footers/footerNoReply.php');

// construction du mail (variables sont dans les fichiers inclus)
$contentMailToDestinataire = $headerCommun.$contentBouteille.$footerNoReply;


require '../../../PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer();
$mail->CharSet = "UTF-8";

$mail->SMTPDebug = 0;                               // Enable verbose debug output mettre 0 en production

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = $GLOBALS['smtp'];                      // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = $GLOBALS['compteMailContact'];                          // SMTP username
$mail->Password = $GLOBALS['compteMailPasswordContact'];                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to

$mail->From = $GLOBALS['compteMailContact'];
$mail->addReplyTo("no-reply@refaitesvosjeux.fr");
$mail->FromName = "REFAITES VOS JEUX";                       //nom de qui envoie le mail
$mail->addAddress($donneesBouteille['email']);                              // ->destinataire
//$mail->addBCC($GLOBALS['compteMail']);                           // copie invisible vers l'adresse du site pour surveillance...

// $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML
$mail->Subject = "Votre bouteille à la mer du ".date("d.m.Y",$donneesBouteille['time']);     //objet du mail      
$mail->Body    = $contentMailToDestinataire;
// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';


//$mail->send();

if(!$mail->send()) {
    $_SESSION['alertMessage'] = "Problème pour envoyer la demande !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /administration/recherche-documents.php");
    exit(); 
}else{

    //METTRE A JOUR bouteille envoyée
    $sqlUpdateBouteille = $bdd-> prepare("UPDATE bouteille_mer SET actif = ? , end_time = ? WHERE idBouteille = ?");
    $sqlUpdateBouteille-> execute(array(1,time(),$donneesBouteille['idBouteille'])); //ACTIF 1 = neutre en attendre de réponse


    $_SESSION['alertMessage'] = "Bouteille retournée !";
    $_SESSION['alertMessageConfig'] = "success";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();   
}
 
?>