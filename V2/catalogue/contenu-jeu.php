<?php
@session_start ();
include('../config.php');
include('../bdd/connexion-bdd.php');
include('../bdd/table_config.php');

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
                header("Location: /catalogue-pieces-detachees/");
                exit(); 
            }else{
                $titreDeLaPage = $donneesJeu['nom']." - ".$donneesJeu['editeur']." | ".$GLOBALS['titreDePage'];
                $descriptionPage = "Pièces détachées pour ".$donneesJeu['nom']." ? Le service l'a peut- être en stock!";
     
                include_once("../commun/haut_de_page.php");
                include_once("../commun/alertMessage.php");

                if(isset($_SERVER['HTTP_REFERER'])){
                    if(preg_match("#/catalogue-pieces-detachees/#",$_SERVER['HTTP_REFERER'])){
                        $retour_texte = "Retour au catalogue";
                        $retour_url = $_SERVER['HTTP_REFERER'].'#'.$jeu;
                    }elseif(preg_match("#/accessoires/#",$_SERVER['HTTP_REFERER'])){
                        $retour_texte = "Retour aux accessoires";
                        $retour_url = $_SERVER['HTTP_REFERER'].'#'.$jeu;{
                    }
                    }else{
                        $retour_texte = "Allez au catalogue";
                        $retour_url = "/catalogue-pieces-detachees/";
                    }
                }else{
                    $retour_texte = "Allez au catalogue";
                    $retour_url = "/catalogue-pieces-detachees/";  
                } 
                ?>

                <div class="container mt-5">
                    <!-- RETOUR CATALOGUE -->
                    <div class="col-12 text-right"><a href="<?php echo $retour_url;?>" class="btn btn-warning bg-refaites border-primary"><?php echo $retour_texte; ?></a></div>
                    <!-- titre -->
                    <h1 class="col-12 text-center mt-2 mt-sm-0"><?php echo $donneesJeu['nom']; ?></h1>
                    <?php
                        if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                            echo '<div class="col-12 text-center mb-2">'.$donneesJeu['editeur'].' - '.$donneesJeu['annee'].' - <a href="/admin/jeu/'.$donneesJeu['idCatalogue'].'/edition/"><i class="fas fa-cog text-gray-dark"></i></a></div>';
                        }else{
                            echo '<div class="col-12 text-center mb-2">'.$donneesJeu['editeur'].' - '.$donneesJeu['annee'].'</div>';
                        }
                    ?>
            
                    <!-- BLOC PRESENTATION DU JEU  -->
                    <div class="row" id="<?php echo $donneesJeux['idCatalogue'];?>">
                        <div class="card col-11 mx-auto col-md-12 p-0 border shadow">
                            <div class="card-body d-flex flex-wrap">

                                <!-- SHARE BOUTON FACEBOOK -->
                                <div class="col-12 p-0 text-right mb-3">
                                    <div class="fb-share-button" data-href="<?php echo $GLOBALS['domaineShareFacebook'].$_SERVER['REQUEST_URI'];?>" data-layout="button" data-size="large"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $GLOBALS['domaineShareFacebook'].$_SERVER['REQUEST_URI']; ?>&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Partager</a></div>
                                </div>

                                <!-- image de la boite -->
                                <div class="col-12 col-md-6 text-center p-0">
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
                                                echo '<img class="zoom__image" src="data:image/jpeg;base64,'.$donneesJeu['imageBlob'].'" alt="Boite du jeu '.$donneesJeu['nom'].' par '.$donneesJeu['editeur'].'" />';
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                 <!-- contenu d'une boite -->
                                 <div class="card col-12 col-md-6 p-0 text-primary">
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

                                <!-- information d'envois -->
                                <div class="col-12 col-md-9 mx-auto mt-5 p-0">
                                    <table class="table table-sm">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th scope="col">Type de pièce</th>
                                                <th scope="col">Disponible<br/> à la vente</th>
                                                <th scope="col">Disponible en<br/>retrait sur Caen</th>
                                                <th scope="col">Envoi postal<br/>possible</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>- de 3cm d'épaisseur</td>
                                                <td class="text-center">✅</td><td class="text-center">✅</td><td class="text-center">✅</td>
                                            </tr>
                                            <tr>
                                                <td>+ de 3cm d'épaisseur</td>
                                                <td class="text-center">✅</td><td class="text-center">✅</td><td class="text-center">❌</td>
                                            </tr>
                                            <tr>
                                                <td>Plateau de jeu</td>
                                                <td class="text-center">✅</td><td class="text-center">✅</td><td class="text-center">Suivant format</td>
                                            </tr>
                                            <tr>
                                                <td>Règle du jeu</td>
                                                <td class="text-center">❌</td><td class="text-center">❌</td><td class="text-center">❌</td>
                                            </tr>
                                            <tr>
                                                <td>Boite incomplète</td>
                                                <td class="text-center">❌</td><td class="text-center">❌</td><td class="text-center">❌</td>
                                            </tr>
                                            <tr>
                                                <td>Boite seule</td>
                                                <td class="text-center">❌</td><td class="text-center">❌</td><td class="text-center">❌</td>
                                            </tr>
                                        
                                        </tbody>
                                    </table>
                                </div>

                                <!-- message d'information sur un jeu -->  
                                <?php 
                                    if(!empty($donneesContenuJeu['message'])){
                                        echo '<div class="col-12 text-danger mt-5">
                                                <i class="fas fa-bell fa-2x"> Information:</i>
                                                <p class="pl-5">'.$donneesContenuJeu['message'].'</p>
                                            </div>';
                                    }
                                ?>   

                                <!-- question pre-formulaire --> 
                                <div class="col-12 mt-2">
                                    Vous souhaitez faire une demande de pièces pour ce jeu ?
                                    <a href="/comment-ca-marche/passer-une-commande/" target="_blank" data-html="true" data-toggle="tooltip" data-placement="top" title="Aide">
                                    <i class="fas fa-question-circle text-info p-2"></i></a><br/>
                                    Remplissez ce formulaire et ajoutez cette demande à votre panier !
                                </div>

                                <!-- formulaire de demande -->
                                <form class="col-12 p-0 d-flex flex-wrap mt-md-4" method="post" class="border-primary pt-3" action="/catalogue/ctrl/ctrl-FormContactJeu.php" name="formulaire" enctype="multipart/form-data">
                                    <div class="form-group col-12 col-md-6 p-0">
                                        <textarea class="form-control mt-3" rows="3" name="content" onKeyUp="limiteur();" placeholder="Bonjour, avez vous cette pièce ?..." minlength="15" maxlenght="300" required><?php if(isset($_SESSION['content'])){echo$_SESSION['content'];}?></textarea>
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
                                    <div class="form-group p-0 col-12 col-md-6 mt-4 mt-md-0">
                                        <label class="col-12 text-center">Vous pouvez illustrer votre demande avec 2 photos maximum :</label>
                                        <div class="image-upload text-center">
                                            <label for="file-input">
                                                <i class="fas fa-camera fa-3x cursor-grab"></i>
                                            </label>
                                            <input type="file" name="photo[]" id="file-input" onchange="getFileInfo()" multiple/>
                                        </div>
                                        <div class="col-12 text-center" id="resultatInput"></div>
                                        <p class="col-12 text-center mt-2">Format d'image accepté: .jpg, .jpeg, .gif, .png<br/>Taille maximum <?php echo $donneesConfig[2]['valeur'];?>MB et dimension minimum <?php echo $donneesConfig[3]['valeur'];?> x <?php echo $donneesConfig[4]['valeur'];?></p>
                                    </div>

                                    <div class="col-12 text-center">
                                        <!-- ICI TAILLE MAX DE L' IMAGE = 5MB -->
                                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $MAX_SIZE_FILE; ?>" />
                                        <input type="hidden" name="idDuJeu" value="<?php echo $donneesJeu['idCatalogue'];?>"> 
                                        <input type="hidden" id="recaptchaResponse" name="recaptcha-response"> 
                                        <?php
                                            if(!isset($_SESSION['levelUser'])){
                                                echo '<a href="/connexion/" class="btn btn-warning mt-3 mb-2">Merci de vous identifier !</a>';
                                            }else{
                                                echo '<button type="submit" class="btn btn-success mt-3 mb-2">Ajouter au panier</button>';
                                            }  
                                        ?>
                                    </div>
                                </form>
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