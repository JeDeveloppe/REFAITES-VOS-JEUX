<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

if($_SERVER["REQUEST_METHOD"] == "GET"){
    $client = $_GET['client'];

        include_once('../../../config.php');
        include_once('../../../bdd/connexion-bdd.php');
  
        $sqlDeleteClient = $bdd->prepare("DELETE FROM clients WHERE idUser = ?");
        $sqlDeleteClient->execute(array($client));
        $sqlDeleteClientLivraison = $bdd->prepare("DELETE FROM clients_livraison WHERE idUser = ?");
        $sqlDeleteClientLivraison->execute(array($client));
        $sqlDeleteClientFacturation = $bdd->prepare("DELETE FROM clients_facturation WHERE idUser = ?");
        $sqlDeleteClientFacturation->execute(array($client));

        $_SESSION['alertMessage'] = "Ok !";
        $_SESSION['alertMessageConfig'] = "success";
        header("Location: ".$_SERVER['HTTP_REFERER'] );
        exit(); 
        

}else{
    $_SESSION['alertMessage'] = "Mauvaise requête !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
}



?>