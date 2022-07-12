<?php
@session_start ();
require("../controles/fonctions/adminOnline.php");
require('../config.php');
require('../bdd/connexion-bdd.php');

$sqlDocuments = $bdd->query("SELECT * FROM documents");
$documents = $sqlDocuments->fetchAll();

foreach($documents as $document){

    $sqlDLA = $bdd->prepare("SELECT * FROM documents_lignes_achats WHERE idDocument = ?");
    $sqlDLA->execute(array($document['idDocument']));
    $donneesLigneAchat = $sqlDLA->fetchAll();

    foreach($donneesLigneAchat as $donne){
        $sqlUpdateJeuxComplet = $bdd->prepare("UPDATE jeux_complets SET vente = ?,timeVente = ?, actif = 0 WHERE idJeuxComplet = ?");
        $sqlUpdateJeuxComplet->execute(array('|CB',$document['time_transaction'],$donne['idJeuComplet']));
    }
}


echo "TABLE DOC ACHAT LIGNE UPDATE OK";