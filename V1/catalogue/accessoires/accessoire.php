<?php 
@session_start ();
include_once("../../config.php");
// utilisateur non loggé
$titreDeLaPage = "Catalogue des jeux de pièces détachées disponibles | ".$GLOBALS['titreDePage'];
$descriptionPage = "Catalogue des jeux dont le service dispose de pièces en stock. Retrouvez tous nos jeux disponibles pour compléter les vôtres.";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");
require("../../controles/fonctions/cleanUrl.php");

$sqlCategorie = $bdd -> query("SELECT * FROM categories WHERE actif = 1");
$donneesCategorie = $sqlCategorie->fetchAll();
$nbrCategorie = $sqlCategorie->rowCount();

if(isset($_GET['categorie'])){

    require("../../controles/fonctions/validation_donnees.php");
    $categorie = valid_donnees($_GET['categorie']);

    $sqlVerifCategorieExiste = $bdd -> prepare("SELECT * FROM categories WHERE actif = 1 AND idCategorie = ?");
    $sqlVerifCategorieExiste-> execute(array($categorie));
    $nbrVerifCategorieExiste = $sqlVerifCategorieExiste->rowCount();
    
    if($nbrVerifCategorieExiste != 1){
        $_SESSION['alertMessage'] = "Catégorie inexistante !";
        $_SESSION['alertMessageConfig'] = "warning";
        $requeteAccessoires = 'WHERE accessoire_idCategorie != "" AND actif = "1" ';
        $parametresUrl = "";
    }else{
        $requeteAccessoires = 'WHERE accessoire_idCategorie = '.$categorie;
        $parametresUrl = '&categorie='.$categorie;
    }
}else{
    $requeteAccessoires = 'WHERE accessoire_idCategorie != "" AND actif = "1" ';
    $parametresUrl = "";
}

$sqlAccessoires = $bdd -> query("SELECT * FROM catalogue $requeteAccessoires");
$nbrAccessoires = $sqlAccessoires->rowCount();

