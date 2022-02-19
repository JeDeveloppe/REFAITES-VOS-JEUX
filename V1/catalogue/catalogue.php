<?php 
@session_start ();
include_once("../config.php");
// utilisateur non loggé
$titreDeLaPage = "Catalogue des jeux de pièces détachées disponibles | ".$GLOBALS['titreDePage'];
$descriptionPage = "Catalogue des jeux dont le service dispose de pièces en stock. Retrouvez tous nos jeux disponibles pour compléter les vôtres.";
include_once("../bdd/connexion-bdd.php");
include_once("../commun/haut_de_page.php");
require("../controles/fonctions/cleanUrl.php");
if(isset($_GET['tri']) && preg_match('#nom|annee|editeur|ajouts#',$_GET['tri'])){
    require("../controles/fonctions/validation_donnees.php");
    $tri = valid_donnees($_GET['tri']);
    $ordreTri = "ASC";
    $triUrl = $tri;
    if($tri == "ajouts"){
        $tri = "idCatalogue";
        $ordreTri = "DESC";
        $triUrl = "ajouts";
    }
}else{
    $tri = "idCatalogue";
    $ordreTri = "DESC";
    $triUrl = "ajouts";
} 

if(isset($_GET['recherche'])){
    require("../controles/fonctions/validation_donnees.php");
    $recherche = valid_donnees($_GET['recherche']);

    if(strlen($recherche) >= 2){
    $requeteRecherche = 'WHERE nom LIKE "%'.str_replace(" ","%",$recherche).'%" ';
    $parametresUrl = '&recherche=%'.str_replace(" ","%",$recherche).'%';
        if(isset($_GET['tri'])){
            $parametresUrl .="?tri=".$triUrl;
        }
    }else{
        $_SESSION['alertMessage'] = "Recherche trop courte !";
        $_SESSION['alertMessageConfig'] = "warning";
        $requeteRecherche = 'WHERE nom != "" ';
        $parametresUrl = "";
        if(isset($_GET['tri'])){
            $parametresUrl ="&tri=".$triUrl;
        } 
    }
}else{
    $requeteRecherche = 'WHERE nom != "" ';
    $parametresUrl = "";
    if(isset($_GET['tri'])){
        $parametresUrl ="&tri=".$triUrl;
    }
}

