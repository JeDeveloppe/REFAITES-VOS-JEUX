<?php
@session_start ();
require_once('../controles/fonctions/memberOnline.php');

// On vérifie que la méthode POST est utilisée
if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['client'])){
    include_once("../config.php");
    include_once("../bdd/connexion-bdd.php");
    require_once("../controles/fonctions/validation_donnees.php");

    $client = valid_donnees($_GET['client']);

        $sqlClientExist = $bdd->prepare("SELECT * FROM clients WHERE idUser = ?");
        $sqlClientExist->execute(array($client));
        $nbr = $sqlClientExist->rowCount();

        if($nbr == 1){
            $sqlDeleteClient = $bdd->prepare("DELETE FROM clients WHERE idUser = ?");
            $sqlDeleteClient->execute(array($client));

            session_destroy(); // on detruit la session
            
            @session_start (); 
            $_SESSION['alertMessage'] = "Votre compte à été supprimé !";
            $_SESSION['alertMessageConfig'] = "success";
            header("Location: /"); 
            exit(); 
        }else{
            $_SESSION['alertMessage'] = "Utilisateur introuvable ou non unique...";
            $_SESSION['alertMessageConfig'] = "warning";
            header("Location: /"); 
            exit(); 
        }


}else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /"); 
    exit(); 
}
?>