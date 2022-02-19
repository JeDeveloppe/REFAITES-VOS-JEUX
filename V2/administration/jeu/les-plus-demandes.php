<?php 
@session_start ();
require("../../controles/fonctions/adminOnline.php");

if(!isset($_GET['demande']) && preg_match('#pieces|occasions#',$_GET['demande']) ){
    $_SESSION['alertMessage'] = "Variable inconnue !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /");
    exit();   
}else{
include_once("../../config.php");
// utilisateur non loggé
$titreDeLaPage = "[ADMIN] - Top 20 des commandes";
$descriptionPage = "";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");
require('../../controles/fonctions/validation_donnees.php');

if(valid_donnees($_GET['demande']) == "pieces"){
    $table = "documents_lignes";
    $champSql = "idJeu";
    $champsSqlRechercheJeu = "idCatalogue";
    $h3 = "Les 20 jeux ayant le plus de commandes. (pièces)";
}else{
    $table = "documents_lignes_achats";
    $champSql = "idCatalogue";
    $champsSqlRechercheJeu = "idCatalogue";
    $h3 = "Les 20 jeux ayant le plus de commandes. (occasions)";
}

$sqlRequete = $bdd -> query("SELECT $champSql, COUNT(*) FROM $table GROUP BY $champSql ORDER BY count(*) DESC LIMIT 20");
$donneesRequete = $sqlRequete-> fetch();
$nbrJeux = $sqlRequete->rowCount();

include_once("../../commun/alertMessage.php");
    ?>
    <div class="container">
        <div class="col-12 text-center mt-5 mb-3"><h3><?php echo $h3; ?></h3></div>
        <table class="table table-striped table-sm text-center">
            <thead>
                <th>Classement</th>
                <th>Image</th>
                <th>Nom</th>
                <th>Editeur</th>
                <th>Année</th>
                <th>Nbre de commandes</th>
            </thead>
            <tbody>
                <?php
                    $classement = 1;

                    while($donneesRequete){
                        
                        $sqlCatalogue = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = ".$donneesRequete[$champSql]);
                        $donneesCatalogue = $sqlCatalogue-> fetch();

                        //affichage pièces vendu ou pas de ce jeu
                        $sqlVendu = $bdd -> prepare("SELECT * FROM $table WHERE $champSql = ?");
                        $sqlVendu-> execute(array($donneesCatalogue['idCatalogue']));
                        $countVendu = $sqlVendu-> rowCount();
                        ?>
                        <tr>
                            <td class="text-center align-middle"><?php echo $classement; ?></td>
                            <td class="text-center align-middle">  
                                <div class="divImgPresentationExempleAdmin">
                                    <?php echo '<img src="data:image/jpeg;base64,'.$donneesCatalogue['imageBlob'].'" alt="'.$donneesCatalogue['nom'].' - '.$donneesCatalogue['editeur'].' - '.$donneesCatalogue['annee'].'"  alt="'.$donneesCatalogue['nom'].' - '.$donneesCatalogue['editeur'].' - '.$donneesCatalogue['annee'].'"/>'; ?>
                                </div>
                            </td>
                            <td class="text-center align-middle"><?php echo '<div class="col-12 text-center mt-2">'.$donneesCatalogue['nom'].'</div>'; ?></td>
                            <td class="text-center align-middle"><?php echo $donneesCatalogue['editeur'];?></td>
                            <td class="text-center align-middle"><?php echo $donneesCatalogue['annee']; ?></td>
                            <td class="text-center align-middle"><?php echo $countVendu; ?></td>
                        </tr>
                        <?php

                    $classement++;
                    $donneesRequete = $sqlRequete-> fetch();  
                    }
                ?>
            </tbody>
        </table>
    </div>
<?php
    include_once("../../commun/bas_de_page-admin.php");
}
?>
