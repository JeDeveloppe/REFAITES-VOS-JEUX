<?php
@session_start ();
require("../controles/fonctions/adminOnline.php");
require('../config.php');
require('../bdd/connexion-bdd.php');

$sqlClients = $bdd->query("SELECT * FROM clients");
$clients = $sqlClients->fetchAll();

$sqlAlterTable = $bdd->query('ALTER TABLE clients
                            ADD organismeLivraison VARCHAR(80) NULL,
                            ADD organismeFacturation VARCHAR(80) NULL,
                            ADD nomLivraison VARCHAR(50) NULL, 
                            ADD nomFacturation VARCHAR(50) NULL,
                            ADD prenomLivraison VARCHAR(50) NULL,
                            ADD prenomFacturation VARCHAR(50) NULL,
                            ADD adresseLivraison VARCHAR(80) NULL,
                            ADD adresseFacturation VARCHAR(80) NULL,
                            ADD cAdresseLivraison VARCHAR(80) NULL,
                            ADD cAdresseFacturation VARCHAR(80) NULL,
                            ADD cpLivraison VARCHAR(5) NULL,
                            ADD cpFacturation VARCHAR(5) NULL,
                            ADD villeLivraison VARCHAR(50) NULL,
                            ADD villeFacturation VARCHAR(50) NULL,
                            ADD paysLivraison VARCHAR(3) NOT NULL,
                            ADD paysFacturation VARCHAR(3) NOT NULL

');

foreach($clients as $client){
    $nom = $client['nom'];
    $prenom = $client['prenom'];
    $adresse = $client['adresse'];
    $cp = $client['cp'];
    $ville = $client['ville'];
    $pays = $client['pays'];
    $sqlUpdate = $bdd->prepare("UPDATE clients SET 
                    nomLivraison = :nom,
                    nomFacturation = :nom,
                    prenomLivraison = :prenom,
                    prenomFacturation = :prenom,
                    adresseLivraison = :adresse,
                    adresseFacturation = :adresse,
                    cpLivraison = :cp,
                    cpFacturation = :cp,
                    villeLivraison = :ville,
                    villeFacturation = :ville,
                    paysLivraison = :pays,
                    paysFacturation = :pays
                    WHERE idClient = :id ");

    $sqlUpdate->execute(array(
        'nom'      => $nom,
        'prenom'   => $prenom,
        'adresse'  => $adresse,
        'cp'       => $cp,
        'ville'    => $ville,
        'pays'     => $pays,
        'id'       => $client['idClient']
    ));
}

$dropColumns = $bdd->query("ALTER TABLE clients 
                DROP nom,
                DROP prenom,
                DROP adresse,
                DROP cp,
                DROP ville,
                DROP pays
");

echo "TABLE CLIENT A JOUR HAPPY END :)";