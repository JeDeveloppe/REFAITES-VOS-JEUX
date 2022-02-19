<?php
@session_start ();
if(!isset($_GET['data']) || !is_numeric($_GET['data'])){
    $_SESSION['alertMessage'] = "Image inconnue !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /");
    exit();  
}else{
    require('../controles/fonctions/validation_donnees.php');
    $data = $_GET['data'];

    require('../config.php');
    require('../bdd/connexion-bdd.php');
    $sqlImage = $bdd->prepare("SELECT image FROM jeu_image WHERE idJeux = ?");
    $sqlImage->execute(array($data));
    $donneesImage = $sqlImage->fetch();
    $nbr = $sqlImage->rowCount();

    if($nbr == 1){
        $image = imagecreatefromstring(base64_decode($donneesImage['image']));
        $image = imagescale($image, 250, 250);
        header('Content-Type: image/jpeg');
        echo imagejpeg($image);
        imagedestroy($image);
    }else{
        $_SESSION['alertMessage'] = "Image inconnue !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: /");
        exit();  
    }
}