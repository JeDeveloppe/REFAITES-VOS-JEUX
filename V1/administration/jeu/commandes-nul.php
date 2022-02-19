<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");

include_once("../../config.php");
// utilisateur non loggé
$titreDeLaPage = "[ADMIN] - Catalogue des jeux SANS commande";
$descriptionPage = "";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");

 

if(isset($_GET['recherche'])){
    require("../../controles/fonctions/validation_donnees.php");
    $recherche = valid_donnees($_GET['recherche']);
    $requeteRecherche = 'WHERE nom LIKE "%'.str_replace(" ","%",$recherche).'%" ';
}else{
    $requeteRecherche = 'WHERE nom != "0" ';
}
$sqlJeux = $bdd -> query("SELECT * FROM catalogue $requeteRecherche AND actif = 1 AND idCatalogue NOT IN (SELECT idJeu FROM documents_lignes) ORDER BY idCatalogue ASC");

$donneesJeux = $sqlJeux-> fetch();

include_once("../../commun/alertMessage.php");
?>

<div class="container-fluid">
    <div class="col h1 text-center mt-4 text-danger">Liste des jeux au catalogue sans ventes</div>
    <!-- formulaire de recherche -->
    <div class="col mt-4">
        <form class="d-flex justify-content-center" method="get" action="">
        <input class="col-3 form-control mr-2 align-self-center" type="search" name="recherche" placeholder="Rechercher un nom de jeu" aria-label="Rechercher" required>
        <button class="btn btn-outline-success mt-0" type="submit">Chercher</button>
        <a href="/admin/jeu/sans-ventes/" class="btn btn-outline-danger ml-2">Éffacer la recherche</a>
      </form>
    </div>
    <table class="table table-sm mt-4 col-12 text-center">
        <thead class="thead-dark text-center">
            <tr>
                <th scope="col">#</th>
                <th scope="col">idJeu</th>
                <th scope="col">Image</th>
                <th scope="col">Vente</th>
                <th scope="col">Nom</th>
                <th scope="col">Editeur</th>
                <th scope="col">Année</th>
                <th scope="col">Renseignement du contenu de la boite</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(is_array($donneesJeux)){
            $ligne = 1;
            while($donneesJeux){
                //on cherche l'image du jeu
                $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$donneesJeux['idCatalogue']);
                $donneesImage = $sqlImage->fetch();

                //affichage pièces vendu ou pas de ce jeu
                $sqlVendu = $bdd -> prepare("SELECT * FROM documents_lignes WHERE idJeu = ?");
                $sqlVendu-> execute(array($donneesJeux['idCatalogue']));
                $countVendu = $sqlVendu-> rowCount();
                if($countVendu > 0){
                    $iconeVendu = '<i class="fas fa-money-bill-alt text-success" data-html="true" data-toggle="tooltip" data-placement="right" title="DEMANDE FAITE"> ('.$countVendu.') </i>';;
                }else{
                    $iconeVendu = '<i class="fas fa-search-dollar text-danger" data-html="true" data-toggle="tooltip" data-placement="right" title="PAS DE DEMANDE"></i>';
                }
                ?>
                <tr id="<?php echo $donneesJeux['idCatalogue']; ?>">
                <td class="text-center align-middle"><?php echo $ligne; ?></td>
                <td class="text-center align-middle"><?php echo $donneesJeux['idCatalogue']; ?></td>
                <td class="text-center align-middle">
                    <div class="divImgPresentationExempleAdmin">
                        <?php 
                            if($donneesImage['image'] != ""){
                                echo '<img src="data:image/jpeg;base64,'.$donneesImage['image'].'"/>';
                            }else{
                                echo '<img src="/images/design/default.png" />';
                            }
                        ?>
                    </div>
                </td>
                <td class="text-center align-middle"><?php echo $iconeVendu; ?></td>
                <td class="text-center align-middle"><?php echo $donneesJeux['nom']; ?></td>
                <td class="text-center align-middle"><?php echo $donneesJeux['editeur']; ?></td>
                <td class="text-center align-middle"><?php echo $donneesJeux['annee']; ?></td>
                <td class="text-center align-middle">
                    <?php 
                    $sqlContenuJeu = $bdd-> query("SELECT * FROM pieces WHERE idJeu =".$donneesJeux['idCatalogue']);
                    $donneesContenuJeu = $sqlContenuJeu->fetch();
                    //si y a une info
                    if($donneesContenuJeu['contenu_total'] != ""){
                        echo '<i class="fas fa-check text-success fa-2x"></i>';
                    }else{
                        echo '<i class="fas fa-times text-danger fa-2x"></i>';
                    }
                    ?>
                </td>
                
                <td class="text-center align-middle">
                    <div class="btn-group">
                        <a href="/administration/jeu/edition.php?etat=offline&jeu=<?php echo $donneesJeux['idCatalogue'];?>" class="btn btn-warning mt-2">Éditer</a>
                    </div>
                    
                </td>
                </tr>
            <?php
            $donneesJeux = $sqlJeux-> fetch();
            $ligne ++;
            }
        }else{
            echo '<tr><td colspan="5" class="bg-light text-center">Aucun jeu hors ligne avec cette recherche...</td></tr>';
        }
            ?>
        </tbody>
    </table>
</div>

<?php
    include_once("../../commun/bas_de_page-admin.php");
?>
