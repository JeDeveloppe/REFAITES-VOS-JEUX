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

    $nom = valid_donnees($_POST['nom']);
    $description = valid_donnees($_POST['description']);
    $partenaire = valid_donnees($_POST['idDuPartenaire']);
    $ville = valid_donnees($_POST['ville']);
    $dep = valid_donnees($_POST['departement']);


    //ON MET A JOUR LE PARTENAIRE
    $sqlInfosJeu = $bdd -> prepare("UPDATE partenaires SET nom= :nom, description = :description, ville = :ville, departement = :dep WHERE idPartenaire = :partenaire");
    $sqlInfosJeu-> execute(array("nom" => $nom,"description" => $description,"partenaire" => $partenaire, "ville" => $ville, "dep" => $dep));


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

                //ON MET A JOUR
                $sqlImage = $bdd -> prepare("UPDATE partenaires SET image = :image WHERE idPartenaire = :partenaire");
                $sqlImage-> execute(array("image" => $imgBase64, "partenaire" => $partenaire));
                
                
            }
        }
    }

    if($_POST['site'] != "" && $_POST['url'] != ""){
        $site = valid_donnees($_POST['site']);
        $url = valid_donnees($_POST['url']);
            $sqlSite = $bdd -> prepare("UPDATE partenaires SET site = :site, url = :url WHERE idPartenaire = :partenaire");
            $sqlSite-> execute(array("site" => $site, "url" => $url, "partenaire" => $partenaire));
    }

    $_SESSION['alertMessage'] = "Partenaire mis à jour !";
    $_SESSION['alertMessageConfig'] = "success";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();  
}
?>