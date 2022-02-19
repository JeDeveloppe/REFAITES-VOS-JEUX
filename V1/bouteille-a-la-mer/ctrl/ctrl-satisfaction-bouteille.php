<?php
@session_start ();

//requete get obligatoire
if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(!isset($_POST["newValue"]) || !preg_match('#3|4#', $_POST["newValue"])){
        $_SESSION['alertMessage'] = "Donnée manquante...!";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    require_once("../../controles/fonctions/validation_donnees_int.php");
    require("../../config.php");
    require("../../bdd/connexion-bdd.php");
    $newValue = valid_donnees($_POST['newValue']);
    $bouteille = valid_donnees(($_POST['bouteille']));

    //si on confirme que c'est bien
    // 3 = OK
    // 4 = KO 

    $sqlBouteille = $bdd -> prepare("UPDATE bouteille_mer SET actif = ? WHERE idBouteille = ?");
    $sqlBouteille-> execute(array($newValue,$bouteille));

    if($newValue == 3){ //retour contant
        $_SESSION['alertMessage'] = "MERCI du vote !";
        $_SESSION['alertMessageConfig'] = "success";
    }else{ // 4 pas contant
        $_SESSION['alertMessage'] = "Merci du vote !<br/>Nous gardons votre bouteille à la mer !";
        $_SESSION['alertMessageConfig'] = "warning";
    }


    if($bouteille < 10){
        $x = 1;
    }elseif($bouteille > 9 && $bouteille <100){
        $x = 2;
    }elseif($bouteille > 99 && $bouteille <1000){
        $x = 3;
    }

    $urlRetour = substr($_SERVER['HTTP_REFERER'], 0, -8-$x);
    header("Location: ".$urlRetour);
    exit();
    
}else{
    $_SESSION['alertMessage'] = "Mauvaise requête !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}
?>