<?php
@session_start ();

require("./fonctions/validation_donnees.php");

    $pseudo = valid_donnees($_POST["pseudo"]);
    $mailDestinataire = valid_donnees($_POST["mail"]);
    $password1 = valid_donnees($_POST["password1"]);
    $password2 = valid_donnees($_POST["password2"]);

    if (empty($pseudo)
        || strlen($pseudo) >= 15
        || empty($mailDestinataire)
        || !filter_var($mailDestinataire, FILTER_VALIDATE_EMAIL)
        || empty($password1)
        || empty($password2)
        || $password1 != $password2){

        $_SESSION['alertMessage'] = "Un champs n' était pas correct, veuillez rééssayer !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: /inscription/");
        exit();
    }
    else{ //tout est bon on verifie si l'adresse mail n'existe pas deja dans la base

 
        require_once('../config.php');
        require_once('../bdd/connexion-bdd.php');

        $reqMail = $bdd->prepare('SELECT * FROM users WHERE email = :email');
        $reqMail ->execute(array('email' => $mailDestinataire));
        $donnees = $reqMail->fetch();

        if($donnees){ // si adresse mail existe on ne peut pas re-creer le membre
            $_SESSION['alertMessage'] = "Inscription impossible: cette adresse email existe déjà !";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: /inscription/");
            exit();
 
        }
        else{ // adresse mail n'existe pas on peut faire l' inscription
    
            //Hachage du mot de passe
            $options = [
                'cost' => $GLOBALS['costHashageMdp'], //nombre de fois renouveller -> config.php
            ];
            $pass_hache =  password_hash($password1, PASSWORD_BCRYPT, $options);

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
                $verifValidKey = $bdd -> query("SELECT * FROM users WHERE validKey = '$validKey' ");
                $donneesValidKey = $verifValidKey -> fetch();
            }
            while($donneesValidKey == 1);


            // Insertion
            $timeInscription = time();
            require_once('../bdd/connexion-bdd.php');
            $req = $bdd->prepare('INSERT INTO users(pseudo, email, password, validkey, userLevel,timeInscription,lastVisite) VALUES(:pseudo, :email, :password, :validKey, :userLevel, :timeInscription, :timeLastVisite)');
            $req->execute(array('pseudo' => $pseudo,'email' => $mailDestinataire,'password' => $pass_hache,'validKey' => $validKey,'userLevel' => "0",'timeInscription' => $timeInscription,'timeLastVisite' => 0));
        
            // redirection acceuil du site
            $_SESSION['alertMessage'] = "Inscription faite, voir avec l'Administrateur pour l'affectation des droits.";
            $_SESSION['alertMessageConfig'] = "success";
            header("Location: /accueil/");
            exit();
            
        }
    }
?>