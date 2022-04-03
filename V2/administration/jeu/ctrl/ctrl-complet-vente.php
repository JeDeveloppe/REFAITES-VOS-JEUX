<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

//requete get obligatoire
if($_SERVER["REQUEST_METHOD"] == "GET"){

    require_once("../../../controles/fonctions/validation_donnees_int.php");

    $reference = valid_donnees($_GET['reference']);
    $prix = valid_donnees(($_GET['prix']));
    $moyenPaiement = valid_donnees(($_GET['moyenPaiement']));
    $time = time();


    $sql = "UPDATE jeux_complets SET vente = :valeur, actif = :actif, timeVente = :timeVente WHERE reference = :reference";
    $data = array('valeur' => $prix.'|'.$moyenPaiement, 'actif' => 0, 'timeVente' => $time, 'reference' => $reference);
    

    try
    {
        include_once('../../../config.php');
        include_once('../../../bdd/connexion-bdd.php');
        $requete = $bdd -> prepare($sql) ;
        $requete->execute($data) ;

     
        $_SESSION['alertMessage'] = "État du jeu mis à jour !";
        $_SESSION['alertMessageConfig'] = "success";
        header("Location: ".$_SERVER['HTTP_REFERER']."/#".$reference );
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