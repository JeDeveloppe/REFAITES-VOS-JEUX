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
    $likeRecherche = str_replace(" ","%",$recherche);
    $parametresUrl = "&recherche=%".$likeRecherche."%";
    $messagesParPage = 10;
    $tri = "nom";
}else if(isset($_GET['tri'])){
    $tri = valid_donnees($_GET['tri']);
    switch($tri){
        case "hors_catalogue_pieces":
            $requeteRecherche = 'WHERE actif = 0 ORDER BY nom ASC';
            $parametresUrl = "&tri=hors_catalogue_pieces";
            $messagesParPage = 25;
            break;
        case "jeu_complet_indisponible":
            $requeteRecherche = 'WHERE isComplet = 0 ORDER BY nom ASC';
            $parametresUrl = "&tri=jeu_complet_indisponible";
            $messagesParPage = 25;
            break;
        case "jeu_complet_disponible":
            $requeteRecherche = 'WHERE isComplet = 1 ORDER BY nom ASC';
            $parametresUrl = "&tri=jeu_complet_disponible";
            $messagesParPage = 25;
            break;
        case "jeu_gramme_non_renseigne":
            $requeteRecherche = 'WHERE poidBoite = 0 OR poidBoite IS NULL ORDER BY nom ASC';
            $parametresUrl = "&tri=jeu_gramme_non_renseigne";
            $messagesParPage = 25;
            break;
        case "jeu_deee_non_renseigne":
            $requeteRecherche = 'WHERE deee IS NULL ORDER BY nom ASC';
            $parametresUrl = "&tri=jeu_deee_non_renseigne";
            $messagesParPage = 25;
            break;
        case "pieces_sans_vente";
            $requeteRecherche = "WHERE idCatalogue NOT IN (SELECT idJeu FROM documents_lignes) ORDER BY nom ASC";
            $parametresUrl = "&tri=pieces_sans_vente";
            $messagesParPage = 25;
            break;
        case "mise_au_catalogue_z-a":
            $requeteRecherche = 'ORDER BY idCatalogue DESC';
            $parametresUrl = "&tri=mise_au_catalogue_z-a";
            $messagesParPage = 25;
            break;
        case "mise_au_catalogue_a-z":
            $requeteRecherche = 'ORDER BY idCatalogue ASC';
            $parametresUrl = "&tri=mise_au_catalogue_a-z";
            $messagesParPage = 25;
            break;
        default:
            $requeteRecherche = "";
            $tri = "nom";
            $messagesParPage = 25;
            $parametresUrl = "";
    }
}else{
    $requeteRecherche = "";
    $tri = "nom";
    $messagesParPage = 25;
    $parametresUrl = "";
}

$querySql = "SELECT * FROM catalogue $requeteRecherche";
$sqlJeux = $bdd->query($querySql);
$rows = $sqlJeux->rowCount();

//stats créateurs
$sqlAntoine = $bdd->query("SELECT * FROM catalogue WHERE createur = 'Antoine' ");
$nbrAntoine = $sqlAntoine->rowCount();
$sqlCoco = $bdd->query("SELECT * FROM catalogue WHERE createur = 'Coco' ");
$nbrCoco = $sqlCoco->rowCount();

require('./pagination-jeu.php');


include_once("../../commun/alertMessage.php");
?>

