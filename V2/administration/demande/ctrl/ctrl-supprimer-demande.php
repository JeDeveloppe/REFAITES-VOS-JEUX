<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");
include('../../../config.php');
include('../../../bdd/connexion-bdd.php');
include('../../../bdd/table_config.php');
include('../../../controles/fonctions/validation_donnees.php');

$client = valid_donnees($_GET['client']);
$panier = valid_donnees($_GET['panier']);

$sqlDELETEDemande = $bdd-> prepare("DELETE FROM listeMessages WHERE idUser = (SELECT idUser FROM clients WHERE idClient = ?) AND panierKey = ?");
$sqlDELETEDemande-> execute(array($client,$panier));

$_SESSION['alertMessage'] = "Demande supprimée !";
$_SESSION['alertMessageConfig'] = "success";
header("Location: /admin/accueil/");
exit();
?>