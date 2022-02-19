<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

//requete get obligatoire
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $id = $_POST['nouvelId'];
    $doc = $_POST['doc'];
    $vieu = $_POST['vieuClient'];


        include_once('../../../config.php');
        include_once('../../../bdd/connexion-bdd.php');

        $requete1 = $bdd-> prepare("DELETE FROM clients WHERE idClient = :id") ;
        $requete1->execute(array('id' => $vieu));
        $requete2 = $bdd-> prepare("UPDATE documents SET idUser= ? WHERE idDocument = ?") ;
        $requete2->execute(array($id,$doc));


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