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
    $content = valid_donnees($_POST['content']);
    $url = valid_donnees($_POST['url']);
    $mois = valid_donnees($_POST['mois']);
    $jour = valid_donnees($_POST['jour']);
    $annee = valid_donnees($_POST['annee']);
    $hrs = valid_donnees($_POST['heure']);
    $min= valid_donnees($_POST['minutes']);

    $timestampPublication = strtotime($jour."-".$mois."-".$annee."-".$hrs.":".$min);


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
            }
        }
    }

    //ON MET DANS LA BASE
    $sqlCreationPartenaire = $bdd -> prepare("INSERT INTO medias (date,titre,content,image,lien,actif) VALUES (?,?,?,?,?,?)");
    $sqlCreationPartenaire-> execute(array($timestampPublication,$nom,$content,$imgBase64,$url,1));
    $lastId = $bdd-> lastInsertId();
    

    $_SESSION['alertMessage'] = "Média créé";
    $_SESSION['alertMessageConfig'] = "success";
    header("Location: /administration/medias/edition-medias.php?media=".$lastId);
    exit();  
}
?>