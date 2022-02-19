<?php
@session_start ();

// On vérifie que la méthode POST est utilisée
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    require_once("../../../controles/fonctions/validation_donnees.php");
                
    $prenom = ucfirst(strtolower(valid_donnees($_POST['prenom'])));
    $nom = mb_strtoupper(valid_donnees($_POST['nom']));
    $adresse = valid_donnees($_POST['adresse']);
    $cp = strtoupper(valid_donnees($_POST['cp']));
    $ville = mb_strtoupper(valid_donnees($_POST['ville']));
    $pays = mb_strtoupper(valid_donnees($_POST['pays']));
    $id = valid_donnees($_POST['idDuClient']);
 
    //si une session est vide on revient en arrière
    if($prenom == "" ||
        $nom == "" ||
        $adresse == "" ||
        $cp == "" ||
        $ville == "" ||
        $pays == "" ){

        $_SESSION['alertMessage'] = "Aucun champs ne peut être vide !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }
    
    //on controle chaque champs si on a le format attendu
    //prenom,nom,ville,pays inférieur à 50
    if(strlen($prenom) > 50 || strlen($nom) > 50 || strlen($ville) > 30 || strlen($pays) > 2){

        $_SESSION['alertMessage'] = "Une saisie est trop longue (max (30 ville - 50 nom/prenom) caractères)";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();   
    }

    //adresse supérieur à 80
    if(strlen($adresse) > 80){
        $_SESSION['alertMessage'] = "Adresse trop longue (max 80 caractères)";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();   
    }

    //CODES POSTAUX EN FONCTION DES PAYS
    //Allemagne
    if($pays == "DE"){
        if(!preg_match('#^W-([0-9]{4})$#', $cp)){
            $_SESSION['alertMessage'] = "Saisie du code postale incorrect !";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();   
        } 
    }
    //France, Espagne, Italie, Monaco, DOM-TOM / 5 chiffres
    $codepostaux5chiffres = array("FR","ES","IT","MC","YT","GF","GP","MQ","RE");
    if(in_array($pays, $codepostaux5chiffres)){
        if(!preg_match('#^[0-9]{5}$#', $cp)){
            $_SESSION['alertMessage'] = "Saisie du code postale incorrect !";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();   
        } 
    }
    //Belgique, Luxembourg et Suisse / 4 chiffres
    $codepostaux4chiffres = array("BE","LU","CH");
    if(in_array($pays, $codepostaux4chiffres)){
        if(!preg_match('#^[0-9]{4}$#', $cp)){
            $_SESSION['alertMessage'] = "Saisie du code postale incorrect pour ".$pays."!";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();   
        } 
    }
    //Andorre : format AD-3 chiffres
    if($pays == "AD"){
        if(!preg_match('#^AD([0-9]{3})$#', $cp)){
            $_SESSION['alertMessage'] = "Saisie du code postale incorrect pour AD !";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();   
        } 
    }
    //Royaume-Uni
    if($pays == "GB"){
        if(!preg_match('#^[A-Z]{1,2}[0-9][A-Z0-9]? ?[0-9][A-Z]{2}$#', $cp)){
            $_SESSION['alertMessage'] = "Saisie du code postale incorrect !";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();   
        } 
    }



    //A CE NIVEAU TOUT EST OK ON METS A JOUR LE CLIENT
    require("../../../config.php");
    require("../../../bdd/connexion-bdd.php");

    try{
    $sqlSaveClient = $bdd -> prepare("UPDATE clients SET nomFacturation = ?, prenomFacturation = ?, adresseFacturation = ?, cpFacturation = ?, villeFacturation = ?, paysFacturation = ? WHERE idUser = ?");
    $sqlSaveClient-> execute(array($nom,$prenom,$adresse,$cp,$ville,$pays,$id));
    }
    catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
    }

    $_SESSION['alertMessage'] = "Fiche client mise à jour !";
    $_SESSION['alertMessageConfig'] = "success";
    header("Location: ".$_SERVER['HTTP_REFERER']); 

        
}else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /");  
}
?>