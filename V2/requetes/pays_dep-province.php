<?php
session_start();

if(isset($_GET['pays']) && $_GET['pays'] != '' ){

    require_once("../config.php");
    require_once("../bdd/connexion-bdd.php");
    require_once("../controles/fonctions/validation_donnees.php");
    
    $pays = valid_donnees($_GET['pays']);

    if($pays == "FR"){
        $table = "villes_france_free";
        $colonne = "ville_departement";

    }elseif($pays == "BE"){
        $table = "villes_belgique_free";
        $colonne = "province";
    }

    $req = $bdd-> query("SELECT DISTINCT $colonne FROM $table ORDER BY $colonne ASC");
    $donnees = $req->fetch();

    echo '<option value="">Choisissez...</option>'; 
    while($donnees){
        echo '<option value="'.mb_strtoupper($donnees[$colonne]).'">'.mb_strtoupper($donnees[$colonne]).'</option>'; 
        $donnees = $req->fetch();
    }

}
?>