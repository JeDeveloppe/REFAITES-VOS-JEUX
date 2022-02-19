<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

if($_SERVER["REQUEST_METHOD"] == "GET"){

    require_once("../../../controles/fonctions/validation_donnees.php");
    include('../../../config.php');
    include('../../../bdd/connexion-bdd.php');

    $partenaire = valid_donnees($_GET['partenaire']);


    //ON MET A JOUR LE PARTENAIRE
    $sqlDeletePartenaire = $bdd -> prepare("DELETE FROM partenaires WHERE idPartenaire = ?");
    $sqlDeletePartenaire-> execute(array($partenaire));

    $_SESSION['alertMessage'] = "Partenaire supprimer !";
    $_SESSION['alertMessageConfig'] = "success";
    header("Location: /partenaires/");
    exit();  
}
?>