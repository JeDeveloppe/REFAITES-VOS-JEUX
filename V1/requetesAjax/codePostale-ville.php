<?php
session_start();

if(isset($_GET['recherche'])){

    require_once("../config.php");
    require_once("../bdd/connexion-bdd.php");
    require_once("../controles/fonctions/validation_donnees.php");
    
    $recherche = valid_donnees(trim($_GET['recherche']));

    $req = $bdd-> prepare("SELECT DISTINCT Ville FROM CodePostauxVilles WHERE Code_postal LIKE ? ORDER BY Ville");
    $req-> execute(array($recherche.'%'));
    $donnees = $req->fetch();

    while($donnees){
        echo '<div class="col-12">'.$donnees['Ville'].'</div>'; 
        $donnees = $req->fetch();
    }
}
?>