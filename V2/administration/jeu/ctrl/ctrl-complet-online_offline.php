<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

//requete get obligatoire
if($_SERVER["REQUEST_METHOD"] == "GET"){

    require_once("../../../controles/fonctions/validation_donnees_int.php");

    if(!isset($_GET["newValue"]) || !preg_match('#0|1#', $_GET["newValue"])){
        $_SESSION['alertMessage'] = "Donnée manquante...!";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    $newValue = valid_donnees($_GET['newValue']);
    $jeu = valid_donnees(($_GET['idComplet']));
    $reference = valid_donnees(($_GET['reference']));


    $sql = "UPDATE jeux_complets SET actif = :value WHERE idJeuxComplet = :jeu";
    $data = array('value' => $newValue, 'jeu' => $jeu);
    

    try
    {
        include_once('../../../config.php');
        include_once('../../../bdd/connexion-bdd.php');
        $requete = $bdd -> prepare($sql) ;
        $requete->execute($data) ;

        //pour le sitemap
        // $sqlSitemap = $bdd->prepare("UPDATE sitemaps SET actif  = ? WHERE idJeu = ?");
        // $sqlSitemap->execute(array($newValue,$jeu));


            $_SESSION['alertMessage'] = "Visibilité mise à jour !";
            $_SESSION['alertMessageConfig'] = "success";
            header("Location: ".$_SERVER['HTTP_REFERER']."#".$reference );
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