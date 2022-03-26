<?php
@session_start ();

// On vérifie que la méthode POST est utilisée
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    require_once("../../controles/fonctions/validation_donnees.php");

    if($_POST['conditionBienOk'] != "lu"){
        $_SESSION['alertMessage'] = "Conditions non lues !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }


    $cp = valid_donnees($_POST['cp']);
    $pays = valid_donnees($_POST['pays']);
    $email = valid_donnees($_POST['email']);
    $telephone = valid_donnees($_POST['telephone']);
    $password1 = valid_donnees($_POST['password']);
    $password2 = valid_donnees($_POST['password2']);

    //si un champs n'est pas vide on met en session pour un eventuel retour arrière


    if($email != ""){
        $_SESSION['email'] = $email;
    }
    if($password1 != ""){
        $_SESSION['password1'] = "ok-saisie";
    }
    if($password2 != ""){
        $_SESSION['password2'] = "ok-saisie";
    }    
    if($telephone != ""){
        $_SESSION['telephone'] = $telephone;
    }
    $_SESSION['cp'] = $cp;
    $_SESSION['pays'] = $pays;

    //si une session est vide on revient en arrière
    if($_SESSION['cp'] == "" ||
        $_SESSION['pays'] == "" ||
        $_SESSION['email'] == "" ||
        $_SESSION['password1'] == "" ||
        $_SESSION['telephone'] == "" ||
        $_SESSION['password2'] == ""){

        $_SESSION['alertMessage'] = "Il manque un champs de saisie !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }
 
    //si les mots de passe ne sont pas strictement identique
    if($password1 !== $password2){
        $_SESSION['alertMessage'] = "Les mots de passe ne correspondent pas !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();   
    }
    //on controle chaque champs si on a le format attendu
    //prenom,nom,ville,pays inférieur à 50

    if($pays == "FR"){
        if(!preg_match('#[0-9]{2,3}$#', $_SESSION['cp'])){
            unset($_SESSION['cp']);
            $_SESSION['alertMessage'] = "Saisie département incorrect !";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();
        }
    }else{
        $_SESSION['cp'] = 0;
    }

    //on controle adresse mail au bon format
    if(!filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL)){
        unset($_SESSION['email']);
        $_SESSION['alertMessage'] = "Mauvais format: adresse email !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();  
    }

    //on controle le champs telephone
    if(!preg_match('#^[0-9]{8,10}$#', $_SESSION['telephone'])){
        unset($_SESSION['telephone']);
        $_SESSION['alertMessage'] = "Saisie téléphone incorrect !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }else{

        if(preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/', $telephone,  $matches ) ){ // numero à 10 chiffres
            $telephone = $matches[1] . '-' .$matches[2] . '-' . $matches[3] . '-' . $matches[4] . '-' . $matches[5];
        }else if(preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $telephone,  $matches ) ){ //numero à 8 chiffres
            $telephone = $matches[1] . '-' .$matches[2] . '-' . $matches[3] . '-' . $matches[4];
        }else{
            $telephone = $telephone;
        }
        
    }

    // On vérifie si le champ "recaptcha-response" contient une valeur
    if(empty($_POST['recaptcha-response'])){
        $_SESSION['alertMessage'] = "Captcha Google vide... Veuillez ré-essayer !";
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
                //tout est bon on verifie si l'adresse mail n'existe pas deja dans la base
                require('../../config.php');
                require('../../bdd/connexion-bdd.php');

                $reqMail = $bdd->prepare('SELECT * FROM clients WHERE email = :email');
                $reqMail ->execute(array('email' => $email));
                $donnees = $reqMail->fetch();
                $nbrEmail = $reqMail->rowCount();

                if($nbrEmail > 0){ // si adresse mail existe on ne peut pas re-creer le membre
                    $_SESSION['alertMessage'] = "Inscription déja faite: réglamer un mot de passe !";
                    $_SESSION['alertMessageConfig'] = "warning";
                    header("Location: /first-connexion/");
                    exit();
        
                }
                else{ // adresse mail n'existe pas on peut faire l' inscription
            
                    //Hachage du mot de passe
                    $options = [
                        'cost' => $GLOBALS['costHashageMdp'], //nombre de fois renouveller -> config.php
                    ];
                    $pass_hache = password_hash($password1, PASSWORD_DEFAULT, $options);

                    //validKey aléatoire
                    function random_strings($length_of_string) 
                    { 
                        // String of all alphanumeric character 
                        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@'; 
                    
                        // Shufle the $str_result and returns substring 
                        // of specified length 
                        return substr(str_shuffle($str_result),0, $length_of_string); 
                    } 

                    //on creer une clefUnique tant que cela n'existe pas dans la base
                    do{
                        $validKey = random_strings(64);
                        $verifValidKey = $bdd -> query("SELECT * FROM clients WHERE idUser = '$validKey' ");
                        $donneesValidKey = $verifValidKey -> fetch();
                    }
                    while($donneesValidKey == 1);


                    // Insertion
                    $timeInscription = time();

                    $sqlSaveClient = $bdd -> prepare("INSERT INTO clients (telephone,email,password,idUser,userLevel,timeInscription,lastVisite,isAssociation,paysFacturation,paysLivraison) VALUES (?,?,?,?,?,?,?,?,?,?)");
                    $sqlSaveClient-> execute(array($telephone,$email,$pass_hache,$validKey,1,$timeInscription,0,0,$pays,$pays));
    
                    // redirection acceuil du site
                    session_destroy();
                    session_start ();
                    $_SESSION['alertMessage'] = "Inscription faite, vous pouvez vous identifier !";
                    $_SESSION['alertMessageConfig'] = "success";
                    header("Location: /connexion/");
                    exit();
                    
                }
            }
        }
    }
}else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /");  
    exit();
}
?>