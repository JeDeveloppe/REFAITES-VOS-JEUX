<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$tva = $donneesConfig[6]['valeur'];
include('../../controles/fonctions/calculePrix.php');
$titreDeLaPage = "[ADMIN] - Gestion d'un jeu";
$descriptionPage = "";

if(!isset($_GET['jeu'])){
    $_SESSION['alertMessage'] = "Pas de jeu sélectionné !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit(); 
}else{
    require('../../controles/fonctions/validation_donnees.php');
    
    $jeu = valid_donnees($_GET['jeu']);

        if(empty($_GET['jeu']) || !preg_match('#^[0-9]{1,25}$#', $jeu)){
            $_SESSION['alertMessage'] = "Ce n'est pas un nombre...";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP-REFERER']);
            exit();            
        }else{

            $sqlToutDuJeu = $bdd -> prepare("SELECT * FROM catalogue WHERE idCatalogue = :ligne") ;
            $sqlToutDuJeu->execute(array('ligne' => $jeu)) ;
            $donneesJeu = $sqlToutDuJeu->fetch();
            $count = $sqlToutDuJeu-> rowCount();

            if($count< 1){
                $_SESSION['alertMessage'] = "Jeu inconnu !";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit(); 
            }else{
                  
                include_once("../../commun/haut_de_page.php");
                include_once("../../commun/alertMessage.php");
                $sqlEditeur = $bdd -> query("SELECT DISTINCT editeur FROM catalogue ORDER BY editeur");
                $donneesEditeur = $sqlEditeur-> fetchAll();

                ?>
                <div class="container-fluid">
                    <!-- RETOUR CATALOGUE -->
                    
                    <!-- JEU DU CATALOGUE  -->
                    <div class="row">
                        <div class="card mt-3 p-0 col-11 mx-auto">
                            <div class="card-header bg-dark text-white d-flex justify-content-around">
                                <div class="col align-self-center"><?php echo $donneesJeu['nom']; ?></div> 
                                <div class="col text-right"><?php if($donneesJeu['actif'] == 1){echo '<a class="btn btn-success ml-1" href="/administration/jeu/ctrl/ctrl-jeu-online_offline.php?newValue=0&jeu='.$jeu.'" >EN LIGNE <i class="fas fa-exchange-alt"></i></a>';}else{ echo '<a class="btn btn-danger ml-1" href="/administration/jeu/ctrl/ctrl-jeu-online_offline.php?newValue=1&jeu='.$jeu.'">HORS LIGNE <i class="fas fa-exchange-alt"></i></a>';} ?></div>
                            </div>
                            <div class="card-body p-1">
                                <form class="col-12 d-flex flex-wrap justify-content-around" method="post" action="/administration/jeu/ctrl/ctrl-edition.php" enctype="multipart/form-data">
                                    <!-- Partie image -->
                                    <div class="col-6 mt-4">
                                        <div class="col-12 text-center">
                                            <div class="divImgPresentation">
                                                <?php
                                                if(isset($donneesJeu['imageBlob']) AND $donneesJeu != ""){
                                                    echo '<img class="img-thumbnail border mt-4" src="data:image/jpeg;base64,'.$donneesJeu['imageBlob'].'"/>';
                                                }else{
                                                    echo '<img class="img-thumbnail border mt-4" src="/images/design/default.png"/>';
                                                }
                                            
                                                ?>
                                            </div>                                          
                                        </div>
                                        <div class="form-group col-12 bg-light mt-5">
                                            <div class="col text-center">
                                                <label class="col text-center">Changer l'image:</label>
                                                <div class="image-upload text-center">
                                                    <label for="file-input">
                                                        <i class="fas fa-camera fa-3x cursor-grab"></i>
                                                    </label>
                                                    <input type="file" name="photo" id="file-input" />
                                                </div>
                                            </div>
                                            <div class="col text-warning text-center mt-3 pb-3">
                                                <p>Format d'image accepté: .jpg, .jpeg, .gif, .png</p>
                                                <p>Taille maximum <?php echo $donneesConfig[2]['valeur'];?>MB et dimension minimum <?php echo $donneesConfig[3]['valeur'];?> x <?php echo $donneesConfig[4]['valeur'];?></p>
                                            </div>
                                        </div>
                                    </div>
                                     <!-- Partie texte -->
                                    <div class="col-6 mt-4">
                                        <div class="form-group text-center col-4 mx-auto">
                                            <label for="version3">Dans la version 3:</label>
                                            <select class="form-control" name="v3" required>
                                                <option value="NON" <?php if($donneesJeu['v3'] == "" || $donneesJeu['v3'] == "NON") { echo 'selected';} ?>>NON</option>
s                                               <option value="OUI" <?php if($donneesJeu['v3'] == "OUI") { echo 'selected';} ?>>OUI</option>
                                            </select>
                                        </div>
                                        <div class="form-group text-center col-11 mx-auto">
                                            <label for="nondujeu">Nom du jeu:</label>
                                            <input type="text" name="nom" class="form-control" id="nondujeu" pattern="{1,40}" maxlength="40" value="<?php echo $donneesJeu['nom'];?>">
                                            <small class="form-text text-muted">Maximum 40 caractères</small>
                                        </div>
                                        <div class="form-group text-center col-9 mx-auto">
                                            <label for="anneedujeu">Année du jeu:</label>
                                            <input type="text" name="annee" value="<?php if($donneesJeu['annee'] == "Année inconnue"){echo "?";}else{echo $donneesJeu['annee'];}?>" class="form-control text-center" id="anneedujeu" placeholder="CHIFFRES OU ?" pattern="[0-9\?]{1,4}" maxlength="4">
                                            <small class="form-text text-muted">Maximum 4 chiffres ou le caractère ?</small>
                                        </div>
                                        <div class="form-group text-center">
                                            <label for="editeurdujeu">Éditeur du jeu:</label>
                                            <div class="col-12">
                                                <div class="col-12">
                                                    <select name="editeur1" id="editeur1" class="form-control text-center">
                                                        <option value="">Choisir un editeur existant...</option>
                                                        <?php
                                                            foreach($donneesEditeur as $editeur){
                                                                echo '<option value="'.$editeur['editeur'].'"';
                                                                    if($donneesJeu['editeur'] == $editeur['editeur']){ echo "selected";}
                                                                echo '>'.$editeur['editeur'].'</option>';
                                                                $Editeurs = $sqlEditeur-> fetch();
                                                            }
                                                        ?>
                                                    </select>
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-12 text-center mt-2 mb-3">
                                            <label>Url:</label><br/>
                                            <input class="col-11 mx-auto" type="text" name="urlNom" placeholder="Url propre sans espace uniquement avec tiraits (!!! aucun autre caractère)" value="<?php echo $donneesJeu['urlNom'];?>">
                                        </div>
                                    </div>                          
                                    <div class="jumbotron bg-vos col-6 mt-2 p-2">
                                        Contenu d'un jeu complet:<br/>
                                        <p class="mt-2">
                                        <?php
                                        $sqlContenuJeu = $bdd->query("SELECT * FROM pieces WHERE idJeu =".$jeu);
                                        $donneesContenuJeu = $sqlContenuJeu->fetch();
                                        if($donneesContenuJeu['contenu_total'] == ''){
                                            $pieces = null;
                                        }else{
                                            $pieces = $donneesContenuJeu['contenu_total'];
                                        }
                                        echo '<textarea name="pieces" cols="40" rows="5" class="p-4" placeholder="(vide)">'.$pieces.'</textarea>';
                                        ?>
                                        </p>
                                    </div>
                                    <div class="col-6 text-center mt-2 mb-3">
                                        <label>Message spécial:</label><br/>
                                        <textarea name="messageSpecial" cols="30" rows="3" placeholder="Message spécial...max 255 caractères"><?php echo $donneesContenuJeu['message'];?></textarea>
                                    </div>
                                    <!-- PARTIE JEUX COMPLETS: -->
                                    <h3 class="col-12 text-center mt-4">Partie jeu complet:</h3>
                                    <div class="col-12 d-flex flex-wrap justify-content-start">
                                        <div class="form-group text-center col-4">
                                            <label for="nondujeu">Disponible en jeu complet:</label>
                                            <select name="jeuComplet">
                                                <option value="0" <?php if($donneesJeu['isComplet'] == 0) { echo 'selected';} ?>>NON</option>
                                                <option value="1" <?php if($donneesJeu['isComplet'] == 1) { echo 'selected';} ?>>OUI</option>
                                            </select>
                                                <?php
                                                    if($donneesJeu['isComplet'] == 1){
                                                        echo '<a class="btn btn-info" href="/admin/jeu/catalogue/complet/'.$donneesJeu['nom'][0].'/#'.$donneesJeu['idCatalogue'].'">Voir</a>';
                                                    }
                                                ?>
                                        </div>
                                        <div class="form-group text-center col-4">
                                            <label for="poidBoite">Prix TTC de référence:</label>
                                            <input class="text-center" type="text" name="prixHT" placeholder="10.00" pattern="([0-9]{1,2}).([0-9]{2})" value="<?php echo htEnTtc($donneesJeu['prix_HT'],$tva);?>">
                                        </div>
                                        <div class="form-group text-center col-4">
                                            <label for="nondujeu">Disponible à la livraison:</label>
                                            <select name="jeuCompletLivraison">
                                                <option value="0" <?php if($donneesJeu['isLivrable'] == 0) { echo 'selected';} ?>>NON</option>
                                                <option value="1" <?php if($donneesJeu['isLivrable'] == 1) { echo 'selected';} ?>>OUI</option>
                                            </select>
                                        </div>
                                        <div class="form-group text-center col-4">
                                            <label for="poidBoite">Poid boite en gr:</label>
                                            <input class="text-center" type="text" name="poidBoite" placeholder="attention en gramme" value="<?php echo $donneesJeu['poidBoite'];?>">
                                        </div>
                                        <div class="col-4 text-center">
                                            Jeu DEEE:
                                            <select name="deee" required>
                                                <option value="">...</option>
                                                <option value="NON" <?php if($donneesJeu['deee'] == "NON") { echo 'selected';} ?>>NON</option>
s                                               <option value="OUI" <?php if($donneesJeu['deee'] == "OUI") { echo 'selected';} ?>>OUI</option>
                                            </select>
                                        </div>
                                        <div class="col-4 text-center"> 
                                            A partir de:
                                            <select name="age" required>
                                                <option value="">...</option>
                                                <?php
                                                for($age= 1; $age<=10; $age++){
                                                    echo '<option value="'.$age.'"'; if($donneesJeu['age'] == $age) { echo 'selected';} echo '>'.$age.' ans</option>';

                                                }
                                                ?>
                                                <option value="12" <?php if($donneesJeu['age'] == 12) { echo 'selected';} ?>>12 ans</option>
                                                <option value="14" <?php if($donneesJeu['age'] == 14) { echo 'selected';} ?>>14 ans</option>
                                                <option value="18" <?php if($donneesJeu['age'] == 18) { echo 'selected';} ?>>18 ans</option>
                                            </select>
                                        </div>
                                        <div class="col-4 text-center">
                                            Se joue:
                                            <select name="joueurs" required>
                                                <?php
                                                    echo '<option value=""'; if($donneesJeu['nbrJoueurs'] == '') { echo 'selected';} echo'>...</option>';
                                                    for($j=1;$j<=8;$j++){
                                                        echo '<option value="'.$j.'"'; if($donneesJeu['nbrJoueurs'] == $j) { echo 'selected';} echo'>A partir de '.$j.' joueurs</option>';
                                                    }
                                                    echo '<option value="u1"'; if($donneesJeu['nbrJoueurs'] == "u1") { echo 'selected';} echo'>Uniquement à 1 joueur</option>';
                                                    echo '<option value="u2"'; if($donneesJeu['nbrJoueurs'] == "u2") { echo 'selected';} echo'>Uniquement à 2 joueurs</option>';
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12 text-center mt-5">
                                        <!-- ICI TAILLE MAX DE L' IMAGE = 5MB -->
                                        <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
                                        <input type="hidden" name="idDuJeu" value="<?php echo $donneesJeu['idCatalogue'];?>">
                                        <button type="submit" class="btn btn-success border border-primary mb-3">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>        
                </div>
                
            <?php
            }//fin du if count
        }//fin du if pas un nombre
}//fin du if presence du get
include_once("../../commun/bas_de_page-admin.php");
?>