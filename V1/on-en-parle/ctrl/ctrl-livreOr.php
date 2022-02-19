<?php
@session_start ();

// On vérifie que la méthode POST est utilisée
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    require("../../controles/fonctions/validation_donnees.php");
    //require("../../controles/fonctions/getIp.php");
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      }else{
        $ip = $_SERVER['REMOTE_ADDR'];
      }
    $ipVisiteur = $ip;

    $content = valid_donnees($_POST["content"]);
    $pseudo = valid_donnees($_POST["pseudo"]);
    $email = valid_donnees($_POST["email"]);

    //ON VERIFIE LES CHAMPS REMPLI
    if(empty($content) || strlen($content) < 15 || strlen($content) > 300){
        $erreur = 1;
    }else{
        $_SESSION['content'] = $content;
    } 
    if(empty($pseudo) || strlen($pseudo) < 3 || strlen($pseudo) > 30){
        $erreur = 1;
    }else{
        $_SESSION['pseudo'] = $pseudo;
    }
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $erreur = 1;
    }else{
        $_SESSION['email'] = $email;
    }

    if(isset($erreur)){
        $_SESSION['alertMessage'] = "Un champs n' était pas correct, veuillez rééssayer !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }else{  

        // On vérifie si le champ "recaptcha-response" contient une valeur
        if(empty($_POST['recaptcha-response'])){
            $_SESSION['alertMessage'] = "Captcha Google vide...";
            $_SESSION['alertMessageConfig'] = "warning";
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit();
        }else{

            require("../../config.php");

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
                    //protection avec ip
                    if(isset($_SESSION['adresseIp'])){
                        if($ip == $_SESSION['adresseIp']){
                            $_SESSION['alertMessage'] = "Vous avez déjà écrit dans le livre il n'y a pas longtemps... !";
                            $_SESSION['alertMessageConfig'] = "warning";
                            header("Location: ".$_SERVER['HTTP_REFERER']);
                            exit();
                        }
                    }else{
                        $_SESSION['adresseIp'] = $ipVisiteur;
                    }

                    require_once("../../config.php");
                    require_once("../../bdd/connexion-bdd.php");

                    $timeEnd = time() + 31536000; //1 AN

                    $sqlListeMessages = $bdd -> prepare("INSERT INTO livreOr (email,ip,pseudo,content,time,end_time,actif) VALUES (?,?,?,?,?,?,?)");
                    $sqlListeMessages-> execute(array($email,$ipVisiteur,$pseudo,$content,time(),$timeEnd,0));

                    //SUPRESSION DES MESSAGES > 1 AN
                    $sqlDelete1an = $bdd -> prepare("DELETE FROM livreOr WHERE end_time < ?");
                    $sqlDelete1an-> execute(array(time()));
                    
                    //ICI TOUT EST BON ON SUPPRIMER LES VARIABLES
                    unset($_SESSION['email']);
                    unset($_SESSION['content']);
                    unset($_SESSION['pseudo']);

                    $_SESSION['alertMessage'] = "Message enregistré et soumis à validation !";
                    $_SESSION['alertMessageConfig'] = "success";

                    if (!headers_sent($filename, $linenum)) {
                        header("Location: ".$_SERVER['HTTP_REFERER']);
                        exit;
                    // Vous allez probablement déclencher une erreur ici
                    } else {
                       echo "Erreur dans ".$filename." à la ligne: ".$linenum;
                       exit;
                    }
                }else{

                    $_SESSION['alertMessage'] = "Mauvaise réponse Captcha Google...<br/>Veuillez réessayer !";
                    $_SESSION['alertMessageConfig'] = "danger";
                    header("Location: ".$_SERVER['HTTP_REFERER']);
                    exit();
                }
            }
        }//fin de recaptcha ok
    }//fin des controles POST
}//fin de methode POST
else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /accueil/");  
}
?>