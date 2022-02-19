<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$titreDeLaPage = "[ADMIN] - Gestion d'un jaccessoire";
$descriptionPage = "";

if(!isset($_GET['accessoire'])){
    $_SESSION['alertMessage'] = "Pas d'accessoire sélectionné !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit(); 
}else{
    require('../../controles/fonctions/validation_donnees.php');
    
    $accessoire = valid_donnees($_GET['accessoire']);

    $sqlCategorie = $bdd-> query("SELECT * FROM categories");
    $donneesCategorie = $sqlCategorie-> fetchAll();

        if(empty($_GET['accessoire']) || !preg_match('#^[0-9]{1,25}$#', $accessoire)){
            $_SESSION['alertMessage'] = "Ce n'est pas un nombre...";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP-REFERER']);
            exit();            
        }else{

            $sqlToutDeAccessoire = $bdd -> prepare("SELECT * FROM catalogue WHERE idCatalogue = :ligne") ;
            $sqlToutDeAccessoire->execute(array('ligne' => $accessoire)) ;
            $donneesAccessoire = $sqlToutDeAccessoire->fetch();
            $count = $sqlToutDeAccessoire-> rowCount();

            if($count< 1){
                $_SESSION['alertMessage'] = "Accessoire inconnu !";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit(); 
            }else{
                  
                include_once("../../commun/haut_de_page.php");
                include_once("../../commun/alertMessage.php");

                ?>
                <div class="container-fluid d-flex flex-column flex-md-row p-0">
                    <!-- BLOC PRESENTATION DU JEU  -->
                    <div class="col col-md-9 col-xl-10 mx-auto m-2 p-0">
                        <!-- JEU DU CATALOGUE  -->
                        <div class="row">
                            <div class="card mt-3 p-0 col-11 col-sm-9 col-md-8 mx-auto">
                                <div class="card-header bg-dark text-white d-flex justify-content-around">
                                    <div class="col align-self-center"><?php echo $donneesAccessoire['nom']; ?></div> 
                                    <div class="col text-right"><?php if($donneesAccessoire['actif'] == 1){echo '<a class="btn btn-success ml-1" href="/administration/accessoire/ctrl/ctrl-accessoire-online_offline.php?newValue=0&accessoire='.$accessoire.'" >EN LIGNE <i class="fas fa-exchange-alt"></i></a>';}else{ echo '<a class="btn btn-danger ml-1" href="/administration/accessoire/ctrl/ctrl-accessoire-online_offline.php?newValue=1&accessoire='.$accessoire.'">HORS LIGNE <i class="fas fa-exchange-alt"></i></a>';} ?></div>
                                </div>
                                    <div class="card-body p-1">
                                        <div class="col-12 text-center">
                                            <div class="divImgPresentation">
                                                <?php
                                                //on cherche l'image du jeu
                                                $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$donneesAccessoire['idCatalogue']);
                                                $donneesImage = $sqlImage->fetch();
                                                echo '<img class="img-thumbnail border mt-4" src="data:image/jpeg;base64,'.$donneesImage['image'].'"/>'; 
                                                ?>
                                            </div>                                          
                                        </div>
                                        <div class="col-12 mt-4 mb-3">
                                            <form method="post" action="/administration/accessoire/ctrl/ctrl-edition.php" enctype="multipart/form-data">
                                                <div class="form-group mt-5 col-xl-5 mx-auto text-center">
                                                    <label>Rattaché à la catégorie:</label><br/>
                                                        <select name="categorie" required>
                                                            <option value="">...</option>
                                                            <?php
                                                                foreach($donneesCategorie as $categorie){
                                                                    echo '<option value="'.$categorie['idCategorie'].'"';
                                                                    if($donneesAccessoire['accessoire_idCategorie'] == $categorie['idCategorie']){
                                                                        echo 'selected';
                                                                    }
                                                                    echo '>'.$categorie['nom'].'</option>';
                                                                }
                                                            ?>
                                                        </select>
                                                </div>
                                                <div class="form-group col-xl-5 text-center mx-auto">
                                                    <label for="nondujeu">Nom de l'accessoire:</label>
                                                    <input type="text" name="nom" class="form-control" pattern="{1,40}" maxlength="40" value="<?php echo $donneesAccessoire['nom'];?>">
                                                    <small class="form-text text-muted">Maximum 40 caractères</small>
                                                </div>

                                                <div class="jumbotron bg-vos col-11 col-md-8 col-lg-8 mx-auto">
                                                    Description:<br/>
                                                    <p>
                                                    <?php
                                                    $sqlDescriptionAccessoire = $bdd-> query("SELECT * FROM pieces WHERE idJeu =".$accessoire);
                                                    $donneesDescriptionAccessoire = $sqlDescriptionAccessoire->fetch();
                                                    echo '<textarea name="description" cols="40" rows="5" class="p-4" placeholder="(vide)">'.$donneesDescriptionAccessoire['contenu_total'].'</textarea>';
                                                    ?>
                                                    </p>
                                                </div>
                                                <div class="col-12 text-center mt-2 mb-3">
                                                    <label>Message spécial:</label><br/>
                                                    <textarea name="messageSpecial" cols="30" rows="3" placeholder="Message spécial...max 255 caractères"><?php echo $donneesDescriptionAccessoire['message'];?></textarea>
                                                </div>
                                                <div class="col-12 text-center mt-2 mb-3">
                                                    <label>Url:</label><br/>
                                                    <input class="col-8 mx-auto" type="text" name="urlNom" placeholder="Url propre sans espace uniquement avec tiraits (!!! aucun autre caractère)" value="<?php echo $donneesAccessoire['urlNom'];?>">
                                                </div>
                                                <div class="form-group bg-light mt-3">
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
                                                <div class="col text-center">
                                                    <input type="hidden" name="idCatalogue" value="<?php echo $donneesAccessoire['idCatalogue'];?>">
                                                    <button type="submit" class="btn btn-success border border-primary">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                            </div>
                        </div>        
                    </div>
                </div>
                <script>
                    function requiredOff(){
                        var inputEditeur = document.getElementById("editeur2").required;
                        if(inputEditeur = true){
                        document.getElementById("editeur1").required = true;
                        document.getElementById("editeur2").required = false;
                        document.getElementById("editeur2").value = '';
                        }else{
                        document.getElementById("editeur2").required = true;
                        document.getElementById("editeur1").required = false;
                        }
                    }
                </script>
                
                <?php
            }//fin du if count
        }//fin du if pas un nombre
}//fin du if presence du get
include_once("../../commun/bas_de_page-admin.php");
?>