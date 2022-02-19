<?php
@session_start ();
include('../config.php');
include('../bdd/connexion-bdd.php');
include('../bdd/table_config.php');

if($GLOBAL['versionSITE'] >= 2){
    require_once("../controles/fonctions/memberOnline.php");
}

$MAX_SIZE_FILE = $donneesConfig[2]['valeur'] * 1024 * 1024;

if(!isset($_GET['jeu'])){
    $_SESSION['alertMessage'] = "Pas de jeu sélectionné !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit(); 
}else{
    require('../controles/fonctions/validation_donnees.php');
    
    $jeu = valid_donnees($_GET['jeu']);

        if(empty($_GET['jeu']) || !preg_match('#^[0-9]{1,25}$#', $jeu)){
            $_SESSION['alertMessage'] = "Ce n'est pas un nombre...";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP-REFERER']);
            exit();            
        }else{

            $sqlToutDuJeu = $bdd -> prepare("SELECT * FROM catalogue WHERE idCatalogue = :ligne AND actif = 1") ;
            $sqlToutDuJeu->execute(array('ligne' => $jeu)) ;
            $donneesJeu = $sqlToutDuJeu->fetch();
            $count = $sqlToutDuJeu-> rowCount();

            if($count< 1){
                $_SESSION['alertMessage'] = "Jeu inconnu ou sorti du catalogue!";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: /catalogue/");
                exit(); 
            }else{
                //si ce n'est pas un accessoire
                if($donneesJeu['accessoire_idCategorie'] == 0){
                    $titreDeLaPage = $donneesJeu['nom']." - ".$donneesJeu['editeur']." | ".$GLOBALS['titreDePage'];
                    $descriptionPage = "Il vous manque des pièces pour le jeu ".$donneesJeu['nom']." ? Refaites vos jeux les a peut- être en stock!";
                }else{
                    $titreDeLaPage = $donneesJeu['nom']." | ".$GLOBALS['titreDePage'];
                    $descriptionPage = "Besoin de pièces dans les accessoires ".$donneesJeu['nom']." ? Refaites vos jeux les a peut- être en stock!";
                }
                include_once("../commun/haut_de_page.php");
                include_once("../commun/alertMessage.php");

                if(isset($_SERVER['HTTP_REFERER'])){
                    if(preg_match("#/catalogue/#",$_SERVER['HTTP_REFERER'])){
                        $retour_texte = "Retour au catalogue";
                        $retour_url = $_SERVER['HTTP_REFERER'].'#'.$jeu;
                    }elseif(preg_match("#/accessoires/#",$_SERVER['HTTP_REFERER'])){
                        $retour_texte = "Retour aux accessoires";
                        $retour_url = $_SERVER['HTTP_REFERER'].'#'.$jeu;{
                    }
                    }else{
                        $retour_texte = "Allez au catalogue";
                        $retour_url = "/catalogue/";
                    }
                }else{
                    $retour_texte = "Allez au catalogue";
                    $retour_url = "/catalogue/";  
                }
                ?>

                <div class="container-fluid d-flex flex-column p-0">
                    <!-- RETOUR CATALOGUE -->
                    <div class="row">
                        <div class="col-11 mx-auto text-center mt-4"><a href="<?php echo $retour_url;?>" class="btn btn-warning bg-vos"><?php echo $retour_texte; ?></a></div>
                    </div>
 
                    <?php
                    if(isset($_GET['visite']) && $_GET['bouteille']){
                        echo '
                        <div class="row mt-3">
                            <div class="jumbotron bg-jeux col-6 col-sm-5 col-md-4 col-lg-3 col-xl-2 position-fixed" style="z-index:10; top:20%; right:1%">
                                <div class="col-12 text-center">Satisfait de ce retour<br/> de bouteille à la mer ?</div>
                                <div class="col-12 text-center">
                                    <div class="btn-group">
                                        <form method="POST" action="/bouteille-a-la-mer/ctrl/ctrl-satisfaction-bouteille.php">
                                        <input type="hidden" name="bouteille" value="'.$_GET['bouteille'].'">
                                        <input type="hidden" name="newValue" value="3">
                                        <button class="btn btn-success">OUI</button>
                                        </form>
                                        <form method="POST" action="/bouteille-a-la-mer/ctrl/ctrl-satisfaction-bouteille.php">
                                        <input type="hidden" name="bouteille" value="'.$_GET['bouteille'].'">
                                        <input type="hidden" name="newValue" value="4">
                                        <button class="btn btn-danger ml-2">NON</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                    if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                        echo '<div class="col-11 mx-auto text-right">
                        <div class="fb-share-button" data-href="'.$GLOBALS['domaineShareFacebook'].$_SERVER['REQUEST_URI'].'" data-layout="box_count" data-size="large"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u='.$GLOBALS['domaineShareFacebook'].$_SERVER['REQUEST_URI'].'&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Partager</a></div>
                        </div>';
                    }
                    ?>
                    
                    <!-- BLOC PRESENTATION DU JEU  -->
                    <div class="col-12 mt-3" id="<?php echo $donneesJeux['idCatalogue'];?>">
                        <div class="row">
                            <div class="col-12 text-center mt-2 mt-md-0"><h1><?php echo $donneesJeu['nom']; ?></h1></div>
                            <div class="card p-0 col-11 col-sm-9 col-md-11 col-xl-9 mx-auto text-dark">
                                <div class="card-header bg-secondary h4 text-white text-center">
                                    <?php 
                                        //si ce n'est pas un accessoire
                                        if($donneesJeu['accessoire_idCategorie'] == 0){
                                            echo $donneesJeu['editeur'].' - '.$donneesJeu['annee'];
                                        }else{
                                            echo 'Accessoires';
                                        }
                                    ?>
                                </div>
                                    <div class="card-body p-0 d-md-flex">
                                        <!-- IMAGE + descriptif en vertical -->
                                        <div class="col-12 col-md-6 p-0">
                                        <?php
                                        if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                                            echo '<div class="col-12 mt-2">
                                                    <a href="/admin/jeu/'.$donneesJeu['idCatalogue'].'/edition/"><i class="fas fa-cog fa-2x text-gray-dark ml-3"></i></a>
                                                </div>';
                                        }
                                        ?>
                                            <!-- image de la boite -->
                                            <div class="col-12 text-center p-0">
                                                <div class="divImgPresentation mt-4">
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
                                                        //on cherche l'image du jeu
                                                        $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$donneesJeu['idCatalogue']);
                                                        $donneesImage = $sqlImage->fetch();
                                                        echo '<img class="zoom__image" src="data:image/jpeg;base64,'.$donneesImage['image'].'" alt="Boite du jeu '.$donneesJeux['nom'].' par '.$donneesJeux['editeur'].'" />';
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- editeur et annee -->
                                            <!-- <div class="col-12 text-center"><?php echo $donneesJeu['editeur'].'<br/>'.$donneesJeu['annee'];?>
                                            </div> -->
                                            <!-- contenu d'une boite -->
                                            <div class="card col-11 col-sm-10 mx-auto p-0 mt-5 mb-2 text-primary">
                                                <div class="card-header bg-vos">
                                                    <?php
                                                    //si ce n'est pas un accessoire
                                                    if($donneesJeu['accessoire_idCategorie'] == 0){
                                                        echo 'Contenu d\'un jeu complet :';
                                                    }else{
                                                        echo 'Description:';
                                                    }
                                                    ?>
                                                </div>
                                                <div class="card-body mt-2">
                                                    <ul>
                                                <?php
                                                $sqlContenuJeu = $bdd-> query("SELECT * FROM pieces WHERE idJeu =".$jeu);
                                                $donneesContenuJeu = $sqlContenuJeu->fetch();

                                                //si y a une info
                                                if($donneesContenuJeu['contenu_total'] != ""){
                                                    //si ce n'est pas un accessoire
                                                    if($donneesJeu['accessoire_idCategorie'] == 0){
                                                        $lignes = explode("\n",$donneesContenuJeu['contenu_total']);
                                                        foreach($lignes as $ligne){
                                                            echo '<li >'.$ligne.'</li>';
                                                        }
                                                    }else{
                                                        $lignes = explode("\n",$donneesContenuJeu['contenu_total']);
                                                        foreach($lignes as $ligne){
                                                            echo $ligne;
                                                        }
                                                    }
                                                }else{
                                                    echo 'Aucune information pour le moment...';
                                                }
                                                ?>
                                                </ul>
                                            </div>
                                            </div>
                                        </div>
                                        <!-- formulaire de demande -->
                                        <div class="col-12 col-md-6 mt-md-5 p-0">
                                            <div class="col-12 sticky">
                                                <?php 
                                                    if($donneesContenuJeu['message'] != ""){
                                                        echo '<div class="col-12 text-danger mb-2"><i class="fas fa-bell fa-2x"> Information:</i><p class="pl-4">'.$donneesContenuJeu['message'].'</p></div>';
                                                    }
                                                ?>
                                            
                                                <form method="post" class="border-primary pt-3" action="/catalogue/ctrl/ctrl-FormContactJeu.php" name="formulaire" enctype="multipart/form-data">
                                                    <div class="form-group col-12 p-0">
                                                        <div class="col-12">
                                                            <?php
                                                            //si ce n'est pas un accessoire
                                                            if($donneesJeu['accessoire_idCategorie'] == 0){
                                                            echo 'Vous souhaitez acheter la boite incomplète ou faire une demande de pièces pour ce jeu ?<br/>';
                                                            }
                                                            ?>
                                                            Remplissez ce formulaire et ajoutez cette demande à votre liste !<br />
                                                        </div>
                                                        <div class="col-12 text-center">
                                                            <a href="/comment-ca-marche/passez-une-commande/" target="_blank" data-html="true" data-toggle="tooltip" data-placement="top" title="Aide"><i class="fas fa-question-circle text-info p-2"></i></a>
                                                        </div>

                                                        <textarea class="form-control" rows="3" name="content" onKeyUp="limiteur();" placeholder="Bonjour, avez vous cette pièce ?..." minlength="15" maxlenght="300" required><?php if(isset($_SESSION['content'])){echo$_SESSION['content'];}?></textarea>
                                                        <span id="precision" class="jumbotron bg-vos p-2"><i class="fas fa-lightbulb text-info"></i> N’oubliez pas d’être précis dans votre demande :
                                                            <ul class="m-0">
                                                                <li>nombre de pièces souhaité</li>
                                                                <li>couleur</li>
                                                                <li>forme</li>
                                                                <li>etc...</li>
                                                            </ul>
                                                            <div class="col-12 p-0 text-right">Merci</div>
                                                        </span>
                                                        <small class="form-text text-danger text-center">Entre 15 et 300 caractères.</small>
                                                        <div class="small text-center" id="caracteresRestantFormulaireContenuJeu">300 caractères restant...</div>
                                                    </div>
                                                    <div class="form-group p-0 mt-4">
                                                        <label class="col text-center">Vous pouvez illustrer votre demande avec 2 photos maximum :</label>
                                                        <div class="image-upload text-center">
                                                            <label for="file-input">
                                                                <i class="fas fa-camera fa-3x cursor-grab"></i>
                                                            </label>
                                                            <input type="file" name="photo[]" id="file-input" onchange="getFileInfo()" multiple/>
                                                        </div>
                                                        <div class="col-12 text-center" id="resultatInput"></div>
                                                    </div>
                                                    <div class="col pl-4 text-center small">
                                                        <p>Format d'image accepté: .jpg, .jpeg, .gif, .png<br/>Taille maximum <?php echo $donneesConfig[2]['valeur'];?>MB et dimension minimum <?php echo $donneesConfig[3]['valeur'];?> x <?php echo $donneesConfig[4]['valeur'];?></p>
                                                    </div>

                                                    <div class="col text-center">
                                                        <!-- ICI TAILLE MAX DE L' IMAGE = 5MB -->
                                                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $MAX_SIZE_FILE; ?>" />
                                                        <input type="hidden" name="idDuJeu" value="<?php echo $donneesJeu['idCatalogue'];?>"> 
                                                        <input type="hidden" id="recaptchaResponse" name="recaptcha-response">         
                                                        <button type="submit" class="btn btn-success mt-3 mb-2">Ajouter à mes demandes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>        
                    </div>
                </div>
                <script>
                    function limiteur(){
                        maximum = 300;
                        champ = document.formulaire.content;
                        indic = document.formulaire.indicateur;

                        if (champ.value.length > maximum){
                        champ.value = champ.value.substring(0, maximum);
                        }
                        else{
                        document.getElementById("caracteresRestantFormulaireContenuJeu").innerHTML = maximum - champ.value.length+" caractères restant...";
                        document.getElementById("precision").style.display = "block";
                        }
                        if(champ.value.length == 0){
                            document.getElementById("precision").style.display = "none";
                        }else{
                            // Init a timeout variable to be used below
                            let timeout = null;
                            // Listen for keystroke events
                            champ.addEventListener('keyup', function (e) {
                                // Clear the timeout if it has already been set.
                                // This will prevent the previous task from executing
                                // if it has been less than <MILLISECONDS>
                                clearTimeout(timeout);
                                // Make a new timeout set to go off in 1000ms (1 second)
                                timeout = setTimeout(function () {
                                    document.getElementById("precision").style.display = "none";
                                }, 4000);
                            });
                        }
                    }
                    var resultatInput = document.getElementById('resultatInput');  
                    resultatInput.innerHTML = "Aucune image !";

                    function getFileInfo(){
                        var countFiles = document.getElementById('file-input').files.length;

                        if(countFiles > 1){
                            resultatInput.innerHTML = "2 images séléctionnées";
                        }else{
                            var name = document.getElementById('file-input').files[0].name;
                            resultatInput.innerHTML = "Image séléctionnée:<br/>"+name;
                        }
                    }
                </script>
                <?php
            }//fin du if count
        }//fin du if pas un nombre
}//fin du if presence du get
require_once("../captcha/captchaGoogle.php");
include_once("../commun/bas_de_page.php");
?>