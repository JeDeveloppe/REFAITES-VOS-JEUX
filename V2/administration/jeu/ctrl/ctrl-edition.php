<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

   
    require_once("../../../controles/fonctions/validation_donnees.php");
    include('../../../config.php');
    include('../../../bdd/connexion-bdd.php');
    include('../../../bdd/table_config.php');

    $tailleImage = $donneesConfig[2]['valeur'];
    $widthImage = $donneesConfig[3]['valeur'];
    $heightImage = $donneesConfig[4]['valeur'];
    $tva = $donneesConfig[6]['valeur'];

    $nom = valid_donnees(mb_strtoupper($_POST['nom']));
    $editeur1 = valid_donnees($_POST['editeur1']);
    $anneePOST = valid_donnees($_POST['annee']);
    $jeu = valid_donnees($_POST['idDuJeu']);
    $deee = valid_donnees($_POST['deee']);
    $contenuPieces = valid_donnees($_POST['pieces']);
    $messageSpecial = valid_donnees($_POST['messageSpecial']);
    $urlNom = valid_donnees(trim($_POST['urlNom']));
    $jeuCompletLivraison = $_POST['jeuCompletLivraison'];
    $jeuComplet = $_POST['jeuComplet'];
    $poidBoite = valid_donnees($_POST['poidBoite']);
    $age = valid_donnees($_POST['age']);
    $joueurs = valid_donnees($_POST['joueurs']);
    $prixHT = valid_donnees($_POST['prixHT']);

   
    if(strlen($prixHT) <= 3 ){
        $_SESSION['alertMessage'] = "Prix HT trop petit, minimum 4 caractères...";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit(); 
    }else{
        $prixHT = $prixHT / $tva * 100;
    }

    if(empty($editeur1)){
        $_SESSION['alertMessage'] = "L' éditeur ne peut être vide";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();  
    }else{
        $editeur = strtoupper($editeur1);
    }
    
    if($anneePOST == "?"){
        $annee = "Année inconnue";
    }else{
        $annee = $anneePOST;
    }

    //ON MET A JOUR LE CONTENU DES PIECES
    $sqlExistePiece = $bdd->prepare("SELECT * FROM pieces WHERE idJeu = ?");
    $sqlExistePiece->execute(array($jeu));
    $nbExistePiece = $sqlExistePiece->rowCount();

    if($nbExistePiece == 1){
        $sqlUpdatePiece = $bdd -> prepare("UPDATE pieces SET contenu_total = :contenu, message = :message WHERE idJeu = :jeu");
        $sqlUpdatePiece-> execute(array("contenu" => $contenuPieces, "message" => $messageSpecial, "jeu" => $jeu));
    }else{
        $sqlCreationPiece = $bdd -> prepare("INSERT INTO pieces (idJeu,contenu_total,message) VALUES (?,?,?)");
        $sqlCreationPiece-> execute(array($jeu,$contenuPieces,$messageSpecial));
    }


    //ON TESTE L'IMAGE
    if(isset($_FILES["photo"]) && $_FILES["photo"]["size"] > 0){
        if($_FILES["photo"]["error"] == 0){
            $allowed = array("jpg" => "image/jpg", "JPG" => "image/jpg", "JPEG" => "image/jpeg", "jpeg" => "image/jpeg", "GIF" => "image/gif", "gif" => "image/gif", "png" => "image/png", "PNG" => "image/png");
            $filename = $_SESSION['userId']."-".time()."-".$_FILES["photo"]["name"];
            $filetype = $_FILES["photo"]["type"];
            $filesize = $_FILES["photo"]["size"];
        
            // Verify file extension
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(!array_key_exists($ext, $allowed)){
                $_SESSION['alertMessage'] = "Format d'image incorrect !";
                $_SESSION['alertMessageConfig'] = "warning";
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit();  
            }

            // Verify file size - 5MB maximum
            $maxsize = $tailleImage * 1024 * 1024;

            if($filesize > $maxsize){
                $_SESSION['alertMessage'] = "Image trop grande, maximum ".$tailleImage."MB (ou ".$maxsize." octets)!";
                $_SESSION['alertMessageConfig'] = "warning";
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit();
            }


            // Verify MYME type of the file
            if(in_array($filetype, $allowed)){
                $image = $_FILES['photo']['tmp_name']; 
                $imgContent = file_get_contents($image); 

                $imgBase64 = base64_encode($imgContent);

                //optention des dimensions de l'image
                list($width, $height) = getimagesize($image);
                //dimension mini pour affichage correct en extra large voir css
                $minWidth = $widthImage;
                $minHeight = $heightImage;
                if($width < $minWidth || $height < $minHeight){
                    $_SESSION['alertMessage'] = "Image trop petite, taille mini (".$widthImage."px X ".$heightImage."px)";
                    $_SESSION['alertMessageConfig'] = "warning";
                    header("Location: ".$_SERVER['HTTP_REFERER']);
                    exit();

                }


                $sqlImage = $bdd->prepare("UPDATE catalogue SET imageBlob = :imageBlob WHERE idCatalogue = :jeu");
                $sqlImage->execute(array("imageBlob" => $imgBase64, "jeu" => $jeu));
                
            }
        }

    }

    //ON MET A JOUR LE JEU
    $sqlInfosJeu = $bdd -> prepare("UPDATE catalogue SET deee = :deee, nom= :nom, editeur = :editeur, annee = :annee, urlNom = :urlNom, isLivrable = :livrable, isComplet = :complet, poidBoite = :poid, age = :Age, nbrJoueurs = :Joueurs, prix_HT = :prixHT WHERE idCatalogue = :jeu");
    $sqlInfosJeu-> execute(array("deee" => $deee, "nom" => $nom,"editeur" => $editeur,"annee" => $annee, "urlNom" => $urlNom, "jeu" => $jeu, "livrable" => $jeuCompletLivraison, "complet" => $jeuComplet, "poid" => $poidBoite, "Age" => $age, "Joueurs" => $joueurs, "prixHT" => $prixHT));
    

    $_SESSION['alertMessage'] = "Jeu mis à jour!";
    $_SESSION['alertMessageConfig'] = "success";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();  

}
?>