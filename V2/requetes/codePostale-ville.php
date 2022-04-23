<?php
session_start();

if(isset($_GET['recherche']) && isset($_GET['pays'])){

    require_once("../config.php");
    require_once("../bdd/connexion-bdd.php");
    require_once("../controles/fonctions/validation_donnees.php");
    
    $recherche = valid_donnees(trim($_GET['recherche']));
    $pays = valid_donnees($_GET['pays']);

    if($pays == "FR"){
        $table = "villes_france_free";
        $colonne = "ville_code_postal";
    }elseif($pays == "BE"){
        $table = "villes_belgique_free";
        $colonne = "ville_code_postal";
    }


    $req = $bdd-> prepare("SELECT ville_nom FROM $table WHERE $colonne LIKE ? ORDER BY ville_nom");
    $req-> execute(array($recherche.'%'));
    $donnees = $req->fetch();

    echo '<option value="">Choisissez...</option>'; 
    while($donnees){
        echo '<option value="'.mb_strtoupper($donnees['ville_nom']).'">'.mb_strtoupper($donnees['ville_nom']).'</option>'; 
        $donnees = $req->fetch();
    }

}
?>