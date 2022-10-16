<?php 
@session_start ();
include_once("../config.php");
// utilisateur non loggé
$titreDeLaPage = "Catalogue des pièces détachées | ".$GLOBALS['titreDePage'];
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

if(isset($_SESSION['recherchePieceDetachees'])){
    require("../controles/fonctions/validation_donnees.php");
    $recherche = valid_donnees($_SESSION['recherchePieceDetachees']);
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

//NOMBRE DE JEUX TOTAL POUR PIECES
$sqlCatalogue = $bdd -> query("SELECT * FROM catalogue WHERE actif = 1 AND accessoire_idCategorie = 0");
$nbrJeuxTotalEnLigne = $sqlCatalogue->rowCount();

$sqlCatalogue = $bdd -> query("SELECT * FROM catalogue $requeteRecherche AND accessoire_idCategorie = 0 AND actif = 1");
$nbrJeux = $sqlCatalogue->rowCount();
//si y a au moins un resultat
if($nbrJeux > 0){
    include_once("../commun/alertMessage.php");
    require("./pagination.php");
    ?>
    <div class="container-fluid">
        <div class="row mt-5 mb-4">
            <div class="col-12 text-center"><h1>Pièces détachées</h1></div>
            <div class="col-11 mx-auto text-center lead text-muted">
                Le catalogue référence tous les jeux pour lesquels le service dispose de pièces.<br/>
            </div>
    
            <!-- formulaire de recherche -->
            <div class="col-xl-6 mx-auto mt-4 mb-4">
                <input class="col-12 col-sm-7 col-md-5 col-lg-5 col-xl-8 mx-auto form-control" type="search" name="recherche" id="recherchePieceDetachees" minlength="2" placeholder="Rechercher un nom de jeu" aria-label="Rechercher">
                <small class="form-text text-muted text-center">Recherche à partir de 3 caractères...</small>
            </div>
        </div>
        <div id="affichageRecherche"></div>
        <div class="row px-xl-9 d-flex justify-content-start" id="affichageCatalogue">
            <div class="col-12 d-flex flex-wrap justify-content-between mb-3">
                <div class="col-12 col-sm-6 fb-share-button" data-href="<?php echo $GLOBALS['domaineShareFacebook'].$_SERVER['REQUEST_URI']; ?>" data-layout="button" data-size="large"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $GLOBALS['domaineShareFacebook'].$_SERVER['REQUEST_URI'] ; ?>&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Partager</a></div>
                <div class="col-12 col-sm-6 p-0 text-right">
                    <label class="my-1 mr-2" for="formulaireTri">Option d'affichage: </label>
                        <select name="tri" class="custom-select my-1 mr-sm-2 col-6 col-sm-12 col-md-6" id="formulaireTri" onchange="trierCatalogue()">
                            <option value="nom" <?php if($tri == "nom"){echo 'selected';}?>>Par nom</option>
                            <option value="annee" <?php if($tri == "annee"){echo 'selected';}?>>Par année</option>
                            <option value="editeur" <?php if($tri == "editeur"){echo 'selected';}?>>Par éditeur</option>
                            <option value="ajouts" <?php if($tri == "idCatalogue"){echo 'selected';}?>>Les derniers ajouts</option>
                        </select>
                </div>
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
                    <div class="col-9 col-sm-6 col-md-4 col-lg-3 mx-auto mx-md-0 p-2" id="<?php echo $boite['idCatalogue']; ?>">
                            <div class="col-12 p-1 border shadow scale-hover bg-white">   
                                <div class="col-12 p-0"> 
                                    <div class="row">
                                        <div class="col-12 mt-2">
                                            <div class="divImgCatalogue">
                                                <?php 
                                                    if($boite['imageBlob'] == ''){
                                                        echo '<img src="/images/design/default.png" alt="Image du jeu en attente...">';
                                                    }else{
                                                        echo '<img src="data:image/jpeg;base64,'.$boite['imageBlob'].'" alt="Boite du jeu '.$boite['nom'].' par '.$boite['editeur'].'" />';
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- nom,editeur,annee -->
                                    <div class="row my-3">
                                        <?php 
                                            if(strlen($boite['nom']) > 25){
                                                echo '<div class="col-12 display-6 text-center" data-html="true" data-toggle="tooltip" data-placement="top" title="'.$boite['nom'].'">'.substr($boite['nom'],0,25).'...</div>';
                                            }else{
                                                echo '<div class="col-12 text-center">'.$boite['nom'].'</div>';
                                            }  
                                        ?>                                    
                                        <div class="col-12 mt-2 text-center"><?php if($boite['editeur'] == "INCONNU"){echo "&nbsp;";}else{echo $boite['editeur'];}?></div>
                                        <div class="col-12 text-center"><?php if($boite['annee'] == "Année inconnue"){echo "&nbsp;";}else{echo $boite['annee'];} ?></div>
                                    </div>
                                </div>
                                <!-- button -->
                                <div class="row text-center my-2">
                                    <?php
                                        echo '<div class="col-12 d-none d-sm-block">
                                            <a href="/catalogue-pieces-detachees/'.$urlEditeurCatalogue.'/'.$boite['idCatalogue'].'/'.$boite['urlNom'].'/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Faire une demande de pièces</span></a>
                                        </div>
                                        <div class="col-12 mx-auto d-sm-none">
                                            <a href="/catalogue-pieces-detachees/'.$urlEditeurCatalogue.'/'.$boite['idCatalogue'].'/'.$boite['urlNom'].'/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Faire une demande...</span></a>
                                        </div>';
                                        
                                    
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
                                            echo '<li class="page-item'.$active.'"><a class="page-link" href="/catalogue-pieces-detachees/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                        }
                                        $pageSuivante = $pageActuelle+1;
                                        echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-pieces-detachees/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                        echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-pieces-detachees/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
                                    }
                                    
                                    if($pageActuelle > 1 && $pageActuelle < $nombreDePages - $variation){
                                        $pageAvant = $pageActuelle-1;
                                        echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue-pieces-detachees/?page=1'.$parametresUrl.'"><i class="fas fa-fast-backward"></i></a></li>';
                                        echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue-pieces-detachees/?page='.$pageAvant.$parametresUrl.'"><i class="fas fa-step-backward"></i></a></li>';
                                            for($i=$pageActuelle-1;$i<=$pageActuelle+$variation;$i++){
                                                if($pageActuelle == $i){
                                                    $active = " active";
                                                }else{
                                                    $active = "";
                                                }
                                                echo '<li class="page-item'.$active.'"><a class="page-link" href="/catalogue-pieces-detachees/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                            }
                                        $pageSuivante = $pageActuelle+1;
                                        echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-pieces-detachees/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                        echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-pieces-detachees/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
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
                                        echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue-pieces-detachees/?page=1'.$parametresUrl.'"><i class="fas fa-fast-backward"></i></a></li>';
                                        echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue-pieces-detachees/?page='.$pageAvant.$parametresUrl.'"><i class="fas fa-step-backward"></i></a></li>';
                                            for($i=$pageActuelle-$back;$i<=$nombreDePages;$i++){
                                                if($pageActuelle == $i){
                                                    $active = " active";
                                                }else{
                                                    $active = "";
                                                }
                                                echo '<li class="page-item'.$active.'"><a class="page-link" href="/catalogue-pieces-detachees/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                            }
                                        if($pageActuelle < $nombreDePages){
                                            $pageSuivante = $pageActuelle+1;
                                            echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-pieces-detachees/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                            echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-pieces-detachees/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
                                        }else{
                                            echo '<li rel="next" class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-step-forward"></i></a></li>';
                                            echo '<li rel="next" class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-fast-forward"></i></a></li>';
                                        }
                                    }             
                                    ?>
                            </ul>
                        </nav>
                    </div>
                    <div class="col-12 text-center">
                        <p>Total des pages: <?php echo $nombreDePages; ?></p>
                        <p>Actuellement il y a <span data-html="true" data-toggle="tooltip" data-placement="top" id="odometerJeuxEnLigne" class="odometer"></span> jeux incomplets en ligne.</p>
                    </div>
                <?php
                }
                ?>
        </div>
    </div>
<?php
}// fin de resultat
else{
?>
    <div class="container-fluid">
        <div class="row mt-5 mb-4">
            <div class="col-12 text-center"><h1>Pièces détachées</h1></div>
            <div class="col-11 mx-auto text-center lead text-muted">
                Le catalogue référence tous les jeux pour lesquels le service dispose de pièces.
            </div>
            <!-- formulaire de recherche -->
            <div class="col-xl-6 mx-auto mt-4 mb-4">
                <input class="col-12 col-sm-7 col-md-5 col-lg-5 col-xl-8 mx-auto form-control" type="search" name="recherche" id="recherchePieceDetachees" minlength="2" placeholder="Rechercher un nom de jeu" aria-label="Rechercher" required>
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <p class="h2">Nous n'avons pas ce jeu en stock.</p>
                <p class="h5">Vous pouvez suivre nos arrivages sur la page Facebook !</p>
            </div>
        </div>
    </div>
<?php
}
require("../commun/bas_de_page.php");
?>
<script>
    let recherchePieceDetachees = document.getElementById('recherchePieceDetachees');
    let affichageRecherche = document.getElementById('affichageRecherche');
    let affichageCatalogue = document.getElementById('affichageCatalogue');
    let timer;

    // recherchePieceDetachees.addEventListener('input', function() {
    // if(recherchePieceDetachees.value.length > 2){   
    //     setTimeout(function(){      
    //         fetch('../../requetes/catalogue-piece-detachee-like.php?recherche='+recherchePieceDetachees.value)
    //             .then(response => response.text())
    //             .then((response) => {
    //                 affichageRecherche.innerHTML = response;
    //                 affichageCatalogue.style.setProperty("display", "none", "important")
    //             })
    //             .catch(err => console.log(err));
    //         },500)
            
    // }else{
    //     affichageCatalogue.style.display = "block";
    //     affichageRecherche.innerHTML = "";
    // }
    // }); 

    recherchePieceDetachees.addEventListener('keyup',function (e) {
        // Clears any outstanding timer
        clearTimeout(timer);

        // Sets new timer that may or may not get cleared
        timer = setTimeout(() => {
            // Only fires if not cleared
            if(recherchePieceDetachees.value.length > 2){   
            fetch('../../requetes/catalogue-piece-detachee-like.php?recherche='+recherchePieceDetachees.value)
                .then(response => response.text())
                .then((response) => {
                    affichageRecherche.innerHTML = response;
                    affichageCatalogue.style.setProperty("display", "none", "important")
                })
                .catch(err => console.log(err));
            }else{
                affichageCatalogue.style.display = "block";
                affichageRecherche.innerHTML = "";
            }

        }, 800);
    });

</script>
<script>
    /*
    * ODOMETRE
    */
    let jeuxEnLigne = <?php echo json_decode($nbrJeuxTotalEnLigne); ?>;

    if(jeuxEnLigne < 10){
        odometerJeuxEnLigne.innerHTML = 4;
    }else if(jeuxEnLigne > 9 && jeuxEnLigne < 100){
        odometerJeuxEnLigne.innerHTML = 31;
    }else if(jeuxEnLigne > 99 && jeuxEnLigne < 1000){
        odometerJeuxEnLigne.innerHTML = 300;
    }else if(jeuxEnLigne > 999 && jeuxEnLigne < 10000){
        odometerJeuxEnLigne.innerHTML = 1983;
    }else if(jeuxEnLigne > 9999 && jeuxEnLigne < 100000){
        odometerJeuxEnLigne.innerHTML = 22220;
    }

    setTimeout(function(){
        odometerJeuxEnLigne.innerHTML = jeuxEnLigne;
    }, 2500);
</script>
<script src="/js/<?php echo $GLOBAL['versionJS'];?>/odometre.js"></script>