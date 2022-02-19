<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

//requete get obligatoire
if($_SERVER["REQUEST_METHOD"] == "GET"){

    require_once("../../../controles/fonctions/validation_donnees_int.php");

    if(!isset($_GET["newValue"]) || !preg_match('#2|3#', $_GET["newValue"])){
        $_SESSION['alertMessage'] = "Donnée manquante...!";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    $newValue = valid_donnees($_GET['newValue']);
    $doc = valid_donnees(($_GET['doc']));

    $sql = "UPDATE documents SET etat = :value WHERE idDocument = :doc";
    $data = array('value' => $newValue, 'doc' => $doc);

    try
    {
        include_once('../../../config.php');
        include_once('../../../bdd/connexion-bdd.php');
        $requete = $bdd -> prepare($sql) ;
        $requete->execute($data) ;

            if($newValue == 3){
                $_SESSION['alertMessage'] = "Document mis en attente d'expédition !"; 
            }elseif($newValue == 2){
                $_SESSION['alertMessage'] = 'Document remis en statut "A expédier" !'; 
            }
            $_SESSION['alertMessageConfig'] = "success";
            header("Location: ".$_SERVER['HTTP_REFERER'] );
            exit(); 
        
    }
    catch(Exception $e){
        // en cas d'erreur :
        $_SESSION['alertMessage'] = $e->getMessage();
        $_SESSION['alertMessage-details'] = $data;
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: ../../../erreurs/500.php");
        exit(); 
    }
}else{
    $_SESSION['alertMessage'] = "Mauvaise requête !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
}
?>