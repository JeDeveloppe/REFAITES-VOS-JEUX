<?php
@session_start ();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    require('../../controles/fonctions/validation_donnees.php');
    require('../../config.php');
    $mail = valid_donnees($_POST["email"]);
    $tokenUser = valid_donnees($_POST["tokenUser"]);
    $password1 = valid_donnees($_POST["password"]);
    $password2 = valid_donnees($_POST["password2"]);

    if (empty($mail)
        || !filter_var($mail, FILTER_VALIDATE_EMAIL) || empty($tokenUser) || empty($password) || empty($password2) || $password1 !== $password2){

        $_SESSION['alertMessage'] = "Donnée manquante...";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }
    else{ //tout est bon

        require('../../config.php');
        require('../../bdd/connexion-bdd.php');

        $req = $bdd->prepare('SELECT * FROM clients WHERE email = :email AND idUser = :token');
        $req->execute(array('email' => $mail, 'token' => $tokenUser));
        $donnees = $req->fetch();

        if(is_array($donnees)){ // si adresse mail existe on peut enregistrer

            //Hachage du mot de passe
            $options = [
                'cost' => $GLOBALS['costHashageMdp'], //nombre de fois renouveller -> config.php
            ];
            $pass_hache = password_hash($password1, PASSWORD_DEFAULT, $options);

            $sqlUpdatePassword = $bdd->prepare("UPDATE clients SET password = ?, userLevel = ? WHERE idUser = ? AND email = ?");
            $sqlUpdatePassword->execute(array($pass_hache, 1, $tokenUser, $mail));
             
            $_SESSION = array();

            session_destroy();

            @session_start ();
            
            $_SESSION['alertMessage'] = "Mot de passe mise à jour, vous pouvez vous connecté(e).";
            $_SESSION['alertMessageConfig'] = "success";
            header("Location: /connexion/");
            exit(); 
            
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