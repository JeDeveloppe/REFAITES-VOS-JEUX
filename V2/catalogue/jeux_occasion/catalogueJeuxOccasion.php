<?php 
@session_start ();
include_once("../../config.php");
// utilisateur non loggé
$titreDeLaPage = "Catalogue des jeux complets disponibles | ".$GLOBALS['titreDePage'];
$descriptionPage = "Les jeux complets d'occasion à petits prix.";
include_once("../../bdd/connexion-bdd.php");
require('../../bdd/table_config.php');
$tva = $donneesConfig[6]['valeur'];
include_once("../../commun/haut_de_page.php");
require("../../controles/fonctions/cleanUrl.php");
include_once("../../commun/alertMessage.php");
require("../../controles/fonctions/validation_donnees.php");

if(!isset($_GET['recherche']) || strlen($_GET['recherche']) < 3){
    $queryRecherche = " AND jeux_complets.stock > 0 AND jeux_complets.actif = 1 ";
}else{
    $recherche = valid_donnees($_GET['recherche']);
    $likesRecherche = "%".strtoupper(str_replace(" ","%",$recherche))."%";
    $queryRecherche = " AND jeux_complets.stock > 0 AND jeux_complets.actif = 1 AND catalogue.nom LIKE '$likesRecherche' OR catalogue.editeur LIKE '$likesRecherche' ";
}

if(isset($_GET['tri']) && preg_match('#u1|u2|2|3|4|5|6|7|8#',$_GET['tri'])){
    $tri = valid_donnees($_GET['tri']);
    $queryTri = " AND catalogue.nbrJoueurs = '$tri' ";
    $urlTri = "&tri=$tri";
}else{
    $queryTri = " AND catalogue.nbrJoueurs != ''";
    $tri = "";
    $urlTri = "&tri=tous";
}

//tri par defaut
$orderByQueryPagination = "jeux_complets.idJeuxComplet DESC";

if(!isset($_GET['age'])){
    $queryAge = "catalogue.age >= 0 ";
    $age = "tous";
}else{
    $age = valid_donnees($_GET['age']);
    if($age == "tous"){
        $queryAge = "catalogue.age >= 0 ";
        $orderByQueryPagination = "jeux_complets.idJeuxComplet DESC";
    }else{
        $queryAge = "catalogue.age >= ".$age;
        $orderByQueryPagination = "catalogue.age ASC";
    }
}

//NOMBRE DE JEUX TOTAL OCCASION
$sqlCatalogue = $bdd -> query("SELECT * FROM jeux_complets WHERE actif = 1");
$nbrJeuxTotalEnLigne = $sqlCatalogue->rowCount();

$querySql = ("SELECT * FROM jeux_complets JOIN catalogue ON catalogue.idCatalogue = jeux_complets.idCatalogue WHERE ".$queryAge.$queryTri.$queryRecherche." ");
$sqlJeuxComplets = $bdd->query($querySql);
$nbrJeuxComplets = $sqlJeuxComplets->rowCount();

$sqlClientLivraison = $bdd -> prepare("SELECT cpLivraison FROM clients WHERE idUser = ?");
$sqlClientLivraison-> execute(array($_SESSION['sessionId']));
$donneesClientLivraison = $sqlClientLivraison->fetch();
$cpClient = $donneesClientLivraison['cpLivraison'] ?? "";
?>

