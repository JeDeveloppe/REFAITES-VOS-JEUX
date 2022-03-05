<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

//requete get obligatoire
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $id = $_POST['nouvelId'];
    $doc = $_POST['doc'];

        include_once('../../../config.php');
        include_once('../../../bdd/connexion-bdd.php');

        $sqlNouveauClient = $bdd->query("SELECT * FROM clients WHERE idClient = ".$id);
        $donneesClient = $sqlNouveauClient->fetch();
        $adresse_livraison = $organismeLivraison.$donneesClient['nomLivraison'].' '.$donneesClient['prenomLivraison'].'<br/>'.$donneesClient['adresseLivraison'].'<br/>'.$donneesClient['cpLivraison'].' '.$donneesClient['villeLivraison'].'<br/>'.$donneesClient['paysLivraison'];
        $adresse_facturation = $organismeFacturation.$donneesClient['nomFacturation'].' '.$donneesClient['prenomFacturation'].'<br/>'.$donneesClient['adresseFacturation'].'<br/>'.$donneesClient['cpFacturation'].' '.$donneesClient['villeFacturation'].'<br/>'.$donneesClient['paysFacturation'];

        $requete2 = $bdd-> prepare("UPDATE documents SET idUser = ?, adresse_livraison = ?, adresse_facturation = ? WHERE idDocument = ?") ;
        $requete2->execute(array($id,$adresse_livraison,$adresse_facturation,$doc));


        $_SESSION['alertMessage'] = "Penser à supprimer l'ancien client si nécessaire !";
        $_SESSION['alertMessageConfig'] = "success";
        header("Location: ".$_SERVER['HTTP_REFERER'] );
        exit(); 
        

}else{
    $_SESSION['alertMessage'] = "Mauvaise requête !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
}



?>