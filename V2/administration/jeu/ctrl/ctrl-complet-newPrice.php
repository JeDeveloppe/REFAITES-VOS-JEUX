<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

//requete get obligatoire
if($_SERVER["REQUEST_METHOD"] == "GET"){

    require_once("../../../controles/fonctions/validation_donnees_int.php");

    if(!isset($_GET["nvPrixTTC"]) OR !isset($_GET['idComplet']) OR empty($_GET["nvPrixTTC"]) ){
        $_SESSION['alertMessage'] = "Donnée manquante...!";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    $jeu = valid_donnees(($_GET['idComplet']));

    try
    {
        include_once('../../../config.php');
        include_once('../../../bdd/connexion-bdd.php');
        include('../../../bdd/table_config.php');
        $tva = $donneesConfig[6]['valeur'];

        $prixHt = valid_donnees($_GET["nvPrixTTC"]) * 100 / $tva;


        $sql = $bdd->prepare("SELECT * FROM jeux_complets WHERE idJeuxComplet = ?");
        $sql->execute(array($jeu));
        $donneesJeu = $sql->fetch();
    
        $sqlUpdate = "UPDATE jeux_complets SET prixHT = :prix, ancienPrixHT = :ancienPrix WHERE idJeuxComplet = :jeu";
        $dataUpdate = array('prix' => $prixHt,'ancienPrix' => $donneesJeu['prixHT'], 'jeu' => $jeu);

        $requeteUpdate = $bdd -> prepare($sqlUpdate) ;
        $requeteUpdate->execute($dataUpdate) ;

        //pour le sitemap
        // $sqlSitemap = $bdd->prepare("UPDATE sitemaps SET actif  = ? WHERE idJeu = ?");
        // $sqlSitemap->execute(array($newValue,$jeu));


            $_SESSION['alertMessage'] = "Nouveau prix mis en place !";
            $_SESSION['alertMessageConfig'] = "success";
            header("Location: ".$_SERVER['HTTP_REFERER'].'#'.$jeu );
            exit(); 
        
    }
    catch(Exception $e){
        // en cas d'erreur :
        $_SESSION['alertMessage'] = $e->getMessage();
        $_SESSION['alertMessage-details'] = $data;
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: ../../../erreurs/500.php");
        exit(); 
    }
}else{
    $_SESSION['alertMessage'] = "Mauvaise requête !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
}
?>