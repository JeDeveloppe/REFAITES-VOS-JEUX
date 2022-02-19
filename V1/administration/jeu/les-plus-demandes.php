<?php 
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include_once("../../config.php");
// utilisateur non loggé
$titreDeLaPage = "[ADMIN] - Top 50 des commandes";
$descriptionPage = "";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");
require("../../controles/fonctions/cleanUrl.php");

$sqlRequete = $bdd -> query("SELECT idJeu, COUNT(*) FROM documents_lignes GROUP BY idJeu ORDER BY count(*) DESC LIMIT 50");
$donneesRequete = $sqlRequete-> fetch();
$nbrJeux = $sqlRequete->rowCount();

include_once("../../commun/alertMessage.php");
    ?>
    <div class="container">
        <div class="col-12 text-center mt-5 mb-3"><h3>Les 50 jeux ayant le plus de commandes.</h3></div>
        <table class="table table-striped table-sm text-center">
            <thead>
                <th>Classement</th>
                <th>Image</th>
                <th>Nom</th>
                <th>Editeur</th>
                <th>Année</th>
                <th>Nbre de commandes</th>
                <th>En ligne</th>
            </thead>
            <tbody>
                <?php
                    $classement = 1;

                    while($donneesRequete){
                        
                        $sqlCatalogue = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = ".$donneesRequete['idJeu']);
                        $donneesCatalogue = $sqlCatalogue-> fetch();

                        //on cherche l'image du jeu
                        $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$donneesCatalogue['idCatalogue']);
                        $donneesImage = $sqlImage->fetch();

                        //affichage pièces vendu ou pas de ce jeu
                        $sqlVendu = $bdd -> prepare("SELECT * FROM documents_lignes WHERE idJeu = ?");
                        $sqlVendu-> execute(array($donneesCatalogue['idCatalogue']));
                        $countVendu = $sqlVendu-> rowCount();
                        ?>
                        <tr>
                            <td class="text-center align-middle"><?php echo $classement; ?></td>
                            <td class="text-center align-middle">  
                                <div class="divImgPresentationExempleAdmin">
                                    <?php echo '<img src="data:image/jpeg;base64,'.$donneesImage['image'].'" alt="'.$donneesCatalogue['nom'].' - '.$donneesCatalogue['editeur'].' - '.$donneesCatalogue['annee'].'"  alt="'.$donneesCatalogue['nom'].' - '.$donneesCatalogue['editeur'].' - '.$donneesCatalogue['annee'].'"/>'; ?>
                                </div>
                            </td>
                            <td class="text-center align-middle"><?php echo '<div class="col-12 text-center mt-2">'.$donneesCatalogue['nom'].'</div>'; ?></td>
                            <td class="text-center align-middle"><?php echo $donneesCatalogue['editeur'];?></td>
                            <td class="text-center align-middle"><?php echo $donneesCatalogue['annee']; ?></td>
                            <td class="text-center align-middle"><?php echo $countVendu; ?></td>
                            <td class="text-center align-middle">
                                <?php
                                    if($donneesCatalogue['actif'] == 1){
                                        echo '<i class="fas fa-circle text-success"></i>';
                                    }else{
                                        echo '<i class="fas fa-circle text-danger"></i>';
                                    }
                                ?>
                            </td>
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
?>
