<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
   
    require_once("../../../controles/fonctions/validation_donnees.php");
    include('../../../config.php');
    include('../../../bdd/connexion-bdd.php');
    include('../../../bdd/table_config.php');

    $malusEtatBoite = $donneesConfig[29]['valeur']*100; //en cents
    $malusEtatMateriel = $donneesConfig[30]['valeur']*100; //en cents
    $malusEtatRegle = $donneesConfig[31]['valeur']*100; //en cents


    $qte = valid_donnees($_POST['qte']);
 
    $prixHtReference = valid_donnees($_POST['prixHtReference']);
    $etatBoite = valid_donnees($_POST['etatBoite']);
        if($etatBoite == "ÉTAT MOYEN"){
            $prixHtReference -= $malusEtatBoite;
        }
    $etatMateriel = valid_donnees($_POST['etatMateriel']); 
        if($etatMateriel == "ÉTAT MOYEN"){
            $prixHtReference -= $malusEtatMateriel;
        }
    $regleJeu = valid_donnees($_POST['regleJeu']);
        if($regleJeu == "IMPRIMÉE"){
            $prixHtReference -= $malusEtatRegle;
        }

    $description = valid_donnees($_POST['description']);
    $idJeu = valid_donnees($_POST['idJeu']);

    //ON CRE DANS LA BASE DE DONNEE
    $sqlNewJeuComplet = $bdd -> prepare("INSERT INTO jeux_complets (idCatalogue,stock,prixHT,etatBoite,etatMateriel,regleJeu,information,actif,reference) VALUES (:idJeu, :stock, :prix, :boite, :materiel, :regle, :information, :actif, :ref)");
    $sqlNewJeuComplet-> execute(array(
        "idJeu" => $idJeu,
        "stock" => $qte,
        "prix" => $prixHtReference,
        "boite" => $etatBoite,
        "materiel" => $etatMateriel,
        "regle" => $regleJeu,
        "information" => $description,
        "actif" => 1,
        "ref" => 0));
    
    $lastId = $bdd->lastInsertId();


        $referenceComplet = $idJeu.'-'.$lastId;
    
        $sqlUpdateJC = $bdd->prepare("UPDATE jeux_complets SET reference = ? WHERE idJeuxComplet = ?");
        $sqlUpdateJC->execute(array($referenceComplet,$lastId));
  
    $_SESSION['alertMessage'] = "Jeu complet créé dans la base !";
     $_SESSION['alertMessageConfig'] = "success";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit(); 

}
?>