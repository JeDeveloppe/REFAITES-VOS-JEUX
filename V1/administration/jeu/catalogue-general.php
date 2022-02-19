<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");

include_once("../../config.php");
// utilisateur non loggé
$titreDeLaPage = "[ADMIN] - Catalogue général";
$descriptionPage = "";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");
require("../../controles/fonctions/validation_donnees.php");


if(isset($_GET['recherche'])){
    $recherche = valid_donnees($_GET['recherche']);
    $requeteRecherche = 'WHERE nom LIKE "%'.str_replace(" ","%",$recherche).'%" ';
}else if(isset($_GET['tri'])){
    $tri = valid_donnees($_GET['tri']);
    switch($tri){
        case "hors_catalogue_pieces":
            $requeteRecherche = 'WHERE actif = 0 ORDER BY nom ASC';
            break;
        case "jeu_complet_indisponible":
            $requeteRecherche = 'WHERE isComplet = 0 ORDER BY nom ASC';
            break;
        case "jeu_gramme_non_renseigne":
            $requeteRecherche = 'WHERE poidBoite = 0 OR poidBoite IS NULL ORDER BY nom ASC';
            break;
        case "mise_au_catalogue_z-a":
            $requeteRecherche = 'ORDER BY idCatalogue DESC';
            break;
        case "mise_au_catalogue_a-z":
            $requeteRecherche = 'ORDER BY idCatalogue ASC';
            break;
    }
}else{
    $requeteRecherche = "";
    $tri = "nom";
}

$sqlJeux = $bdd -> query("SELECT * FROM catalogue $requeteRecherche");
$donneesJeux = $sqlJeux-> fetch();

include_once("../../commun/alertMessage.php");
?>

