<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");
include_once("../../../config.php");
$titreDeLaPage = "[ADMIN] - Configuration du site";
$descriptionPage = "";
include_once("../../../bdd/connexion-bdd.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    require("../../../controles/fonctions/validation_donnees.php");

    //on calcul le nombre de valeurs qui existent:
    $valeurs = $_POST['valeur'];
    $noms = $_POST['nom'];

    $nb = count($noms);

    for($i=0;$i<$nb;$i++){
        $sqlUpdateValeur = $bdd->prepare("UPDATE configAdmin SET valeur = ? WHERE nom = ?");
        $sqlUpdateValeur-> execute(array($valeurs[$i],$noms[$i]));
    }
    
    $_SESSION['alertMessage'] = "Changements éffectués !";
    $_SESSION['alertMessageConfig'] = "success";
    header("Location: ".$_SERVER['HTTP_REFERER'] );
    exit(); 
}else{
    $_SESSION['alertMessage'] = "Mauvaise requête !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER'] );
    exit(); 
}
?>