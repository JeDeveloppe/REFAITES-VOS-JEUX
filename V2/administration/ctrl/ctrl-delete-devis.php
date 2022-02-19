<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");

if($_SERVER["REQUEST_METHOD"] == "GET"){

    require_once("../../controles/fonctions/validation_donnees.php");
    include('../../config.php');
    include('../../bdd/connexion-bdd.php');

    $devis = valid_donnees($_GET['devis']);

    $sqlLastDevis = $bdd -> query("SELECT * FROM documents WHERE numero_devis != '' ORDER BY idDocument DESC LIMIT 1");
    $donneesLastDevis = $sqlLastDevis-> fetch();

    //si c'est le dernier devis de la base
    if($donneesLastDevis['numero_devis'] == $devis){
        $_SESSION['alertMessage'] = "Suppression impossible car c'est le dernier de la liste...";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: /admin/accueil/");
        exit();
    }else{
        $sqlDevis = $bdd-> prepare("SELECT * FROM documents WHERE numero_devis = ?");
        $sqlDevis-> execute(array($devis));
        $donneesDevis = $sqlDevis -> fetch();

        $sqlDeleteDevis = $bdd-> prepare("DELETE FROM documents WHERE numero_devis = ?");
        $sqlDeleteDevis-> execute(array($devis));

        $sqlDeleteLignesDevis = $bdd-> prepare("DELETE FROM documents_lignes WHERE idDocument = ?");
        $sqlDeleteLignesDevis-> execute(array($donneesDevis['idDocument']));

        $_SESSION['alertMessage'] = "Devis supprimer";
        $_SESSION['alertMessageConfig'] = "success";
        header("Location: /admin/accueil/");
        exit();
    }
}
?>