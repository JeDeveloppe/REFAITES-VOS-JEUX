<?php
@session_start ();
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$tva = $donneesConfig[6]['valeur'];

if(!isset($_GET['jeuOccasion'])){
    $_SESSION['alertMessage'] = "Pas de jeu sélectionné !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit(); 
}else{
    require('../../controles/fonctions/validation_donnees.php');
    
    $jeu = valid_donnees($_GET['jeuOccasion']);

        if(empty($_GET['jeuOccasion']) || !preg_match('#^[0-9]{1,25}$#', $jeu)){
            $_SESSION['alertMessage'] = "Ce n'est pas un nombre...";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP-REFERER']);
            exit();            
        }else{

            $sqlToutDuJeuComplet = $bdd -> prepare("SELECT * FROM jeux_complets WHERE idJeuxComplet = :ligne AND stock > 0 AND actif = 1") ;
            $sqlToutDuJeuComplet->execute(array('ligne' => $jeu)) ;
            $donneesJeuComplet = $sqlToutDuJeuComplet->fetch();
            $count = $sqlToutDuJeuComplet-> rowCount();

            if($count< 1){
                $_SESSION['alertMessage'] = "Jeu inconnu ou sorti du catalogue!";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: /catalogue-des-jeux-complets/");
                exit(); 
            }else{
      
                $sqlToutDuJeu = $bdd -> prepare("SELECT * FROM catalogue WHERE idCatalogue = :ligne") ;
                $sqlToutDuJeu->execute(array('ligne' => $donneesJeuComplet['idCatalogue'])) ;
                $donneesJeu = $sqlToutDuJeu->fetch();

                $sqlClientLivraison = $bdd -> prepare("SELECT cpLivraison FROM clients WHERE idUser = ?");
                $sqlClientLivraison-> execute(array($_SESSION['sessionId']));
                $donneesClientLivraison = $sqlClientLivraison->fetch();
                $cpClient = $donneesClientLivraison['cpLivraison'] ?? "";

                $titreDeLaPage = $donneesJeu['nom']." - ".$donneesJeu['editeur']." | ".$GLOBALS['titreDePage'];
                $descriptionPage = "Jeu à petit prix: ".$donneesJeu['nom'];

                include_once("../../commun/haut_de_page.php");
                include_once("../../commun/alertMessage.php");

                if(isset($_SERVER['HTTP_REFERER'])){
                        $retour_texte = "Retour au catalogue";
                        $retour_url = $_SERVER['HTTP_REFERER'];
                }else{
                    //dans tous les cas on retourne au catalogue des pièces détachées
                    $retour_texte = "Retour au catalogue";
                    $retour_url = "/catalogue-jeux-occasion/";  
                }
                ?>

                <div class="container-fluid d-flex flex-column p-0 mt-5">
                    <!-- RETOUR CATALOGUE -->
                    <div class="row">
                        <div class="col-11 mx-auto mt-4 text-center"><a href="<?php echo $retour_url;?>" class="btn btn-warning bg-refaites border-primary"><?php echo $retour_texte; ?></a></div>
                    </div>
                    
                    <!-- BLOC PRESENTATION DU JEU  -->
                    <div class="col-12 mt-3" id="<?php echo $donneesJeux['idCatalogue'];?>"></div>
                        <div class="row">
                            <div class="card p-0 col-11 col-sm-9 col-md-11 col-xl-9 mx-auto text-dark">
                                <div class="card-header h3 text-center"><?php echo $donneesJeu['nom']; ?><br/><span class="small"><?php echo $donneesJeu['editeur']; ?></span></div>
                                    <div class="card-body d-flex flex-wrap">
                                        <!-- IMAGE + descriptif en vertical -->
                                     
                                            <?php
                                            if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                                                echo '<div class="col-12 mt-2">
                                                        <a href="/admin/jeu/'.$donneesJeu['idCatalogue'].'/edition/"><i class="fas fa-cog fa-2x text-gray-dark ml-3"></i></a>
                                                    </div>';
                                            }
                                            ?>
                                            <!-- image de la boite -->
                                            <div class="col-12 col-md-6 text-center p-0">
                                                <div class="divImgPresentation">
                                                    <div class="zoom">
                                                        <div class="zoom__top zoom__left"></div>
                                                        <div class="zoom__top zoom__centre"></div>
                                                        <div class="zoom__top zoom__right"></div>
                                                        <div class="zoom__middle zoom__left"></div>
                                                        <div class="zoom__middle zoom__centre"></div>
                                                        <div class="zoom__middle zoom__right"></div>
                                                        <div class="zoom__bottom zoom__left"></div>
                                                        <div class="zoom__bottom zoom__centre"></div>
                                                        <div class="zoom__bottom zoom__right"></div>
                                                        <?php
                                                            echo '<img class="zoom__image" src="data:image/jpeg;base64,'.$donneesJeu['imageBlob'].'" alt="Boite du jeu '.$donneesJeu['nom'].' par '.$donneesJeu['editeur'].'" />';
                                                        ?>
                                                    </div>
                                                </div>
                                                <span class="col-12 col-md-6 lead text-muted small text-center">
                                                    Photo non contractuelle
                                                </span>
                                            </div>  
                                            
                                            <div class="col-12 col-md-6 mt-4 mt-md-0">
                                                <p>État de la boite: <?php echo $donneesJeuComplet['etatBoite']; ?></p>
                                                <p>État du matériel: <?php echo $donneesJeuComplet['etatMateriel']; ?></p>
                                                <p>Règle du jeu: <?php echo $donneesJeuComplet['regleJeu']; ?></p>
                                                <?php if($donneesJeuComplet['information'] != ""){
                                                    echo '<p><em>Informations:</em></p><p class="ml-5">'.$donneesJeuComplet['information'].'</p>';
                                                }
                                                ?>
                                                <p>
                                                    <?php
                                                        if($donneesJeu['nbrJoueurs'] == 1){
                                                            echo 'A partir de '.$donneesJeu['nbrJoueurs'].' joueur';
                                                        }else if($donneesJeu['nbrJoueurs'] > 1){
                                                            echo 'A partir de '.$donneesJeu['nbrJoueurs'].' joueurs';
                                                        }else{
                                                            if($donneesJeu['nbrJoueurs'] == "u1"){
                                                                $nbrJoueur = "Nombre de joueur: Uniquement 1";
                                                            }else{
                                                                $nbrJoueur = "Nombre de joueur(s): Uniquement 2";
                                                            }
                                                            echo $nbrJoueur;
                                                        }
                                                    ?>
                                                </p>
                                                <p>Dès <?php echo $donneesJeu['age'];?> ans.</p>

                                                <p>Prix TTC: <?php echo number_format(($donneesJeuComplet['prixHT'] * $tva)/100,2,",",' '); ?> €</p>
                                                <div class="position-relative">
                                                <?php
                                                    if(!isset($_SESSION['levelUser'])){
                                                        echo '<span id="precisionIdentificationDetails" class="col-11 col-sm-10 mx-auto col-md-9 bg-vos pt-3 pb-4">
                                                                    <i class="fas fa-lightbulb text-info"></i> Vous devez être identifié(e) pour continuer...
                                                                    <div class="text-center">
                                                                        <div class="btn-group mt-2" role="group" aria-label="Basic example">
                                                                            <a href="/connexion/" class="btn btn-secondary">Connexion</a>
                                                                            <a href="/inscription/" class="btn btn-secondary">Inscription</a>
                                                                        </div>
                                                                    </div>
                                                                </span>
                                                                <a class="btn btn-warning border-0 bg- p-1" id="boutonIdentificationRequise"><span class="p-1">Ajouter au panier</span></a>';
                                                    }else{
                                                        if(strlen($cpClient) < 1){
                                                            echo '<span id="precisionIdentification" class="col-11 mx-auto bg-vos p-2">
                                                            <i class="fas fa-lightbulb text-info"></i> Pensez à renseigner votre <a href="../../membre/adresses/#secteurlivraison">adresse de livraison...</a>
                                    
                                                            </span>
                                                            <a class="btn btn-warning border-0 bg- p-1" id="boutonIdentificationRequise"><span class="p-1">Ajouter au panier</span></a>';
                                                        }else if(!preg_match('#^14#',$cpClient)){
                                                            echo '<span id="precisionIdentificationDetails" class="col-11 col-sm-10 mx-auto col-md-9 bg-vos p-2">
                                                            <i class="fas fa-lightbulb text-info"></i> Réservé au bassin de Caen pour le moment...
                                    
                                                            </span>
                                                            <a class="btn btn-warning border-0 bg- p-1" id="boutonIdentificationRequise"><span class="p-1">Ajouter au panier</span></a>';
                                                        }else{
                                                        echo '
                                                        <form class="text-center" method="POST" action="/catalogue/jeux_occasion/ctrl/ctrl-panier-jeux_occasion.php">
                                                        <input type="hidden" name="rvjc" value="'.$donneesJeuComplet['idJeuxComplet'].'">
                                                        <button class="btn btn-info pl-3" type="submit">Ajouter au panier</button>
                                                    </form>';
                                                        }
                                                    }
                                                ?>
                                                </div>
                                            </div> 
                                    </div>
                            </div> 
                        </div>     
                    </div>
                </div>
                <?php
            }//fin du if count
        }//fin du if pas un nombre
}//fin du if presence du get

include_once("../../commun/bas_de_page.php");
?>
<script>

// let blocs = document.querySelectorAll('#blocIdentificationRequise');
const bouton = document.getElementById('boutonIdentificationRequise');
const span = document.getElementById('precisionIdentificationDetails');

    bouton.addEventListener('click', () => {

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

</script>