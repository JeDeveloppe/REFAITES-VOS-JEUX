<?php
@session_start ();

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    require("../../config.php");

    // On vérifie si le champ "recaptcha-response" contient une valeur
    if(empty($_POST['recaptcha-response'])){
        $_SESSION['alertMessage'] = "Captcha Google vide...";
        $_SESSION['alertMessageConfig'] = "warning";
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();
    }else{
        // On prépare l'URL
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$GLOBAL['cleSecreteGoogleCaptcha']."&response={$_POST['recaptcha-response']}";

        // On vérifie si curl est installé
        if(function_exists('curl_version')){
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
        }else{
            // On utilisera file_get_contents
            $response = file_get_contents($url);
        }

        // On vérifie qu'on a une réponse
        if(empty($response) || is_null($response)){
            $_SESSION['alertMessage'] = "Pas de réponse Captcha Google... ";
            $_SESSION['alertMessageConfig'] = "warning";
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit();
        }else{
            $data = json_decode($response);
            if($data->success){

                require("../../controles/fonctions/validation_donnees.php");
 
                $content = valid_donnees($_POST["content"]);
                $mailVisiteur = valid_donnees($_POST["email"]);
                $objet = valid_donnees($_POST["objet"]);
                $reponse = valid_donnees($_POST["reponse"]);

                
                /*Si les champs prenom et mail ne sont pas vides et si les donnees ont
                *bien la forme attendue...*/
                if (!empty($content)
                    || strlen($content) >= 30
                    || !empty($mailVisiteur)
                    || filter_var($mailVisiteur, FILTER_VALIDATE_EMAIL)
                    || !empty($reponse)
                    || $reponse != $_SESSION['reponseCaptcha']){


                    try{
                        require_once('../../config.php');
                        require_once('../../PHPMailer/class.phpmailer.php');
                        require_once('../../PHPMailer/PHPMailerAutoload.php');

                        $compteMail = $GLOBALS['compteMailContact'];         //vient de config
                        $compteMailpassword = $GLOBALS['compteMailPasswordContact'];     //vient de config
                        $adresseMailDuSite = $GLOBALS['adresseMailSite'];  //vient de config
                        $smtp = $GLOBALS['smtp'];  //vient de config

                        $mail = new PHPMailer();
                        $mail->CharSet = "UTF-8";
                        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
                        $mail->isSMTP();                                      // Set mailer to use SMTP
                        $mail->Host = $smtp;                                  // Specify main and backup SMTP servers
                        $mail->SMTPAuth = true;                               // Enable SMTP authentication
                        $mail->Username = $compteMail;                        // SMTP username
                        $mail->Password = $compteMailpassword;                // SMTP password
                        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                        $mail->Port = 587;                                    // TCP port to connect to

                        $mail->From = $mailVisiteur;
                        $mail->FromName = 'Contact d\'un visiteur du site.';
                        $mail->addAddress($adresseMailDuSite);               // Name is optional
                        $mail->addReplyTo($mailVisiteur);

                        $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
                        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                        $mail->isHTML(true);                                  // Set email format to HTML

                        $mail->Subject = $objet;
                        $mail->Body    = $content;
                        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        if(!$mail->send()) {
                            $_SESSION['alertMessage'] = "Bug, message non envoyé :(";
                            $_SESSION['alertMessageConfig'] = "warning";
                            header("Location: /contact/");
                            echo 'Mailer Error: ' . $mail->ErrorInfo;
                        } else {
                            $_SESSION['alertMessage'] = "Message bien envoyé, je vous remercie !";
                            $_SESSION['alertMessageConfig'] = "success";
                            header("Location: /contact/");
                        }
                    }
                    catch(PDOException $e){
                        echo 'Erreur : '.$e->getMessage();
                        $_SESSION['alertMessage'] = "Bug, message non envoyé :(";
                        $_SESSION['alertMessageConfig'] = "warning";
                        header("Location: /contact/");
                    }
                }else{
                    $_SESSION['alertMessage'] = "Un champs n' était pas correct, veuillez rééssayer !";
                    $_SESSION['alertMessageConfig'] = "danger";
                    header("Location: /contact/");
                }
            }
        }
    }
}else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /");  
}
?>