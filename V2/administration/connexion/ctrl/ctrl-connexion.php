<?php
@session_start ();

    $mail = valid_donnees($_GET["mail"]);
    $passwordUser = valid_donnees($_GET["passwordUser"]);

    function valid_donnees($donnees){
        $donnees = trim($donnees);
        $donnees = stripslashes($donnees);
        $donnees = htmlspecialchars($donnees);
        return $donnees;
    }

    if (empty($mail)
        || !filter_var($mail, FILTER_VALIDATE_EMAIL)
        || empty($passwordUser)){

        $_SESSION['alertMessage'] = "Un champs n' était pas rempli, veuillez rééssayer !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: /R3f@iteV0sJ3u&/");
    }
    else{ //tout est bon on verifie si l'adresse mail existe

        require_once('../../../config.php');
        require_once('../../../bdd/connexion-bdd.php');

        $req = $bdd->prepare('SELECT * FROM clients WHERE email = :email AND userLevel = 4');
        $req->execute(array('email' => $mail));
        $donnees = $req->fetch();

        if(is_array($donnees)){ // si adresse mail existe on peut comparer les mots de passe saisie et celui de la base de donnee


            $passwordFromBdd = $donnees['password'];
            
            if(password_verify($passwordUser, $passwordFromBdd)){ // les mots de passes sont identiques on se connecte
                //on met a jour la bdd de la derniere visite
                $updateTimeVisite = $bdd->prepare('UPDATE clients SET lastVisite = :time WHERE email = :email');
                $updateTimeVisite->execute(array('time' => time(), 'email' => $mail));

                $_SESSION['pseudo'] = $donnees['pseudo'];
                $_SESSION['sessionId'] = $donnees['idUser'];
                $_SESSION['levelUser'] = $donnees['userLevel'];
                $_SESSION['validKey'] = $donnees['validKey'];

                $_SESSION['alertMessage'] = "Bienvenue ".$donnees['pseudo'];
                $_SESSION['alertMessageConfig'] = "success";

                header("Location: /admin/accueil/");
                exit(); 

            }else{            
                $_SESSION['alertMessage'] = "Mot de passe incorrect !";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: /R3f@iteV0sJ3u&/");
            }

        }
        else{ // adresse mail n'existe pas

            // redirection acceuil du site
            $_SESSION['alertMessage'] = "Adresse email inconnue ou niveau requis trop faible !";
            $_SESSION['alertMessageConfig'] = "danger";
            $req->closeCursor(); 
            header("Location: /R3f@iteV0sJ3u&/");
        }
    }
?>