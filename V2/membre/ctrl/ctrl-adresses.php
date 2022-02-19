<?php
@session_start ();
require_once('../../controles/fonctions/memberOnline.php');

// On vérifie que la méthode POST est utilisée
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    include_once("../../config.php");
    include_once("../../bdd/connexion-bdd.php");
    require_once("../../controles/fonctions/validation_donnees.php");
                
    if($_POST['formName'] == "facturation"){
        $formulaire = "facturation";
        $column = "Facturation";
        $retourId = "secteurfacturation";
    }else{
        $formulaire = "livraison";
        $column = "Livraison";
        $retourId = "secteurlivraison";
    }

    $organisme = valid_donnees(mb_strtoupper($_POST['organisme-'.$formulaire]));
    $nom = valid_donnees(mb_strtoupper($_POST['nom-'.$formulaire]));
    $prenom = valid_donnees(ucfirst(strtolower($_POST['prenom-'.$formulaire])));
    $adresse = valid_donnees($_POST['adresse-'.$formulaire]);
    $cadresse = valid_donnees($_POST['cadresse-'.$formulaire]);
    $cp = valid_donnees(strtoupper($_POST['cp-'.$formulaire]));
    $ville = valid_donnees(mb_strtoupper($_POST['ville-'.$formulaire]));
    $pays = valid_donnees(mb_strtoupper($_POST['pays-'.$formulaire]));



    //si une variable est vide on revient en arrière
    if($prenom == "" ||
        $nom == "" ||
        $adresse == "" ||
        $cp == "" ||
        $ville == "" ||
        $pays == ""){

        $_SESSION['alertMessage'.$formulaire] = "Il manque une saisie !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']."#".$retourId);
        exit();
    }

    //on controle chaque champs si on a le format attendu
    //prenom,nom,ville,pays inférieur à 50
    if(strlen($organisme) > 80 | strlen($prenom) > 50 || strlen($nom) > 50 || strlen($ville) > 30 || strlen($pays) > 2 || strlen($cp) > 5 ){
        $_SESSION['alertMessage'.$formulaire] = "Une saisie est trop longue!";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']."#".$retourId);
        exit();   
    }

    //si FR le code postal doit etre de 5
    if($pays == "FR" && strlen($cp) < 5 ){
        $_SESSION['alertMessage'.$formulaire] = "Code postal Français, mettre 5 chiffres !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']."#".$retourId);
        exit();   
    }

    //adresse supérieur à 80
    if(strlen($adresse) > 80){
        $_SESSION['alertMessage'.$formulaire] = "Adresse trop longue !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']."#".$retourId);
        exit();   
    }
    //complement adresse supérieur à 80
    if(!empty($cadresse) && strlen($cadresse) > 80){
        $_SESSION['alertMessage'.$formulaire] = "Complément d'adresse trop long !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']."#".$retourId);
        exit();   
    }

    try{
        $sqlAdresse = $bdd->prepare("UPDATE clients SET organisme$column = ?, nom$column = ?, prenom$column = ?, adresse$column = ?, cAdresse$column = ?, cp$column = ?, ville$column = ?, pays$column = ? WHERE idUser = ?");
        $sqlAdresse->execute(array($organisme,$nom,$prenom,$adresse,$cadresse,$cp,$ville,$pays,$_SESSION['sessionId']));

        $_SESSION['alertMessage'.$formulaire] = "Adresse mise à jour !";
        $_SESSION['alertMessageConfig'] = "success";
        header("Location: ".$_SERVER['HTTP_REFERER']."#".$retourId);
        exit();  
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
    }

}else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /"); 
    exit(); 
}
?>