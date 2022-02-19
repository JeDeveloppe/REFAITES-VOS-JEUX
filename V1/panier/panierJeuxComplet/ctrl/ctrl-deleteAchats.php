<?php
@session_start ();

require("../../../config.php");
require("../../../bdd/connexion-bdd.php");


$allAchatsWaiting = $bdd->prepare("SELECT * FROM listeMessages WHERE idUser = ? AND qte > ?");
$allAchatsWaiting->execute(array($_SESSION['sessionId'], 0));  
$donneesAllAchatsWaiting = $allAchatsWaiting->fetchAll();

foreach($donneesAllAchatsWaiting as $ligne){
    $sqlUpdateStockJC = $bdd->prepare("SELECT * FROM jeux_complets WHERE idJeuxComplet = ?");
    $sqlUpdateStockJC->execute(array($ligne['idJeu']));
    $donneesJC = $sqlUpdateStockJC->fetch();

    $nouveauStock = $donneesJC['stock'] + $ligne['qte'];

    $sqlUpdateStockJCAfterCalc = $bdd->prepare("UPDATE jeux_complets SET stock = ? WHERE idJeuxComplet = ?");
    $sqlUpdateStockJCAfterCalc->execute(array($nouveauStock,$ligne['idJeu']));

}

$sqlDelete = $bdd -> prepare("DELETE FROM listeMessages WHERE idUser = ? AND qte > ?");
$sqlDelete-> execute(array($_SESSION['sessionId'],0));


$_SESSION['alertMessage'] = "Liste supprimée !";
$_SESSION['alertMessageConfig'] = "success";
header("Location: /accueil/");
exit();
?>