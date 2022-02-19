<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

if($_SERVER["REQUEST_METHOD"] == "GET"){

    require_once("../../../controles/fonctions/validation_donnees.php");
    include('../../../config.php');
    include('../../../bdd/connexion-bdd.php');

    $media = valid_donnees($_GET['media']);


    //ON MET A JOUR LE PARTENAIRE
    $sqlDeleteMedia = $bdd -> prepare("DELETE FROM medias WHERE idMedia = ?");
    $sqlDeleteMedia-> execute(array($media));

    $_SESSION['alertMessage'] = "Média supprimé !";
    $_SESSION['alertMessageConfig'] = "success";
    header("Location: /on-en-parle/medias/");
    exit();  
}
?>