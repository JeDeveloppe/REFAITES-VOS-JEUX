<?php
session_start();

if(isset($_GET['recherche']) && isset($_GET['pays'])){

    require_once("../config.php");
    require_once("../bdd/connexion-bdd.php");
    require_once("../controles/fonctions/validation_donnees.php");
    
    $recherche = valid_donnees(trim($_GET['recherche']));
    $pays = valid_donnees($_GET['pays']);

    if($pays == "FR"){
        $req = $bdd-> prepare("SELECT ville_id, ville_nom FROM villes_france_free WHERE ville_code_postal LIKE ? ORDER BY ville_nom");
        $req-> execute(array($recherche.'%'));
        $donnees = $req->fetch();

        echo '<option value="">Choisir une ville de FRANCE</option>'; 
        while($donnees){
            echo '<option value="'.$donnees['ville_id'].'">'.$donnees['ville_nom'].'</option>'; 
            $donnees = $req->fetch();
        }

    }else{
        $req = $bdd-> prepare("SELECT ville_id, ville_nom FROM villes_belgique_free WHERE province LIKE ? ORDER BY ville_nom");
        $req-> execute(array($recherche.'%'));
        $donnees = $req->fetch();

        echo '<option value="">Choisir une ville de BELGIQUE</option>'; 
        while($donnees){
            echo '<option value="'.$donnees['ville_id'].'">'.mb_strtoupper($donnees['ville_nom']).'</option>'; 
            $donnees = $req->fetch();
        }
    }

}
?>