$sqlCatalogue = $bdd->query("SELECT * FROM catalogue $requeteRecherche AND accessoire_idCategorie = 0 AND actif = 1");
$nbrJeux = $sqlCatalogue->rowCount();
//si y a au moins un resultat
if($nbrJeux > 0){
    include_once("../commun/alertMessage.php");
    require("./pagination.php");
    ?>
    <div class="container-fluid">
        <div class="row mt-5 mb-4">
            <div class="col-12 text-center"><h1>Les jeux incomplets</h1></div>
            <div class="col-11 mx-auto text-center lead text-muted">
                Le catalogue référence tous les jeux pour lesquels le service dispose de pièces.
            </div>
            <!-- formulaire de recherche -->
            <form class="col-xl-6 mx-auto mt-4 mb-4 d-flex flex-column text-center" method="get" action="/catalogue/">
                <div class="col">
                    <input class="col col-sm-7 col-md-5 col-lg-5 col-xl-8 mx-auto form-control" type="search" name="recherche" minlength="2" placeholder="<?php if(isset($_GET['recherche'])){echo 'Votre recherche: '.$_GET['recherche'];}else{echo'Rechercher un nom de jeu';}?>" aria-label="Rechercher" required>
                </div>
                <div class="col mt-2">
                        <button class="btn btn-outline-success border-success mt-0" type="submit">Chercher</button>
                        <a href="/catalogue/" class="btn btn-outline-danger border-danger">Effacer la recherche</a>
                </div>
            </form>
        </div>
        <div class="row px-xl-9 d-flex justify-content-start">
            <div class="col-11 col-sm-12 p-0 text-right">
                <label class="my-1 mr-2" for="formulaireTri">Option d'affichage: </label>
                    <select name="tri" class="custom-select my-1 mr-sm-2 col-5 col-sm-4 col-md-3 col-lg-2" id="formulaireTri" onchange="trierCatalogue()">
                        <option value="nom" <?php if($tri == "nom"){echo 'selected';}?>>Par nom</option>
                        <option value="annee" <?php if($tri == "annee"){echo 'selected';}?>>Par année</option>
                        <option value="editeur" <?php if($tri == "editeur"){echo 'selected';}?>>Par éditeur</option>
                        <option value="ajouts" <?php if($tri == "idCatalogue"){echo 'selected';}?>>Les derniers ajouts</option>
                    </select>
            </div>
            <?php
                foreach($boites as $boite){
                    $urlEditeurCatalogue = clean_url($boite['editeur']);
                    $urlJeuCatalogue = clean_url($boite['nom']);

                    //url du jeu propre
                    if($boite['urlNom'] == ""){
                        $sqlUpdateUrlNom = $bdd -> prepare("UPDATE catalogue SET urlNom = ? WHERE idCatalogue = ?");
                        $sqlUpdateUrlNom-> execute(array($urlJeuCatalogue,$boite['idCatalogue']));
                    }

                    //affichage pièces vendu ou pas de ce jeu
                    $sqlVendu = $bdd -> prepare("SELECT * FROM documents_lignes WHERE idJeu = ?");
                    $sqlVendu-> execute(array($boite['idCatalogue']));
                    $countVendu = $sqlVendu-> rowCount();
                    if($countVendu > 0){
                        $iconeVenduAdmin = '<i class="fas fa-money-bill-alt text-success" data-html="true" data-toggle="tooltip" data-placement="right" title="DEMANDE FAITE"> ('.$countVendu.') </i>';
                    }else{
                        $iconeVenduAdmin = '<i class="fas fa-search-dollar text-danger" data-html="true" data-toggle="tooltip" data-placement="right" title="PAS DE DEMANDE"></i>';
                    }
                    ?>
                    
                    <div class="col-8 col-sm-6 col-md-4 col-lg-3 p-2 mx-auto mx-md-0" id="<?php echo $boite['idCatalogue']; ?>">
                            <div class="col-12 bg-white p-2 border shadow">   
                                <div class="col-12 p-0"> 
                                    <div class="row">
                                        <div class="col-12 mt-2">
                                            <div class="divImgCatalogue">
                                                <?php 
                                                     echo '<img src="data:image/jpeg;base64,'.$boite['imageBlob'].'" alt="Boite du jeu '.$boite['nom'].' par '.$boite['editeur'].'" />';
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2 p-2">
                                        <?php 
                                            if(strlen($boite['nom']) > 20){
                                                echo '<div class="col-12 text-center" data-html="true" data-toggle="tooltip" data-placement="top" title="'.$boite['nom'].'">'.substr($boite['nom'],0,20).'...</div>';
                                            }else{
                                                echo '<div class="col-12 text-center">'.$boite['nom'].'</div>';
                                            }  
                                        ?>                                    
                                        <div class="col-12 mt-2 text-center"><?php echo $boite['editeur'];?></div>
                                        <div class="col-12 text-center"><?php echo $boite['annee']; ?></div>
                                    </div>
                                </div>
                                <div class="row text-center mt-2">
                                    <?php
                                        //si on passe en version 2
                                        if($GLOBAL['versionSITE'] >= 2){
                                            if(!$_SESSION['levelUser']){
                                                echo '<div class="col-12 d-none d-sm-block">
                                                    <a href="/connexion-inscription/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Merci de vous identifier !</span></a>
                                                </div>
                                                <div class="col-12 mx-auto d-sm-none">
                                                    <a href="/connexion-inscription/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Identifiez- vous!</span></a> 
                                                </div>';
                                            }else{
                                                echo '<div class="col-12 d-none d-sm-block">
                                                    <a href="/jeu/'.$urlEditeurCatalogue.'/'.$boite['idCatalogue'].'/'.$boite['urlNom'].'/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Faire une demande de pièces</span></a>
                                                </div>
                                                <div class="col-12 mx-auto d-sm-none">
                                                    <a href="/jeu/'.$urlEditeurCatalogue.'/'.$boite['idCatalogue'].'/'.$boite['urlNom'].'/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Faire une demande...</span></a>
                                                </div>';
                                            }
                                            
                                        }else{
                                            echo '<div class="col-12 d-none d-sm-block">
                                            <a href="/jeu/'.$urlEditeurCatalogue.'/'.$boite['idCatalogue'].'/'.$boite['urlNom'].'/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Faire une demande de pièces</span></a>
                                        </div>
                                        <div class="col-12 mx-auto d-sm-none">
                                            <a href="/jeu/'.$urlEditeurCatalogue.'/'.$boite['idCatalogue'].'/'.$boite['urlNom'].'/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Faire une demande...</span></a>
                                        </div>';
                                        }
                                    
                                        if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                                            echo '<div class="col-12 mt-3 d-flex justify-content-around align-items-center">
                                                    <a href="/admin/jeu/'.$boite['idCatalogue'].'/edition/"><i class="fas fa-cog fa-2x text-gray-dark ml-3" data-html="true" data-toggle="tooltip" data-placement="right" title="Créé par: '.$boite['createur'].'<br/>le '.date ('d-m-Y à H:i:s', strtotime($boite['created_at'])).'"></i></a> '.$iconeVenduAdmin.'
                                                </div>';
                                        }
                                    ?>
                                </div>
                            </div>
                    </div>
                <?php
                }
            ?>
        </div>
        <?php if($nombreDePages > 1){?>
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
    <div class="row d-flex flex-column mt-4 mb-4">
        <div class="col h1 text-center">Catalogue</div>
           <!-- formulaire de recherche -->
           <form class="col-xl-6 mx-auto mt-4 mb-4 d-flex flex-column text-center" method="get" action="/catalogue/">
                <div class="col">
                    <input class="col col-sm-7 col-md-5 col-lg-5 col-xl-8 mx-auto form-control" type="search" name="recherche" placeholder="<?php if(isset($_GET['recherche'])){echo 'Votre recherche: '.$_GET['recherche'];}else{echo'Rechercher un nom de jeu';}?>" aria-label="Rechercher" required>
                </div>
                <div class="col text-center mt-2">
                    <button class="btn btn-outline-success border-sucess" type="submit">Chercher</button>
                    <a href="/catalogue/" class="btn btn-outline-danger border-danger ml-2">Effacer la recherche</a>
                </div>
            </form>
    </div>
    <div class="row">
        <div class="col text-center">
            <p class="h2">Nous n'avons pas ce jeu en stock.</p>
            <p class="h5">Vous pouvez suivre nos arrivages sur la page Facebook !</p>
            <p class="col-11 col-xl-6 mx-auto mt-5">Vous pouvez aussi utiliser ce <a href="/bouteille-a-la-mer/" class="text-info">formulaire</a> pour être prévenu si le jeu correspondant est mis en ligne.</p>
        </div>
    </div>

</div>
<?php
}
require("../commun/bas_de_page.php");
?>
