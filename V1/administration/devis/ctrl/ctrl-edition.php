<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");
include('../../../config.php');
include('../../../bdd/connexion-bdd.php');
include('../../../bdd/table_config.php');
require('../../../controles/fonctions/validation_donnees.php');

$tailleImage = $donneesConfig[2]['valeur'];
$widthImage = $donneesConfig[3]['valeur'];
$heightImage = $donneesConfig[4]['valeur'];

$client = valid_donnees($_POST['client']);
$ttc = valid_donnees($_POST['totalTTC']);
$ht = valid_donnees($_POST['totalHT']);
$tva = valid_donnees($_POST['totalTVA']);
$prixExpedition = valid_donnees_int($_POST['prixExpedition']);
$prixPreparation = valid_donnees_int($_POST['prixPreparation']);
$commentaire = valid_donnees($_POST['commentaire']);
$envoi = valid_donnees($_POST['envoi']);
$document = valid_donnees($_POST['document']);
$nbrLignes = valid_donnees($_POST['nbr_lignes']);
$doc = valid_donnees($_POST['doc']);

//fin de validation du document
$fin_validation = time () + $donneesConfig[11]['valeur'] + 4;   //+4 temps entre enregistrement en envoi...
//on met a jour les infos
$sqlUpdateDocument = $bdd-> prepare("UPDATE documents SET prix_preparation = :prixP, expedition = :expe, prix_expedition = :prixE, commentaire = :com, totalHT = :ht, totalTVA = :tva, totalTTC = :ttc, time = :hrs,  end_validation = :finValide WHERE idDocument = :doc");
$sqlUpdateDocument->execute(array(
    "prixP" => $prixPreparation,
    "expe" => $envoi,
    "prixE" => $prixExpedition,
    "com" => $commentaire,
    "ht" => $ht,
    "tva" => $tva,
    "ttc" => $ttc,
    "hrs" => time(),
    "finValide" => $fin_validation,
    "doc" => $document));

//pour chaque ligne on vérifie les champs et on mets dans la table documents_lignes

for ($i=0; $i < $nbrLignes; $i++){
    $reponses = $_POST['reponse'];
    $prixLignes = $_POST['prixLigne'];
    $idLignes = $_POST['idLigne'];
    $sqlInsertLignesDocument = $bdd -> prepare("UPDATE documents_lignes SET reponse = ? , prix = ? WHERE idDocLigne = ?");
    $sqlInsertLignesDocument-> execute(array($reponses[$i],$prixLignes[$i],$idLignes[$i]));
}

//si on upload au moins une photo
if(isset($_FILES["photo"])){
    //on compte le nombre de photo
    $countfiles = count($_FILES['photo']['name']);
    //si on a charger plus que 2 photos retour en arriere
    if($countfiles > 1){
        $_SESSION['alertMessage'] = "Pas plus que 2 images par demande merci !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();  
    }

    for($i=0;$i<$countfiles;$i++){
        if($_FILES["photo"]["size"][$i] > 0){
            if($_FILES["photo"]["error"][$i] == 0){
                $allowed = array("jpg" => "image/jpg", "JPG" => "image/jpg", "JPEG" => "image/jpeg", "jpeg" => "image/jpeg", "GIF" => "image/gif", "gif" => "image/gif", "png" => "image/png", "PNG" => "image/png");
                $filename[$i] = $_FILES["photo"]["name"][$i];
                $filetype[$i] = $_FILES["photo"]["type"][$i];
                $filesize[$i] = $_FILES["photo"]["size"][$i];
            
                // Verify file extension
                $ext[$i] = pathinfo($filename[$i], PATHINFO_EXTENSION);
                if(!array_key_exists($ext[$i], $allowed)){
                    $_SESSION['alertMessage'] = "Format d'image incorrect pour l'image ".[$i]." !";
                    $_SESSION['alertMessageConfig'] = "warning";
                    header("Location: ".$_SERVER['HTTP_REFERER']);
                    exit();  
                }

                // Verify file size - 5MB maximum
                $maxsize = $tailleImage * 1024 * 1024;

                if($filesize[$i] > $maxsize){
                    $_SESSION['alertMessage'] = "Image trop grande, maximum ".$tailleImage."MB (ou ".$maxsize." octets)!";
                    $_SESSION['alertMessageConfig'] = "warning";
                    header("Location: ".$_SERVER['HTTP_REFERER']);
                    exit();
                }


                // Verify MYME type of the file
                if(in_array($filetype[$i], $allowed)){
                    $image[$i] = $_FILES['photo']['tmp_name'][$i]; 
                    $imgContent[$i] = file_get_contents($image[$i]); 
                    $imgBase64[$i] = base64_encode($imgContent[$i]);

                    //optention des dimensions de l'image
                    list($width, $height) = getimagesize($image[$i]);
                    //dimension mini pour affichage correct en extra large voir css
                    $minWidth = $widthImage;
                    $minHeight = $heightImage;
                    if($width < $minWidth || $height < $heightImage){
                        $_SESSION['alertMessage'] = "Image n° ".[$i]." trop petite, taille mini (".$widthImage."px X ".$heightImage."px)";
                        $_SESSION['alertMessageConfig'] = "warning";
                        header("Location: ".$_SERVER['HTTP_REFERER']);
                        exit();

                    }
                }
            }
            
        }else{
            $imgBase64 = "";
        }
    }
}

//on saisi dans la base chaque images
for($i=0;$i<$countfiles;$i++){
    if($_FILES["photo"]["size"][$i] > 0){
        $filetype[$i] = $_FILES["photo"]["type"][$i];
        $image[$i] = $_FILES['photo']['name'][$i]; 
        $imageComplete[$i] = $_FILES['photo']['tmp_name'][$i];         
        $imgContent[$i] = file_get_contents($imageComplete[$i]); 
        $imgBase64[$i] = base64_encode($imgContent[$i]);

        //on vérifie si image deja dans la base
        $sqlVerifImageDocument = $bdd-> query("SELECT * FROM documents_images WHERE idDocuments = ".$document);
        $donneesVerifImageDocument = $sqlVerifImageDocument-> fetch();
        $countVerifImageDocument = $sqlVerifImageDocument-> rowCount();
        //si y a un résultat il faut vérifie si c'est le meme nom
        if($countVerifImageDocument == 1){
            if($donneesVerifImageDocument['nom'] != $image[$i]){
                //on supprimer l'image enregistree
                $sqlDeleteVerifImageDocument = $bdd -> query("DELETE FROM documents_images WHERE idDocuments = ".$document);
                //on injecte la nouvelle image
                $sqlInsertImageDocument = $bdd -> prepare("INSERT INTO documents_images (idDocuments,image,nom,image_type) VALUES (?,?,?,?)");
                $sqlInsertImageDocument-> execute(array($document,$imgBase64[$i],$image[$i],$filetype[$i]));
            }
        }else{
        //on injecte la nouvelle image
        $sqlInsertImageDocument = $bdd -> prepare("INSERT INTO documents_images (idDocuments,image,nom,image_type) VALUES (?,?,?,?)");
        $sqlInsertImageDocument-> execute(array($document,$imgBase64[$i],$image[$i],$filetype[$i]));
        }
    }
}

//puis on redirige pour modifier ou envoyer en mail...
$_SESSION['alertMessage'] = "Devis sauvegardé !";
$_SESSION['alertMessageConfig'] = "success";
header("Location: /admin/devis/edition/".$doc);
exit();
?>