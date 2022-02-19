<?php
@session_start ();

require("../../config.php");
require("../../bdd/connexion-bdd.php");

//ON SUPPRIMER LES JEUX OCCASION
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

//ON SUPPRIME LES DEMANDES
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


$_SESSION['alertMessage'] = "Panier supprimé !";
$_SESSION['alertMessageConfig'] = "success";
header("Location: /");
exit();
?>