<div class="container-fluid mt-5">
    <h1 class="col-12 text-center mt-5">Les jeux d'occasion</h1>
    <div class="col-11 mx-auto text-center lead text-muted animated faster fadeInRight">
        Uniquement en retrait sur Caen
    </div>
    <div class="col-11 mx-auto text-right mt-5 mt-sm-0">
        <div class="fb-share-button" data-href="<?php echo $GLOBALS['domaineShareFacebook'].$_SERVER['REQUEST_URI']; ?>" data-layout="button" data-size="large"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $GLOBALS['domaineShareFacebook'].$_SERVER['REQUEST_URI'] ; ?>&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Partager</a></div>
    </div>

    <form method="GET" class="mt-5 col-12 col-sm-10 col-md-8 col-lg-12 mx-auto d-flex flex-wrap align-items-center">
        <div class="col-10 col-lg-3 mx-auto mb-3 p-0 text-center">
            <label class="my-1 mr-2 col-12" for="formulaireAge">Nom de jeu ou d'éditeur: </label>
            <input class="form-control col-12 col-sm-10 col-lg-12 mx-auto" type="text" name="recherche" id="rechercheOccasion" placeholder="Recherche dès 3 caractères" value="<?php echo $recherche ?? ''; ?>">
        </div>
        <div class="col-10 col-lg-3 mx-auto col-sm-6 text-center mb-3">
            <label class="my-1 mr-2 col-12" for="formulaireAge">A partir de: </label>
                <select name="age" class="custom-select col-sm-10 col-md-8 col-lg-12" id="formulaireAge"> 
                    <?php
                        echo '<option value="tous"'; if($age == "tous"){ echo ' selected';} echo'>Aucun filtre...</option>';
                        for($j=1;$j<=10;$j++){
                            if($j == 1){
                                $ans = 'an';
                            }else{
                                $ans = 'ans';
                            }
                            echo '<option value="'.$j.'"'; if($age == $j){ echo ' selected';} echo'>'.$j.' '.$ans.'</option>';
                        }
                        echo '<option value="12"'; if($age == "12"){ echo ' selected';} echo'>12 ans</option>';
                        echo '<option value="14"'; if($age == "14"){ echo ' selected';} echo'>14 ans</option>';
                        echo '<option value="18"'; if($age == "18"){ echo ' selected';} echo'>16 ans</option>';
                    ?>
                </select>
        </div>
        <div class="col-10 col-lg-3 mx-auto col-sm-6 mb-3 text-center">
            <label class="my-1 mr-2 col-12" for="formulaireTri">Nombre de joueurs: </label>
                <select name="tri" class="custom-select col-sm-10 col-md-8 col-lg-12" id="formulaireTri"> 
                    <?php
                        echo '<option value="tous"'; if($tri == "tous"){ echo ' selected';} echo'>Aucun filtre...</option>';
                        for($j=2;$j<=8;$j++){
                            echo '<option value="'.$j.'"'; if($tri == $j){ echo ' selected';} echo'>A partir de '.$j.' joueurs</option>';
                        }
                        echo '<option value="u1"'; if($tri == "u1"){ echo ' selected';} echo'>Uniquement à 1 joueur</option>';
                        echo '<option value="u2"'; if($tri == "u2"){ echo ' selected';} echo'>Uniquement à 2 joueurs</option>';
                    ?>
                </select>
        </div>
        <div class="col-10 col-lg-3 mx-auto text-center">
            <div class="btn-group pt-lg-3" role="group">
                <button type="submit" class="btn btn-info">Rechercher</button>
                <a href="/catalogue-jeux-occasion/" class="btn btn-danger">Supprimer les filtres</a>
            </div>
        </div>
    </form>
<?php