<div class="container-fluid">
    <div class="col h1 text-center mt-4 text-danger">Catalogue général</div>
    <div class="col h6 text-center text-info small">Antoine: <?php echo $nbrAntoine; ?> - Coco: <?php echo $nbrCoco; ?></div>
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
                    <option value="jeu_complet_disponible" <?php if($tri == "jeu_complet_disponible"){echo 'selected';}?>>Jeu complet disponible</option>
                    <option value="jeu_gramme_non_renseigne" <?php if($tri == "jeu_gramme_non_renseigne"){echo 'selected';}?>>Gramme non renseigné</option>
                    <option value="jeu_deee_non_renseigne" <?php if($tri == "jeu_deee_non_renseigne"){echo 'selected';}?>>DEEE non renseigné</option>
                    <option value="pieces_sans_vente" <?php if($tri == "pieces_sans_vente"){echo 'selected';}?>>Sans ventes (pièces)</option>
                    <option value="mise_au_catalogue_z-a" <?php if($tri == "mise_au_catalogue_z-a"){echo 'selected';}?>>Mise au catalogue (Z-A)</option>
                    <option value="mise_au_catalogue_a-z" <?php if($tri == "mise_au_catalogue_a-z"){echo 'selected';}?>>Mise au catalogue (A-Z)</option>
                </select>
                <?php echo 'Résultats: '.$rows; ?>
        </div>
    </div>
    <table class="table table-sm mt-4 col-12 text-center">
        <thead class="thead-dark text-center">
            <tr>
                <th scope="col">Nom<br/><span class="small">Créateur</span></th>
                <th scope="col">Editeur</th>
                <th scope="col">Année</th>
                <th scope="col">Image</th>
                <th scope="col">Prix de référence</th>
                <th scope="col">Catalogue pièces</th>
                <th scope="col">Ventes pièces</th>
                <th scope="col">Catalogue complet</th>
                <th scope="col">Poid boite</th>
                <th scope="col">DEEE</th>
                <th scope="col">Jeu livrable</th>
                <th scope="col">Contenu de la boite</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(is_array($donneesJeux)){
            foreach($donneesJeux as $jeu){
                    //affichage pièces vendu ou pas de ce jeu
                $sqlVendu = $bdd -> prepare("SELECT * FROM documents_lignes WHERE idJeu = ?");
                $sqlVendu-> execute(array($jeu['idCatalogue']));
                $countVendu = $sqlVendu-> rowCount();

                if($countVendu > 0){
                    $iconeVendu = '<i class="fas fa-money-bill-wave text-success"></i>';
                }else{
                    $iconeVendu = '<i class="fas fa-money-bill-wave text-danger"></i>';
                }
                if(ctype_alpha(substr($jeu['nom'],0,1))){
                    $lettre_complet = substr($jeu['nom'],0,1);
                }else{
                    $lettre_complet = "#";
                }
                if($jeu['isComplet'] == true){
                    $iconeJeuComplet = '<a href="/admin/jeu/catalogue/complet/'.$lettre_complet.'/" class="btn btn-success"><i class="fas fa-cube"></i></a>';
                }else{
                    $iconeJeuComplet = '<i class="fas fa-cube text-danger"></i>';
                }
                if($jeu['isLivrable'] == true){
                    $iconeJeuLivrable = '<i class="fas fa-truck text-success"></i>';
                }else{
                    $iconeJeuLivrable = '<i class="fas fa-truck text-danger"></i>';
                }
                if($jeu['actif'] == true){
                    $iconeCataloguePiece = '<i class="fas fa-puzzle-piece text-success"></i>';
                }else{
                    $iconeCataloguePiece = '<i class="fas fa-puzzle-piece text-danger"></i>';
                }
                if($jeu['poidBoite'] > 0){
                    $iconePoidBoite = '<i class="fas fa-weight text-success"></i>';
                }else{
                    $iconePoidBoite = '<i class="fas fa-weight text-danger"></i>';
                }
                if($jeu['deee'] == "OUI"){
                    $iconeDeee = '<i class="fas fa-battery-full text-success"></i>';
                }else if($jeu['deee'] == "NON"){
                    $iconeDeee = '<i class="fas fa-battery-full text-danger"></i>';
                }else{
                    $iconeDeee = '<i class="fas fa-battery-full text-warning"></i>';
                }
                if($jeu['prix_HT'] > 0){
                    $iconePrixDeReference = '<i class="fas fa-euro-sign text-success"></i>';
                }else{
                    $iconePrixDeReference = '<i class="fas fa-euro-sign text-danger"></i>';
                }
                ?>
                <tr id="<?php echo $jeu['idCatalogue']; ?>">
                <td class="text-center align-middle"><?php echo $jeu['nom']; ?><br/><?php echo '<span class="text-info small">'.$jeu['createur'].'</span>'; ?></td>
                <td class="text-center align-middle"><?php echo $jeu['editeur']; ?></td>
                <td class="text-center align-middle"><?php echo $jeu['annee']; ?></td>
                <td class="text-center align-middle">
                    <div class="divImgPresentationExempleAdmin">
                        <?php 
                            echo '<img src="data:image/jpeg;base64,'.$jeu['imageBlob'].'"/>';
                        ?>
                    </div>
                </td>
                <td class="text-center align-middle"><?php echo $iconePrixDeReference; ?></td>
                <td class="text-center align-middle"><?php echo $iconeCataloguePiece; ?></td>
                <td class="text-center align-middle"><?php echo $iconeVendu; ?></td>
                <td class="text-center align-middle"><?php echo $iconeJeuComplet; ?></td>
                <td class="text-center align-middle"><?php echo $iconePoidBoite; ?></td>
                <td class="text-center align-middle"><?php echo $iconeDeee; ?></td>
                <td class="text-center align-middle"><?php echo $iconeJeuLivrable; ?></td>
                <td class="text-center align-middle">
                    <?php 
                    $sqlContenuJeu = $bdd-> query("SELECT * FROM pieces WHERE idJeu =".$jeu['idCatalogue']);
                    $donneesContenuJeu = $sqlContenuJeu->fetch();
                    //si y a une info
                    if(isset($donneesContenuJeu['contenu_total']) && $donneesContenuJeu['contenu_total'] != ""){
                        echo '<i class="fas fa-check text-success fa-2x"></i>';
                    }else{
                        echo '<i class="fas fa-times text-danger fa-2x"></i>';
                    }
                    ?>
                </td>
               
                <td class="text-center align-middle">
                    <div class="btn-group">
                        <a href="/administration/jeu/edition.php?etat=offline&tri=<?php echo $tri; ?>&jeu=<?php echo $jeu['idCatalogue'];?>" target="_blank" class="btn btn-warning mt-2">Éditer</a>
                    </div>
                </td>
                </tr>
            <?php
            }
        }else{
            echo '<tr><td colspan="5" class="bg-light text-center">Aucun jeu hors ligne avec cette recherche...</td></tr>';
        }
            ?>
        </tbody>
    </table>
    <?php 
        if($nombreDePages > 1){?>
        <div class="col-12 mt-4">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                        <?php 
                        $variation = 2;
                        $milieu = ceil($nombreDePages/2);
                        if($variation < $milieu){
                            $variation = $variation;
                        }else{
                            $variation = $milieu;
                        }

                        if($pageActuelle == 1){
                        echo '<li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-fast-backward"></i></a></li>';
                        echo '<li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-step-backward"></i></a></li>';
                            for($i=1;$i<=$pageActuelle+$variation;$i++){
                                if($pageActuelle == $i){
                                    $active = " active";
                                }else{
                                    $active = "";
                                }
                                echo '<li class="page-item'.$active.'"><a class="page-link" href="/admin/jeu/catalogue/general/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                            }
                            $pageSuivante = $pageActuelle+1;
                            echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/jeu/catalogue/general/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                            echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/jeu/catalogue/general/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
                        }
                        
                        if($pageActuelle > 1 && $pageActuelle < $nombreDePages - $variation){
                            $pageAvant = $pageActuelle-1;
                            echo '<li rel="prev" class="page-item"><a class="page-link" href="/admin/jeu/catalogue/general/?page=1'.$parametresUrl.'"><i class="fas fa-fast-backward"></i></a></li>';
                            echo '<li rel="prev" class="page-item"><a class="page-link" href="/admin/jeu/catalogue/general/?page='.$pageAvant.$parametresUrl.'"><i class="fas fa-step-backward"></i></a></li>';
                                for($i=$pageActuelle-1;$i<=$pageActuelle+$variation;$i++){
                                    if($pageActuelle == $i){
                                        $active = " active";
                                    }else{
                                        $active = "";
                                    }
                                    echo '<li class="page-item'.$active.'"><a class="page-link" href="/admin/jeu/catalogue/general/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                }
                            $pageSuivante = $pageActuelle+1;
                            echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/jeu/catalogue/general/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                            echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/jeu/catalogue/general/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
                        }

                        if($pageActuelle >= $nombreDePages - $variation && $pageActuelle < $nombreDePages+1){
                            if($pageActuelle < $nombreDePages - $variation+1){
                                $back = 1;
                            }elseif($pageActuelle < $nombreDePages - $variation+2){
                                $back = 2;
                            }elseif($pageActuelle < $nombreDePages - $variation+3){
                                $back = 3;
                            }elseif($pageActuelle < $nombreDePages - $variation+4){
                                $back = 4;
                            }
                            $pageAvant = $pageActuelle-1;
                            echo '<li rel="prev" class="page-item"><a class="page-link" href="/admin/jeu/catalogue/general/?page=1'.$parametresUrl.'"><i class="fas fa-fast-backward"></i></a></li>';
                            echo '<li rel="prev" class="page-item"><a class="page-link" href="/admin/jeu/catalogue/general/?page='.$pageAvant.$parametresUrl.'"><i class="fas fa-step-backward"></i></a></li>';
                                for($i=$pageActuelle-$back;$i<=$nombreDePages;$i++){
                                    if($pageActuelle == $i){
                                        $active = " active";
                                    }else{
                                        $active = "";
                                    }
                                    echo '<li class="page-item'.$active.'"><a class="page-link" href="/admin/jeu/catalogue/general/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                }
                            if($pageActuelle < $nombreDePages){
                                $pageSuivante = $pageActuelle+1;
                                echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/jeu/catalogue/general/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/jeu/catalogue/general/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
                            }else{
                                echo '<li rel="next" class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-step-forward"></i></a></li>';
                                echo '<li rel="next" class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-fast-forward"></i></a></li>';
                            }
                        }             
                        ?>
                </ul>
            </nav>
        </div>
        <div class="col-12 text-center">Total des pages: <?php echo $nombreDePages; ?></div>
    <?php
    }
    ?>
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
