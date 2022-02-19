<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

if($_SERVER["REQUEST_METHOD"] == "GET"){
    $id = $_GET['nouvelId'];

        include_once('../../../config.php');
        include_once('../../../bdd/connexion-bdd.php');

        $requete1 = $bdd-> prepare("DELETE FROM clients WHERE idClient = :id") ;
        $requete1->execute(array('id' => $id));
  
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