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
    $pays = valid_donnees($_POST['pays']);
    $dep = valid_donnees($_POST['departement']);
    $ville = valid_donnees($_POST['ville']);
    $url = valid_donnees($_POST['url']);
    $don = valid_donnees($_POST['don']);
    $description = valid_donnees($_POST['description']);
    $collecte = valid_donnees($_POST['collecte']);
    $vend = valid_donnees($_POST['vend']);
    $complet = valid_donnees($_POST['complet']);
    $detachee = valid_donnees($_POST['detachee']);
    $ecommerce = valid_donnees($_POST['ecommerce']);


    //ON TESTE L'IMAGE
    if(isset($_FILES["photo"]) && $_FILES["photo"]["size"] > 0){
        if($_FILES["photo"]["error"] == 0){
            $allowed = array("jpg" => "image/jpg", "JPG" => "image/jpg", "JPEG" => "image/jpeg", "jpeg" => "image/jpeg", "GIF" => "image/gif", "gif" => "image/gif", "png" => "image/png", "PNG" => "image/png");
            $filename = time()."-".$_FILES["photo"]["name"];
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
    $sqlCreationPartenaire = $bdd->prepare("INSERT INTO partenaires (nom, description, collecte, vend, don, url, image, pays, id_villes_free, detachee, complet, ecommerce) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    $sqlCreationPartenaire-> execute(array($nom,$description,$collecte,$vend,$don,$url,$imgBase64,$pays,$ville,$detachee,$complet,$ecommerce));
    $lastId = $bdd-> lastInsertId();
    

    $_SESSION['alertMessage'] = "Partenaire créer";
    $_SESSION['alertMessageConfig'] = "success";
    header("Location: /administration/partenaires/edition-partenaire.php?partenaire=".$lastId);
    exit();  
}

//SI ON SUPPRIME UN PARTENAIRE
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET['delete'])){

}
?>