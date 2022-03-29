<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");

//requete get obligatoire
if($_SERVER["REQUEST_METHOD"] == "POST"){

    require_once("../../../controles/fonctions/validation_donnees_int.php");

    if(isset($_POST['recherche']) &&  preg_match("/^[0-9]{1,9}-[0-9]{1,9}$/", $_POST['recherche'])  ){

        $reference = valid_donnees($_POST['recherche']);

        $sql = "SELECT * FROM jeux_complets WHERE reference = :reference";
        $data = array('reference' => $reference);
    

        try
        {
            include_once('../../../config.php');
            include_once('../../../bdd/connexion-bdd.php');
            $requete = $bdd -> prepare($sql) ;
            $requete->execute($data);
            $donnees = $requete->fetch();
            $nbr = $requete->rowCount();

            if($nbr == 1){
                $sqlCatalogue = $bdd->prepare("SELECT nom FROM catalogue WHERE idCatalogue = ?");
                $sqlCatalogue->execute(array($donnees['idCatalogue']));
                $donneesCatalogue = $sqlCatalogue->fetch();
                $lettre = strtoupper(substr($donneesCatalogue['nom'],0,1));

                $_SESSION['alertMessage'] = "Référence trouvée !";
                $_SESSION['alertMessageConfig'] = "success";
                header("Location: /admin/jeu/catalogue/complet/".$lettre."/#".$donnees['idCatalogue'] );
                exit(); 
            }else{
                $_SESSION['alertMessage'] = "Référence non trouvée !";
                $_SESSION['alertMessageConfig'] = "warning";
                header("Location: ".$_SERVER['HTTP_REFERER'] );
                exit(); 
            }
        
    
            
        }
        catch(Exception $e){
            // en cas d'erreur :
            $_SESSION['alertMessage'] = $e->getMessage();
            $_SESSION['alertMessage-details'] = $data;
            $_SESSION['alertMessageConfig'] = "warning";
            header("Location: ../../../erreurs/500.php");
            exit(); 
        }
    }else{
        $_SESSION['alertMessage'] = "Mauvais format de référence...";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }
}else{
    $_SESSION['alertMessage'] = "Mauvaise requête !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}
?>