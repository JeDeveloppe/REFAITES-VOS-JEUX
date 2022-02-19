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

    
    $nom = mb_strtoupper(valid_donnees($_POST['nom']));
    $categorie = valid_donnees($_POST['categorie']);
    $description = valid_donnees($_POST['description']);
    $messageSpecial = valid_donnees($_POST['messageSpecial']);
    $urlNom = valid_donnees(trim($_POST['urlNom']));
    $idCatalogue = valid_donnees($_POST['idCatalogue']);


    //ON MET A JOUR L' accessoire
    $sqlInfosJeu = $bdd -> prepare("UPDATE catalogue SET nom= :nom, urlNom = :urlNom, accessoire_idCategorie = :categorie WHERE idCatalogue = :catalogue");
    $sqlInfosJeu-> execute(array("nom" => $nom, "urlNom" => $urlNom, "categorie" =>  $categorie, "catalogue" => $idCatalogue));

    //ON MET A JOUR LA DESCRIPTION DE L'ARTICLE
    $sqlUpdatePiece = $bdd -> prepare("UPDATE pieces SET contenu_total = :contenu, message = :message WHERE idJeu = :catalogue");
    $sqlUpdatePiece-> execute(array("contenu" => $description, "message" => $messageSpecial, "catalogue" => $idCatalogue));

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


                //ICI ON PEUT ENREGISTRER IMAGE SI DEJA RENSEIGNE SINON ON CREE
                $sqlImageExiste = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux =". $idCatalogue);
                $donneesImageExiste = $sqlImageExiste -> fetch();
                $nbrImage = $sqlImageExiste-> rowCount();

                if($nbrImage < 1){
                    $sqlImageCreate = $bdd-> prepare("INSERT INTO jeu_image (idJeux,image,image_type) VALUES (?,?,?)");
                    $sqlImageCreate-> execute(array($idCatalogue,$imgBase64,$filetype));
                }else{
                    $sqlImage = $bdd -> prepare("UPDATE jeu_image SET image = :image, image_type = :type WHERE idJeux = :accessoire");
                    $sqlImage-> execute(array("image" => $imgBase64, "type" => $filetype ,"accessoire" => $idCatalogue));
                }
                
            }
        }
    }



  

    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();  

}
?>