//si y a au moins un resultat
if($nbrJeuxComplets > 0){
    require("./paginationOccasion.php");
    if($nombreDePages > 1){
        $urlAge = "&age=".$age;
    }else{
        $urlAge = "?age=".$age;
    }
    $parametresUrl = $urlAge.$urlTri;
        ?>
            <div class="row px-xl-9 mb-4 justify-content-start">
                
                <?php
                    foreach($donneesJeuxCompletPagination as $jeuComplet){
                    
                        $requeteJeux = "SELECT * FROM catalogue WHERE idCatalogue = ".$jeuComplet['idCatalogue'];
                        $sqlJeux = $bdd -> query($requeteJeux);
                        $donneesJeux = $sqlJeux-> fetch();

                        $urlEditeurCatalogue = clean_url($donneesJeux['editeur']);
                        $urlJeuCatalogue = clean_url($donneesJeux['nom']);
                        ?>

                        
                            <div class="col-9 col-sm-5 col-md-4 col-lg-3 p-2 mx-auto mx-md-0 mt-5">
                                <div class="col-12 p-2 border shadow scale-hover h-100 overflow-hidden bg-white">   
                                    <a class="text-decoration-none" href="/jeu-occasion/<?php echo $jeuComplet['idJeuxComplet'].'-'.$donneesJeux['idCatalogue'];?>/<?php echo $urlEditeurCatalogue;?>/<?php echo $donneesJeux['urlNom'];?>/">
                                    <?php
                                    
                                        //LOGO COMME NEUF
                                        if($jeuComplet['isNeuf'] == 1){
                                            echo '<span class="bandeauCommeNeufJeux">COMME NEUF</span>';
                                        }
                                        ?>
                                            <div class="col-12 small text-center p-0 mt-1">
                                                <?php
                                                    if($donneesJeux['nbrJoueurs'] == "u1"){
                                                        echo 'Se joue seul ';
                                                    }else if($donneesJeux['nbrJoueurs'] == "u2"){
                                                        echo 'Se joue à 2 ';
                                                    }else{
                                                        echo 'À partir de '.$donneesJeux['nbrJoueurs'].' joueurs ';
                                                    }
                                                    echo 'et dès '.$donneesJeux['age'].' ans';
                                                ?>
                                            </div>
                                            <!-- image de la boite -->
                                            <div class="col-12 mt-2">
                                                <div class="divImgCatalogue">
                                                    <?php echo '<img src="data:image/jpeg;base64,'.$jeuComplet['imageBlob'].'" alt="Boite du jeu '.$jeuComplet['nom'].' par '.$jeuComplet['editeur'].'" />';?>
                                                </div>
                                            </div>
                                            <div class="col-12 lead text-muted small text-center">
                                                Photo non contractuelle
                                            </div>
                                        
                                            <?php
                                                if(strlen($donneesJeux['nom']) > 17){
                                                    echo '<div class="col-12 mt-4 text-center" data-html="true" data-toggle="tooltip" data-placement="top" title="'.$donneesJeux['nom'].'">'.substr(nl2br($donneesJeux['nom']),0,17).'...</div>';
                                                }else{
                                                    echo '<div class="col-12 mt-4 text-center">'.$donneesJeux['nom'].'</div>';
                                                }  
                                            ?>                                 
                                            <div class="col-12 mt-2 text-center"><?php echo $donneesJeux['editeur']; ?></div>
                                            <div class="col-12 text-center"><?php if($donneesJeux['annee'] == "Année inconnue"){echo "&nbsp;";}else{echo $donneesJeux['annee'];} ?></div>
                                            <div class="col-12 text-center font-weight-bold h6 mt-3">Prix TTC: 
                                                <?php 
                                                    if($jeuComplet['ancienPrixHT'] != 0){
                                                        echo '<del style="color:red">'.number_format(($jeuComplet['ancienPrixHT'] * $tva)/100,2,",",' ').'</del> '.number_format(($jeuComplet['prixHT'] * $tva)/100,2,",",' ').' €';
                                                    }else{
                                                        echo number_format(($jeuComplet['prixHT'] * $tva)/100,2,",",' ').' €';
                                                    }
                                                ?>
                                            </div>
                                    </a>
                                    <div class="col-12 text-center position-relative p-0">
                                        <?php
                                            if(!isset($_SESSION['levelUser'])){
                                                echo '<span id="precisionIdentification" class="col-12 bg-vos pt-3 pb-4">
                                                            <i class="fas fa-lightbulb text-info"></i> Vous devez être identifié(e) pour continuer...
                                                            <div class="col-12 text-center">
                                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                                    <a href="/connexion/" class="btn btn-secondary">Connexion</a>
                                                                    <a href="/inscription/" class="btn btn-secondary">Inscription</a>
                                                                </div>
                                                            </div>
                                                        </span>
                                                        <a class="btn btn-warning border-0 bg- p-1" id="boutonIdentificationRequise"><span class="p-1">Ajouter au panier</span></a>';
                                            }else{
                                                if(!preg_match('#^14#',$cpClient)){
                                                    echo '<span id="precisionIdentification" class="col-11 mx-auto bg-vos p-2">
                                                    <i class="fas fa-lightbulb text-info"></i> Réservé au bassin de Caen pour le moment...
                            
                                                    </span>
                                                    <a class="btn btn-warning border-0 bg- p-1" id="boutonIdentificationRequise"><span class="p-1">Ajouter au panier</span></a>';
                                                }else{
                                                echo '
                                                    <form method="POST" action="/catalogue/jeux_occasion/ctrl/ctrl-panier-jeux_occasion.php">
                                                        <input type="hidden" name="rvjc" value="'.$jeuComplet['idJeuxComplet'].'">
                                                        <button class="btn btn-info pl-3" type="submit">Ajouter au panier</button>
                                                    </form>';
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                
                        <?php
                    }
                ?>
            </div>
                <?php
                    if($nombreDePages > 1){?>
                        <div class="row" id="pagination">
                            <div class="col-12 mt-4">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center">
                                            <?php 
                                            if($nombreDePages == 2){
                                                $variation = 0;
                                            }else{
                                                $variation = 2;
                                            }
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
                                                    echo '<li class="page-item'.$active.'"><a class="page-link" href="/catalogue-jeux-occasion/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                                }
                                                $pageSuivante = $pageActuelle+1;
                                                echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-jeux-occasion/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                                echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-jeux-occasion/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
                                            }
                                            
                                            if($pageActuelle > 1 && $pageActuelle < $nombreDePages - $variation){
                                                $pageAvant = $pageActuelle-1;
                                                echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue-jeux-occasion/?page=1'.$parametresUrl.'"><i class="fas fa-fast-backward"></i></a></li>';
                                                echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue-jeux-occasion/?page='.$pageAvant.$parametresUrl.'"><i class="fas fa-step-backward"></i></a></li>';
                                                    for($i=$pageActuelle-1;$i<=$pageActuelle+$variation;$i++){
                                                        if($pageActuelle == $i){
                                                            $active = " active";
                                                        }else{
                                                            $active = "";
                                                        }
                                                        echo '<li class="page-item'.$active.'"><a class="page-link" href="/catalogue-jeux-occasion/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                                    }
                                                $pageSuivante = $pageActuelle+1;
                                                echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-jeux-occasion/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                                echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-jeux-occasion/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
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
                                                echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue-jeux-occasion/?page=1'.$parametresUrl.'"><i class="fas fa-fast-backward"></i></a></li>';
                                                echo '<li rel="prev" class="page-item"><a class="page-link" href="/catalogue-jeux-occasion/?page='.$pageAvant.$parametresUrl.'"><i class="fas fa-step-backward"></i></a></li>';
                                                    for($i=$pageActuelle-$back;$i<=$nombreDePages;$i++){
                                                        if($pageActuelle == $i){
                                                            $active = " active";
                                                        }else{
                                                            $active = "";
                                                        }
                                                        echo '<li class="page-item'.$active.'"><a class="page-link" href="/catalogue-jeux-occasion/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                                    }
                                                if($pageActuelle < $nombreDePages){
                                                    $pageSuivante = $pageActuelle+1;
                                                    echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-jeux-occasion/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                                    echo '<li rel="next" class="page-item"><a class="page-link" href="/catalogue-jeux-occasion/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
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
                                <p>Actuellement il y a <span data-html="true" data-toggle="tooltip" data-placement="top" id="odometerJeuxEnLigne" class="odometer"></span> jeux d'occasion en ligne.</p>
                            </div>
                        </div>
                    <?php
                    }
}// fin de resultat
else{
    $sqlPartenaires = $bdd->query("SELECT * FROM partenaires WHERE complet = 1 AND ecommerce = 1 AND idPartenaire != 10 ORDER BY RAND()");
    $donneesPartenaires = $sqlPartenaires->fetchAll();

    echo '<div class="row px-xl-9 justify-content-start mt-5">';
    echo '<div class="col-12 text-center">
        <p class="h2">Nous n\'avons pas ce jeu en stock.</p>
        <p class="h5">Vous pouvez suivre nos arrivages sur la page Facebook !</p>    
        <p class="h5 mt-5">Ou tenter votre chance chez l\'un de nos partenaires:</p>
    </div>';
            foreach($donneesPartenaires as $partenaire){ 
                echo '
                    <div class="col-7 col-sm-5 col-md-4 col-lg-3 p-1 mx-auto mx-md-0 animated fadeInRight">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="col-12">
                                    <div class="divImgCatalogue">';
                                        echo '<img src="data:image/jpeg;base64,'.$partenaire['image'].'" alt="Image du partenaire: '.$partenaire['nom'].'" />';
                                    echo '</div>
                                </div>
                                <div class="col-12 mt-2">'.$partenaire['nom'].'</div>
                                <div class="col-12 mt-2"><a href="'.$partenaire['url'].'" target="blank" class="cursor-alias">Voir le site</a></div>
                            </div>
                        </div>
                    </div>';
            }
    echo '</div>';
}
echo '</div>';
require("../../commun/bas_de_page.php");
?>
<script>

    // let blocs = document.querySelectorAll('#blocIdentificationRequise');
    const boutons = document.querySelectorAll('#boutonIdentificationRequise');
    const spans = document.querySelectorAll('#precisionIdentification');

    for(b=0; b<boutons.length; b++){
        let span = spans[b];
        boutons[b].addEventListener('click', () => {

            if(span.style.display == "block"){
                span.style.display = "none";
                
            }else{
                span.style.display = "block";
                let timeout = null;
                // Listen for keystroke events
                clearTimeout(timeout);
                // Make a new timeout set to go off in 1000ms (1 second)
                timeout = setTimeout(function () {
                    span.style.display = "none";
                }, 4000);
            }
        })
    }
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