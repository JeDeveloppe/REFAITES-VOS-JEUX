<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
   
    require_once("../../../controles/fonctions/validation_donnees.php");
    include('../../../config.php');
    include('../../../bdd/connexion-bdd.php');
    include('../../../bdd/table_config.php');

    $tva = $donneesConfig[6]['valeur'];
    $malusEtatBoite = $donneesConfig[29]['valeur']*100; //en cents
    $malusEtatMateriel = $donneesConfig[30]['valeur']*100; //en cents
    $malusEtatRegle = $donneesConfig[31]['valeur']*100; //en cents

    // jeu unique à la création
    $qte = 1;
 
    $prixHtReference = valid_donnees($_POST['prixHtReference']);
    $etatBoite = valid_donnees($_POST['etatBoite']);
    $etatMateriel = valid_donnees($_POST['etatMateriel']); 
    $regleJeu = valid_donnees($_POST['regleJeu']);

    if(isset($_POST['prixCommeNeuf']) && !empty($_POST['prixCommeNeuf'])){
        $prixCommeNeuf = valid_donnees($_POST['prixCommeNeuf']) * 100 / $tva;
    
        if($etatBoite == "COMME NEUF" && $etatMateriel == "COMME NEUF" && $regleJeu == "ORIGINALE"){
            $prixHtReference = valid_donnees($_POST['prixCommeNeuf']) * 100 / $tva;
            $isNeuf = 1;
        }else{
            $_SESSION['alertMessage'] = "Tout n'est pas comme neuf...";
            $_SESSION['alertMessageConfig'] = "warning";
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();
        }
    }else{
        if($etatBoite == "ÉTAT MOYEN"){
            $prixHtReference -= $malusEtatBoite;
        }
        if($etatMateriel == "ÉTAT MOYEN"){
            $prixHtReference -= $malusEtatMateriel;
        }
        if($regleJeu == "IMPRIMÉE"){
            $prixHtReference -= $malusEtatRegle;
        }
        $isNeuf = 0;
    }

   
    $description = valid_donnees($_POST['description']);
    $idJeu = valid_donnees($_POST['idJeu']);

    //ON CRE DANS LA BASE DE DONNEE
    $sqlNewJeuComplet = $bdd -> prepare("INSERT INTO jeux_complets (idCatalogue,stock,prixHT,etatBoite,etatMateriel,regleJeu,information,isNeuf,reference,actif) VALUES (:idJeu, :stock, :prix, :boite, :materiel, :regle, :information, :isneuf, :ref, :actif)");
    $sqlNewJeuComplet-> execute(array(
        "idJeu" => $idJeu,
        "stock" => $qte,
        "prix" => $prixHtReference,
        "boite" => $etatBoite,
        "materiel" => $etatMateriel,
        "regle" => $regleJeu,
        "information" => $description,
        "isneuf" => $isNeuf,
        "ref" => 0,
        "actif" => 1));

    $lastId = $bdd->lastInsertId();

    $referenceComplet = $idJeu.'-'.$lastId;

    $sqlUpdateJC = $bdd->prepare("UPDATE jeux_complets SET reference = ? WHERE idJeuxComplet = ?");
    $sqlUpdateJC->execute(array($referenceComplet,$lastId));

    $_SESSION['alertMessage'] = "Jeu complet créé dans la base !";
     $_SESSION['alertMessageConfig'] = "success";
    header("Location: ".$_SERVER['HTTP_REFERER']."#".$idJeu);
    exit(); 

}
?>