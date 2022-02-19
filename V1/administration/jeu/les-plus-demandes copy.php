<?php 
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include_once("../../config.php");
// utilisateur non loggé
$titreDeLaPage = "[ADMIN] - Top 10";
$descriptionPage = "";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");
require("../../controles/fonctions/cleanUrl.php");

$sqlRequete = $bdd -> query("SELECT idJeu, COUNT(*) FROM documents_lignes GROUP BY idJeu ORDER BY count(*) DESC");
$donneesRequete = $sqlRequete-> fetch();
$nbrJeux = $sqlRequete->rowCount();

include_once("../../commun/alertMessage.php");
    ?>
    <div class="container">
        <div class="col-12 text-center mt-5 mb-3"><h3>Les 10 jeux ayant le plus de commandes.</h3></div>
        <div class="row d-flex">
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
                    <div class="col-5 p-0 mx-auto border border-secondary d-flex my-2" id="<?php echo $donneesCatalogue['idCatalogue']; ?>">
                        <div class="col-6 p-2 bg-white">
                            <div class="divImgCatalogueAdmin">
                                <?php echo '<img src="data:image/jpeg;base64,'.$donneesImage['image'].'" alt="'.$donneesCatalogue['nom'].' - '.$donneesCatalogue['editeur'].' - '.$donneesCatalogue['annee'].'"  alt="'.$donneesCatalogue['nom'].' - '.$donneesCatalogue['editeur'].' - '.$donneesCatalogue['annee'].'"/>'; ?>
                                <div class="position-relative jumbotron p-1 bg-vos col-3 display-4"><?php echo $classement; ?></div>
                            </div>
                        </div>
                        <div class="col-6 p-0 bg-white">
                            <?php 
                                if(strlen($donneesCatalogue['nom']) > 17){
                                    echo '<div class="col-12 text-center mt-2" data-html="true" data-toggle="tooltip" data-placement="top" title="'.$donneesCatalogue['nom'].'">'.substr(nl2br($donneesCatalogue['nom']),0,17).'...</div>';
                                }else{
                                    echo '<div class="col-12 text-center mt-2">'.$donneesCatalogue['nom'].'</div>';
                                }  
                            ?>                                    
                            <div class="col-12 mt-2 text-center mt-2"><?php echo $donneesCatalogue['editeur'];?></div>
                            <div class="col-12 text-center mt-2"><?php echo $donneesCatalogue['annee']; ?></div>
                            <div class="col-12 text-center mt-2">Nbre de commandes: <?php echo $countVendu; ?></div>
                            
                        </div>
                    </div>
            <?php
                $classement++;
                $donneesRequete = $sqlRequete-> fetch();  
                }
                ?>
        </div>
    </div>
<?php
    include_once("../../commun/bas_de_page-admin.php");
?>
