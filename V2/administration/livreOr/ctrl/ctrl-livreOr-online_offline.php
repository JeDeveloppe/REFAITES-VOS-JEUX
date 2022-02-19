<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

//requete get obligatoire
if($_SERVER["REQUEST_METHOD"] == "GET"){

    require_once("../../../controles/fonctions/validation_donnees_int.php");
    require("../../../config.php");
    require("../../../bdd/connexion-bdd.php");

    if(!isset($_GET["newValue"]) || !preg_match('#0|1#', $_GET["newValue"])){
        $_SESSION['alertMessage'] = "Donnée manquante...!";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    $newValue = valid_donnees($_GET['newValue']);
    $livre = valid_donnees(($_GET['idLivre']));
    $message = valid_donnees(($_GET['message']));

    //SI GET VAUT 1 c'est soit creation soit mise a jour
    if($newValue == 1){

        $sqlLivre = $bdd -> prepare("UPDATE livreOr SET actif = :value WHERE idLivre = :livre");
        $sqlLivre -> execute(array('value' => $newValue, 'livre' => $livre));

        $sqlMessageLivre = $bdd -> prepare("SELECT * FROM livreOr_messages WHERE idLivre = ?");
        $sqlMessageLivre-> execute(array($livre));
        $donneesMessageLivre = $sqlMessageLivre-> fetch();
        $count = $sqlMessageLivre -> rowCount();
            //si ca existe c'est une mise a jour
            if($count == 1){
                $sqlUpdateLivreMessage = $bdd-> prepare("UPDATE livreOr_messages SET message = ? WHERE idLivre = ?");
                $sqlUpdateLivreMessage-> execute(array($message,$livre));
            }else{
                //il faut creer si message n'est pas vide
                if(!empty($message)){
                $sqlNewLivreMessage = $bdd-> prepare("INSERT INTO livreOr_messages (idLivre,message) VALUES (?,?)");
                $sqlNewLivreMessage-> execute(array($livre,$message));
                }
            }

        $_SESSION['alertMessage'] = "Message mis en ligne !";
        $_SESSION['alertMessageConfig'] = "success";
        header("Location: ".$_SERVER['HTTP_REFERER'] );
        exit(); 

    }else{
        //sinon on supprimer tout
        $sqlDeleteLivre = $bdd -> prepare("DELETE FROM livreOr WHERE idLivre = ?");
        $sqlDeleteLivre-> execute(array($livre));

        $sqlDeleteMessageLivre = $bdd -> prepare("DELETE FROM livreOr_messages WHERE idLivre = ?");
        $sqlDeleteMessageLivre-> execute(array($livre));

        $_SESSION['alertMessage'] = "Message supprimé !";
        $_SESSION['alertMessageConfig'] = "success";
        header("Location: ".$_SERVER['HTTP_REFERER'] );
        exit(); 
    }
}else{
    $_SESSION['alertMessage'] = "Mauvaise requête !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
}



?>