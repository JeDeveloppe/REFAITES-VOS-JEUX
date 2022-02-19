<?php
@session_start ();
require('../../config.php');
require('../../bdd/connexion-bdd.php');
require('../../controles/fonctions/validation_donnees.php');

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
        $_SESSION['alertMessage'] = "Incohérence état du document - client !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: /");
        exit();  
    }else{

        //a ce niveau j'ai le numero de transaction
        $sqlUpdateDoc = $bdd -> prepare("UPDATE documents SET time_transaction = ?, etat = ? WHERE idDocument = ?");
        $sqlUpdateDoc-> execute(array(time(),0,$donneesDocument['idDocument']));
        header('Location: ./finDeTransaction-abandon.php');
        exit();
    }
}
?>