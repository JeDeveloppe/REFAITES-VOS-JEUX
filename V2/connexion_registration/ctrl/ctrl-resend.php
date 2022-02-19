<?php
@session_start ();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    require('../../controles/fonctions/validation_donnees.php');
    $mail = valid_donnees($_POST["mail"]);

    if (empty($mail)
        || !filter_var($mail, FILTER_VALIDATE_EMAIL)){

        $_SESSION['alertMessage'] = "Un champs n' était pas rempli, veuillez rééssayer !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }
    else{ //tout est bon on verifie si l'adresse mail existe

        require('../../config.php');
        require('../../bdd/connexion-bdd.php');

        $req = $bdd->prepare('SELECT * FROM clients WHERE email = :email');
        $req->execute(array('email' => $mail));
        $donnees = $req->fetch();

        if(is_array($donnees)){ // si adresse mail existe on peut comparer les mots de passe saisie et celui de la base de donnee

            function random_strings($length_of_string) 
            { 
                // String of all alphanumeric character 
                $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@'; 
            
                // Shufle the $str_result and returns substring 
                // of specified length 
                return substr(str_shuffle($str_result),0, $length_of_string); 
            } 

            $validKey = random_strings(64);
            $_SESSION['tokenPasswordChange'] = $validKey;
                 
                //CONTENUE DU MAIL
                $contentMail = '
                <!-- LINE -->
                <!-- Set line color -->
                <tr>
                    <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;" class="line">
                        <hr color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
                    </td>
                </tr>';


                $contentMail .= '
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
                            <th valign="top" style="padding-top: 10px">Voilà le lien pour modifier votre mot de passe:</th>
                            <tr>
                            <td align="center" style="padding-top: 10px;"><a href="'.$GLOBALS['domaine'].'/connexion/password/change/?email='.$donnees['email'].'&token='.$validKey.'&user='.$donnees['idUser'].'">Modifier mon mot de passe !</a></td>
                            </tr>
                            </table>
                    </td>
                </tr>';

                $contentMail .= '
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
                            <tr>
                            <td align="center" style="padding-top: 10px; color:red">Si vous n\'êtes pas à l\'origine de cet email vous pouvez l\'ignorer !</td>
                            </tr>
                            </table>
                    </td>
                </tr>';

                require_once('../../mails/mail_envoiFirstPassword.php');

                if(!$mail->send()) {
                    $_SESSION['alertMessage'] = "Problème pour envoyer la demande !";
                    $_SESSION['alertMessageConfig'] = "danger";
                    header("Location: ".$_SERVER['HTTP_REFERER']);
                    exit(); 
                }else{
                
                    $_SESSION['resendPassword'] = true;
                    //puis on redirige vers la page de resend
                    header("Location: ".$_SERVER['HTTP_REFERER']);
                    exit(); 
                }
        }
        else{ // adresse mail n'existe pas

            // redirection acceuil du site
            $_SESSION['alertMessage'] = "Adresse email inconnue, merci de vous inscrire !";
            $_SESSION['alertMessageConfig'] = "warning";
            $req->closeCursor(); 
            header("Location: /inscription/");
            exit();
        }
    }
}else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /");
    exit();  
}
?>