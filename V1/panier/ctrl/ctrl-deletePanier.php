<?php
@session_start ();

require("../../config.php");
require("../../bdd/connexion-bdd.php");

$sqlVerif = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ?");
$sqlVerif-> execute(array($_SESSION['sessionId']));
$donneesVerif = $sqlVerif -> fetch();

while($donneesVerif){
    $sqlDeleteImageExemple = $bdd -> prepare("DELETE FROM listeMessages_images WHERE idListeMessages = ?");
    $sqlDeleteImageExemple-> execute(array($donneesVerif['idListeMessages']));
    $donneesVerif = $sqlVerif -> fetch();
}


$sqlDelete = $bdd -> prepare("DELETE FROM listeMessages WHERE idUser = ?");
$sqlDelete-> execute(array($_SESSION['sessionId']));


$_SESSION['alertMessage'] = "Liste supprimée !";
$_SESSION['alertMessageConfig'] = "success";
header("Location: /accueil/");
exit();
?>