<div class="container-fluid">
    <div class="col h1 text-center mt-4 text-danger">Catalogue général</div>
    <!-- formulaire de recherche -->
    <div class="row mt-5">
        <div class="col-6 mx-auto">
            <form class="d-flex justify-content-center" method="get" action="">
            <input class="col form-control mr-2 align-self-center" type="search" name="recherche" placeholder="Rechercher un nom de jeu" aria-label="Rechercher" required>
            <button class="btn btn-outline-success mt-0" type="submit">Chercher</button>
            <a href="/admin/jeu/catalogue/general/" class="btn btn-outline-danger ml-2">Éffacer la recherche</a>
        </form>
        </div>
        <div class="col-6 mx-auto">
            <label class="my-1 mr-2" for="formulaireTri">Ou / Affichage: </label>
                <select name="tri" class="custom-select my-1 mr-sm-2 col-lg-5" id="formulaireTri" onchange="trierCatalogueGeneral()">
                    <option value="nom" <?php if($tri == "nom"){echo 'selected';}?>>Par nom</option>
                    <option value="hors_catalogue_pieces" <?php if($tri == "hors_catalogue_pieces"){echo 'selected';}?>>Hors catalogue (pièces)</option>
                    <option value="jeu_complet_indisponible" <?php if($tri == "jeu_complet_indisponible"){echo 'selected';}?>>Jeu complet indisponible</option>
                    <option value="jeu_gramme_non_renseigne" <?php if($tri == "jeu_gramme_non_renseigne"){echo 'selected';}?>>Gramme non renseigné</option>
                    <option value="mise_au_catalogue_z-a" <?php if($tri == "mise_au_catalogue_z-a"){echo 'selected';}?>>Mise au catalogue (Z-A)</option>
                    <option value="mise_au_catalogue_a-z" <?php if($tri == "mise_au_catalogue_a-z"){echo 'selected';}?>>Mise au catalogue (A-Z)</option>
                </select>
        </div>
    </div>
    <table class="table table-sm mt-4 col-12 text-center">
        <thead class="thead-dark text-center">
            <tr>
                <th scope="col">Image</th>
                <th scope="col">Nom</th>
                <th scope="col">Editeur</th>
                <th scope="col">Année</th>
                <th scope="col">Prix de référence</th>
                <th scope="col">Catalogue pièces</th>
                <th scope="col">Ventes pièces</th>
                <th scope="col">Catalogue complet</th>
                <th scope="col">Poid boite</th>
                <th scope="col">Jeu livrable</th>
                <th scope="col">Contenu de la boite</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(is_array($donneesJeux)){
            while($donneesJeux){
                //on cherche l'image du jeu
                $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$donneesJeux['idCatalogue']);
                $donneesImage = $sqlImage->fetch();

                //affichage pièces vendu ou pas de ce jeu
                $sqlVendu = $bdd -> prepare("SELECT * FROM documents_lignes WHERE idJeu = ?");
                $sqlVendu-> execute(array($donneesJeux['idCatalogue']));
                $countVendu = $sqlVendu-> rowCount();

                if($donneesJeux['actif'] == true){
                    $iconeOnline = '<i class="fas fa-circle text-success"></i>';
                }else{
                    $iconeOnline = '<i class="fas fa-circle text-danger"></i>';
                }
                if($countVendu > 0){
                    $iconeVendu = '<i class="fas fa-money-bill-alt text-success" data-html="true" data-toggle="tooltip" data-placement="right" title="DEMANDE FAITE"><br/> ('.$countVendu.') </i>';;
                }else{
                    $iconeVendu = '<i class="fas fa-search-dollar text-danger" data-html="true" data-toggle="tooltip" data-placement="right" title="PAS DE DEMANDE"></i>';
                }
                if($donneesJeux['isComplet'] == true){
                    $iconeJeuComplet = '<i class="fas fa-cube text-success"></i>';
                }else{
                    $iconeJeuComplet = '<i class="fas fa-cube text-danger"></i>';
                }
                if($donneesJeux['isLivrable'] == true){
                    $iconeJeuLivrable = '<i class="fas fa-truck text-success"></i>';
                }else{
                    $iconeJeuLivrable = '<i class="fas fa-truck text-danger"></i>';
                }
                if($donneesJeux['actif'] == true){
                    $iconeCataloguePiece = '<i class="fas fa-puzzle-piece text-success"></i>';
                }else{
                    $iconeCataloguePiece = '<i class="fas fa-puzzle-piece text-danger"></i>';
                }
                if($donneesJeux['poidBoite'] > 0){
                    $iconePoidBoite = '<i class="fas fa-weight text-success"></i>';
                }else{
                    $iconePoidBoite = '<i class="fas fa-weight text-danger"></i>';
                }
                if($donneesJeux['prix_HT'] > 0){
                    $iconePrixDeReference = '<i class="fas fa-euro-sign text-success"></i>';
                }else{
                    $iconePrixDeReference = '<i class="fas fa-euro-sign text-danger"></i>';
                }

                


                
                ?>
                <tr id="<?php echo $donneesJeux['idCatalogue']; ?>">
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
                <td class="text-center align-middle"><?php echo $donneesJeux['nom'].' '.$iconeOnLine; ?></td>
                <td class="text-center align-middle"><?php echo $donneesJeux['editeur']; ?></td>
                <td class="text-center align-middle"><?php echo $donneesJeux['annee']; ?></td>
                <td class="text-center align-middle"><?php echo $iconePrixDeReference; ?></td>
                <td class="text-center align-middle"><?php echo $iconeCataloguePiece; ?></td>
                <td class="text-center align-middle"><?php echo $iconeVendu; ?></td>
                <td class="text-center align-middle"><?php echo $iconeJeuComplet; ?></td>
                <td class="text-center align-middle"><?php echo $iconePoidBoite; ?></td>
                <td class="text-center align-middle"><?php echo $iconeJeuLivrable; ?></td>
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
                        <a href="/administration/jeu/edition.php?etat=offline&tri=<?php echo $tri; ?>&jeu=<?php echo $donneesJeux['idCatalogue'];?>" class="btn btn-warning mt-2">Éditer</a>
                    </div>
                </td>
                </tr>
            <?php
            $donneesJeux = $sqlJeux-> fetch();
            }
        }else{
            echo '<tr><td colspan="5" class="bg-light text-center">Aucun jeu hors ligne avec cette recherche...</td></tr>';
        }
            ?>
        </tbody>
    </table>
</div>

<script>
    function trierCatalogueGeneral(){
        var e=document.getElementById("formulaireTri"),t=e.selectedIndex;
        let a="/admin/jeu/catalogue/general/?tri="+e.options[t].value;
        window.location.href=a
    }
        
</script>
<?php
    include_once("../../commun/bas_de_page-admin.php");
?>
