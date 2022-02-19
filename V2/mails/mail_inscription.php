<?php
// fichier precedent: /controles/verifFormRegister.php
// variables du fichier précèdent
// $validKey, $mailDestinataire
require_once('../config.php');

//on include les templates pour avoir acces aux variables
require_once($_SERVER['DOCUMENT_ROOT'].'/mails/templates/headers/headerCommun.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/mails/templates/corpsInscription.php');
//option ligne desinscription dans le mail
$footer_desinscription = "non";
require_once($_SERVER['DOCUMENT_ROOT'].'/mails/templates/footers/footerNoReply.php');

// construction du mail (variables sont dans les fichiers inclus)
$contentMailToDestinataire = $headerCommun.$contentMail.$footerNoReply;

require '../PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer();
$mail->CharSet = "UTF-8";

$mail->SMTPDebug = 0;                               // Enable verbose debug output mettre 0 en production

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = $GLOBALS['smtp'];                      // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = $GLOBALS['compteMail'];                          // SMTP username
$mail->Password = $GLOBALS['compteMailPassword'];                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to

// $mail->From = $GLOBALS['compteMail'];
$mail->addReplyTo('noreply@echange-domicile.fr');
$mail->FromName = $GLOBALS['nomDomaine'];                       //nom de qui envoie le mail
$mail->addAddress($mailDestinataire);                              // ->destinataire
//$mail->addBCC($GLOBALS['compteMail']);                           // copie invisible vers l'adresse du site pour surveillance...

// $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Validez votre inscription sur Echange-Domicile.fr';                     //objet du mail
$mail->Body    = $contentMailToDestinataire;
// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

$mail->send();
// if(!$mail->send()) {
//     echo 'Message could not be sent.';
//     echo 'Mailer Error: ' . $mail->ErrorInfo;
// } else {
//     echo 'Message has been sent';
// }
?>