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

    $editeur = strtoupper(valid_donnees($_POST["editeur"]));
    $nom = strtoupper(valid_donnees($_POST["nom"]));
    $email = valid_donnees($_POST["email"]);
    $annee = valid_donnees($_POST["annee"]);

    //ON VERIFIE LES CHAMPS REMPLI
    if(empty($editeur) || strlen($editeur) > 30){
        $erreur = 1;
    }else{
        if(strlen($editeur) == 1 && $editeur != "?"){
            $erreur = 1;  
        }else{
            $_SESSION['editeur'] = $editeur;
        }
    } 
    if(empty($nom) || strlen($nom) < 3 || strlen($nom) > 30){
        $erreur = 1;
    }else{
        $_SESSION['nom'] = $nom;
    }
    if(empty($annee) || strlen($annee) > 4){
        $erreur = 1;
    }else{
        if(strlen($annee) == 1 && $annee != "?"){
            $erreur = 1;  
        }else{
            if(strlen($annee) > 1 && strlen($annee) < 4){
                $erreur = 1;
            }else{
                $_SESSION['annee'] = $annee;
            }
        }
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
            $_SESSION['alertMessage'] = "Captcha Google vide...<br/>Réessayez merci !";
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
                // On utilisera file_get_editeurs
                $response = file_get_contents($url);
            }

            // On vérifie qu'on a une réponse
            if(empty($response) || is_null($response)){
                $_SESSION['alertMessage'] = "Pas de réponse Captcha Google...<br/>Réessayez merci !";
                $_SESSION['alertMessageConfig'] = "warning";
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit();
            }else{ 

                $data = json_decode($response);
                if($data->success){

                    require_once("../../config.php");
                    require_once("../../bdd/connexion-bdd.php");

                    $timeEnd = time() + 31536000; //1 AN

                    $sqlListeMessages = $bdd -> prepare("INSERT INTO bouteille_mer (email,ip,nom,editeur,annee,time,end_time,actif) VALUES (?,?,?,?,?,?,?,?)");
                    $sqlListeMessages-> execute(array($email,$ipVisiteur,$nom,$editeur,$annee,time(),$timeEnd,0));

                    //SUPRESSION DES MESSAGES > 1 AN
                    $sqlDelete1an = $bdd -> prepare("DELETE FROM bouteille_mer WHERE end_time < ?");
                    $sqlDelete1an-> execute(array(time()));
                    
                    //ICI TOUT EST BON ON SUPPRIMER LES VARIABLES
                    unset($_SESSION['email']);
                    unset($_SESSION['editeur']);
                    unset($_SESSION['nom']);
                    unset($_SESSION['annee']);

                    if (!headers_sent($filename, $linenum)) {
                        $_SESSION['alertMessage'] = '<i class="fas fa-water"> Bouteille à la mer envoyée...</i>';
                        $_SESSION['alertMessageConfig'] = "info";
                        header("Location: ".$_SERVER['HTTP_REFERER']);
                        exit();
            
                    // Vous allez probablement déclencher une erreur ici
                    } else {
            
                    echo "Les en-têtes ont déjà été envoyés, depuis le fichier ".$filename." à la ligne ".$linenum."<br/>Il est donc impossible de vous rediriger automatiquement...";
                    exit;
                    }


      

                }else{

                    $_SESSION['alertMessage'] = "Mauvaise réponse Captcha Google...";
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