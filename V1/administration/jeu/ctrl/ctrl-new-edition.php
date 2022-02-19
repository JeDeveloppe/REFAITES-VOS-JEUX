<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");



if($_SERVER["REQUEST_METHOD"] == "POST"){

   
    require_once("../../../controles/fonctions/validation_donnees.php");
    require_once("../../../controles/fonctions/cleanUrl.php");
    include('../../../config.php');
    include('../../../bdd/connexion-bdd.php');
    include('../../../bdd/table_config.php');

    $tailleImage = $donneesConfig[2]['valeur'];
    $widthImage = $donneesConfig[3]['valeur'];
    $heightImage = $donneesConfig[4]['valeur'];

    
    $nom = valid_donnees(mb_strtoupper($_POST['nom']));
    $urlNom = clean_url($_POST['nom']);
    $editeur1 = valid_donnees($_POST['editeur1']);
    $editeur2 = strtoupper(valid_donnees($_POST['editeur2']));
    $anneePOST = valid_donnees($_POST['annee']);
    $pieces = valid_donnees($_POST['pieces']);
    $jeuCompletLivraison = $_POST['jeuCompletLivraison'];
    $jeuComplet = $_POST['jeuComplet'];
    $poidBoite = valid_donnees($_POST['poidBoite']);
    $age = valid_donnees($_POST['age']);
    $joueurs = valid_donnees($_POST['joueurs']);
    $prixHT = valid_donnees($_POST['prixHT']);


    if(strlen($prixHT) < 4){
        $_SESSION['alertMessage'] = "Prix HT trop petit, minimum 4 caractères...";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit(); 
    }else{
        $prixHT = $prixHT * 100;
    }
 

    if(!empty($editeur1) && !empty($editeur2)){
        $editeur = strtoupper($editeur2);
    }else if(!empty($editeur1)){
        $editeur = strtoupper($editeur1);
    }
    else if(!empty($editeur2)){
     $editeur = strtoupper($editeur2);
    }
    
    if($anneePOST == "?"){
        $annee = "Année inconnue";
    }else{
        $annee = $anneePOST;
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
                    $_SESSION['alertMessage'] = "Image trop petite, taille mini (".$widthImage." X ".$heightImage.")";
                    $_SESSION['alertMessageConfig'] = "warning";
                    header("Location: ".$_SERVER['HTTP_REFERER']);
                    exit();

                }
                
            }
        }
    }

      //ON CRE DANS LA BASE DE DONNEE
      $sqlNewJeu = $bdd -> prepare("INSERT INTO catalogue (nom,editeur,annee,imageBlob,accessoire_idCategorie,actif,isLivrable,isComplet,poidBoite,urlNom,age,nbrJoueurs,prix_HT,createur) VALUES (:nom, :editeur, :annee, :imageBlob, :categorie, :actif, :livrable, :complet, :poid, :urlNom, :age, :joueurs, :prix, :createur)");
      $sqlNewJeu-> execute(array(
          "nom" => $nom,
          "editeur" => $editeur,
          "annee" => $annee,
          "imageBlob" => $imgBase64,
          "categorie" => 0,
          "actif" => 1,
          "livrable"=> $jeuCompletLivraison,
          "complet" => $jeuComplet,
          "poid" => $poidBoite,
          "urlNom" => $urlNom,
          "age" => $age,
          "joueurs" => $joueurs,
          "prix" => $prixHT,
          "createur" => $_SESSION['pseudo']));
  
      //on recupere dernier entree
      $jeu = $bdd->lastInsertId();
  
      //ON CREE LA LIGNE POUR LE CONTENU DES PIECES
      $sqlUpdatePiece = $bdd -> prepare("INSERT INTO pieces (idJeu,contenu_total) VALUES (?,?)");
      $sqlUpdatePiece-> execute(array($jeu,$pieces));
  
    //ICI ON PEUT ENREGISTRER IMAGE  -> SERA SUPPRIMER A LA VERSION 2
    $sqlImageCreate = $bdd-> prepare("INSERT INTO jeu_image (idJeux,image) VALUES (:jeu, :image)");
    $sqlImageCreate-> execute(array("jeu" => $jeu, "image" => $imgBase64));



    $_SESSION['alertMessage'] = "Ok créer!";
    $_SESSION['alertMessageConfig'] = "success";
    header("Location: /administration/jeu/edition.php?etat=offline&jeu=".$jeu);
    exit();  

}
?>