//si y a au moins un resultat
if($nbrCategorie > 0){
    include_once("../../commun/alertMessage.php");
    require("./pagination.php");
    ?>
    <div class="container-fluid">
        <div class="row mt-5 mb-4">
            <div class="col-12 text-center"><h1>Les accessoires</h1></div>
            <div class="col-11 mx-auto text-center lead text-muted">
                Le catalogue référence tous les jeux pour lesquels le service dispose de pièces.
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 d-flex flex-wrap justify-content-start justify-content-sm-center ">
                    <a href="/accessoires/" class="btn btn-primary bg-refaites justify-content-around m-2 p-2 border border-secondary text-decoration-none">TOUS</a>
                    <?php
                        foreach($donneesCategorie as $categorie){
                            echo '<a href="/accessoires/'.$categorie['idCategorie'].'/'.$categorie['urlNom'].'/" class="btn btn-secondary bg-vos justify-content-around m-2 p-2 border border-secondary text-decoration-none">'.$categorie['nom'].'</a>';
                        }
                    ?>
            </div>
        </div>
        <div class="row px-xl-9 d-flex justify-content-start">
            <?php
                //si y a au moins un accessoire a afficher dans cette categorie
                if($countPagination > 0){
                    while($donneesAccessoiresPagination){
                        //on cherche url de la categorie
                        $sqlCategorieInfo = $bdd -> prepare("SELECT * FROM categories WHERE actif = 1 AND idCategorie = (SELECT accessoire_idCategorie FROM catalogue WHERE idCatalogue = ?)");
                        $sqlCategorieInfo-> execute(array($donneesAccessoiresPagination['idCatalogue']));
                        $donneesCategorieInfo = $sqlCategorieInfo-> fetch();
                        
                        //on cherche l'image du jeu
                        $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$donneesAccessoiresPagination['idCatalogue']);
                        $donneesImage = $sqlImage->fetch();

                        //affichage pièces vendu ou pas de ce jeu
                        $sqlVendu = $bdd -> prepare("SELECT * FROM documents_lignes WHERE idJeu = ?");
                        $sqlVendu-> execute(array($donneesAccessoiresPagination['idCatalogue']));
                        $countVendu = $sqlVendu-> rowCount();
                        if($countVendu > 0){
                            $iconeVenduAdmin = '<i class="fas fa-money-bill-alt text-success" data-html="true" data-toggle="tooltip" data-placement="right" title="DEMANDE FAITE"> ('.$countVendu.') </i>';
                        }else{
                            $iconeVenduAdmin = '<i class="fas fa-search-dollar text-danger" data-html="true" data-toggle="tooltip" data-placement="right" title="PAS DE DEMANDE"></i>';
                        }
                        ?>
                        <div class="col-6 col-sm-5 col-md-4 col-lg-3 p-2 mx-auto mx-md-0" id="<?php echo $donneesJeux['idCatalogue']; ?>">
                                <div class="col-12 px-0 py-2 border shadow">   
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="divImgCatalogue">
                                                    <?php echo '<img src="data:image/jpeg;base64,'.$donneesImage['image'].'" alt="'.$donneesAccessoiresPagination['nom'].'"/>'; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2 p-0">
                                            <?php 
                                                if(strlen($donneesAccessoiresPagination['nom']) > 22){
                                                    echo '<div class="col-12 p-0 text-center" data-html="true" data-toggle="tooltip" data-placement="top" title="'.$donneesAccessoiresPagination['nom'].'">'.substr(nl2br($donneesAccessoiresPagination['nom']),0,22).'...</div>';
                                                }else{
                                                    echo '<div class="col-12 p-0 text-center">'.$donneesAccessoiresPagination['nom'].'</div>';
                                                }  
                                            ?>                                    
                                        </div>
                                    </div>
                                    <div class="row text-center mt-2">
                                        <div class="col-12 d-none d-sm-block mb-2">
                                            <a href="/accessoires/<?php echo $donneesCategorieInfo['urlNom'];?>/<?php echo $donneesAccessoiresPagination['idCatalogue'];?>/<?php echo $donneesAccessoiresPagination['urlNom'];?>/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Faire une demande de pièces</span></a>
                                        </div>
                                        <div class="col-12 mx-auto d-sm-none mb-2">
                                            <a href="/accessoires/<?php echo $donneesCategorieInfo['urlNom'];?>/<?php echo $donneesAccessoiresPagination['idCatalogue'];?>/<?php echo $donneesAccessoiresPagination['urlNom'];?>/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Faire une demande...</span></a>
                                        </div>
                                        <?php
                                        if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                                            echo '<div class="col-12 mt-3">
                                                    <a href="/admin/accessoire/'.$donneesAccessoiresPagination['idCatalogue'].'/edition/"><i class="fas fa-cog fa-2x text-gray-dark ml-3"></i></a> <span class="ml-5">'.$iconeVenduAdmin.'</span>
                                                </div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                        </div>
                    <?php
                    $donneesAccessoiresPagination = $sqlAccessoiresPagination-> fetch();
                    }
                }else{
                    echo '<div class="col-12 text-center">
                            <p class="h2 mt-5">Il n\'y a pas encore d\' accessoires dans cette catégorie.</p>
                        </div>';
                }
            ?>




        </div>
        <?php 
        if($nombreDePages > 1){?>
            <div class="row">
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
                                        echo '<li class="page-item'.$active.'"><a class="page-link" href="/catalogue/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                    }
                                    $pageSuivante = $pageActuelle+1;
                                    echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                    echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
                                }
                                
                                if($pageActuelle > 1 && $pageActuelle < $nombreDePages - $variation){
                                    $pageAvant = $pageActuelle-1;
                                    echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue/?page=1'.$parametresUrl.'"><i class="fas fa-fast-backward"></i></a></li>';
                                    echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue/?page='.$pageAvant.$parametresUrl.'"><i class="fas fa-step-backward"></i></a></li>';
                                        for($i=$pageActuelle-1;$i<=$pageActuelle+$variation;$i++){
                                            if($pageActuelle == $i){
                                                $active = " active";
                                            }else{
                                                $active = "";
                                            }
                                            echo '<li class="page-item'.$active.'"><a class="page-link" href="/catalogue/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                        }
                                    $pageSuivante = $pageActuelle+1;
                                    echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                    echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
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
                                    echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue/?page=1'.$parametresUrl.'"><i class="fas fa-fast-backward"></i></a></li>';
                                    echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue/?page='.$pageAvant.$parametresUrl.'"><i class="fas fa-step-backward"></i></a></li>';
                                        for($i=$pageActuelle-$back;$i<=$nombreDePages;$i++){
                                            if($pageActuelle == $i){
                                                $active = " active";
                                            }else{
                                                $active = "";
                                            }
                                            echo '<li class="page-item'.$active.'"><a class="page-link" href="/catalogue/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                        }
                                    if($pageActuelle < $nombreDePages){
                                        $pageSuivante = $pageActuelle+1;
                                        echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                        echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
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
            </div>
            

        <?php } ?>
    </div>
<?php
}// fin de resultat
else{
?>
<div class="container">
    <div class="row mt-5">
        <div class="col-12 text-center"><h1>Les accessoires</h1></div>
        <div class="col-12 text-center">
            <p class="h2 mt-5">Il n'y a pas encore de catégories de définies.</p>
        </div>
    </div>

</div>

<?php
}
require("../../commun/bas_de_page.php");
?>

