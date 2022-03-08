<?php
@session_start ();
require("../controles/fonctions/adminOnline.php");
require('../config.php');
require('../bdd/connexion-bdd.php');

$sqlvilles = $bdd->query("SELECT * FROM villes_france_free WHERE LENGTH(ville_code_postal) > 5");
$villes = $sqlvilles->fetchAll();



foreach($villes as $ville){
    $cps = explode("-",$ville['ville_code_postal']);
    $init = 1;
    foreach($cps as $cp){
        $sqlUpdate = $bdd->prepare("INSERT INTO villes_france_free (ville_departement,ville_slug,ville_nom,name,ville_code_postal,lng,lat,actif) VALUES (?,?,?,?,?,?,?,?) ");
        $sqlUpdate->execute(array($ville['ville_departement'], $ville['ville_slug'].$init, $ville['ville_nom'], $ville['name'], $cp, $ville['lng'], $ville['lat'], 0));
        $init += 1;
    }
    $delete = $bdd->query("DELETE FROM villes_france_free WHERE ville_id = ".$ville['ville_id']);
}



echo "TABLE VILLES A JOUR HAPPY END :)";


