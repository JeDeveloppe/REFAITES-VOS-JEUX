<?php
@session_start ();

if(!isset($_GET['id'])){
    $_SESSION['alertMessage'] = "Information manquante !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}else{
    require('../../controles/fonctions/validation_donnees.php');

    $id = valid_donnees($_GET['id']);

    if(is_numeric($id)){
        require("../../config.php");
        require("../../bdd/connexion-bdd.php");
        $sqlVerifId = $bdd -> prepare("SELECT * FROM listeMessages WHERE idListeMessages = ? AND idUser = ?");
        $sqlVerifId-> execute(array($id,$_SESSION['sessionId']));
        $count = $sqlVerifId -> rowCount();

            //si y a un resultat unique on détruit comme demander
            if($count == 1){
                $sqlDelete = $bdd -> prepare("DELETE FROM listeMessages WHERE idListeMessages = ?");
                $sqlDelete-> execute(array($id));

                $sqlDeleteImageExemple = $bdd -> prepare("DELETE FROM listeMessages_images WHERE idListeMessages = ?");
                $sqlDeleteImageExemple-> execute(array($id));

                $_SESSION['alertMessage'] = "Ligne supprimée !";
                $_SESSION['alertMessageConfig'] = "success";
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit();
            }else{
                $_SESSION['alertMessage'] = "Ligne inconnue !";
                $_SESSION['alertMessageConfig'] = "warning";
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit();
            }
        
    }else{
        $_SESSION['alertMessage'] = "Ce n'est pas un nombre !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>