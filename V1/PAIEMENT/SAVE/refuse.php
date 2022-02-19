<?php
@session_start ();
require('../config.php');
require('../bdd/connexion-bdd.php');
require('../controles/fonctions/validation_donnees.php');

if(!isset($_GET['doc']) || !isset($_GET['user'])){
    $_SESSION['alertMessage'] = "Il manque une variable utile...";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /");
    exit();
}else{

    $validKey = valid_donnees($_GET['doc']);
    $user = valid_donnees($_GET['user']);

    $sqlDocExiste = $bdd -> prepare("SELECT * FROM documents WHERE validKey = ? AND idUser = ? AND etat = ?");
    $sqlDocExiste-> execute(array($validKey,$user,1));
    $donneesDocument = $sqlDocExiste-> fetch();
    $count = $sqlDocExiste-> rowCount();

    if($count != 1){
        $_SESSION['alertMessage'] = "Incohérence de l'état du document !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: /");
        exit();  
    }else{

        //mise a jour etat du document
        $sqlUpdateDelete = $bdd -> prepare("UPDATE documents SET etat = ? WHERE validKey = ? AND iduser = ?");
        $sqlUpdateDelete-> execute(array(0,$validKey,$user));

        //suppression des donnees client
        $sqlDeleteClient = $bdd -> prepare("DELETE FROM clients WHERE idClient = ?");
        $sqlDeleteClient-> execute(array($user));
       
        //REDIRECTION VERS paiement/fin-de-transaction/
        $_SESSION['alertMessage'] = "<p>Commande annulée !</p><p>Vos données ont été supprimées automatiquement !</p>";
        header("Location: /paiement/fin-de-transaction/");
        exit();  


    }
}
?>