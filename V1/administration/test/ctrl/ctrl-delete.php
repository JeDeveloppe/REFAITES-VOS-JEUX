<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

if($_SERVER["REQUEST_METHOD"] == "GET"){

    require_once("../../../controles/fonctions/validation_donnees.php");
    include('../../../config.php');
    include('../../../bdd/connexion-bdd.php');

    $urlId = valid_donnees($_GET['id']);


    //ON MET A JOUR LE PARTENAIRE
    $sqlDeleteUrl = $bdd -> prepare("DELETE FROM sitemaps WHERE idSitemaps = ?");
    $sqlDeleteUrl-> execute(array($urlId));

    $_SESSION['alertMessage'] = "Url supprimée !";
    $_SESSION['alertMessageConfig'] = "success";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();  
}
?>