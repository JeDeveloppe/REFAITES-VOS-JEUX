<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$titreDeLaPage = "[ADMIN] - Gestion d'un categorie";
$descriptionPage = "";

if(!isset($_GET['categorie'])){
    $_SESSION['alertMessage'] = "Pas de catégorie sélectionnée !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit(); 
}else{
    require('../../controles/fonctions/validation_donnees.php');
    
    $categorie = valid_donnees($_GET['categorie']);

        if(empty($_GET['categorie']) || !preg_match('#^[0-9]{1,25}$#', $categorie)){
            $_SESSION['alertMessage'] = "Ce n'est pas un nombre...";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP-REFERER']);
            exit();            
        }else{

            $sqlToutDucategorie = $bdd -> prepare("SELECT * FROM categories WHERE idCategorie = :ligne") ;
            $sqlToutDucategorie->execute(array('ligne' => $categorie)) ;
            $donneescategorie = $sqlToutDucategorie->fetch();
            $count = $sqlToutDucategorie-> rowCount();

            if($count< 1){
                $_SESSION['alertMessage'] = "categorie inconnu !";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit(); 
            }else{
                  
                include_once("../../commun/haut_de_page.php");
                include_once("../../commun/alertMessage.php");
                ?>
                <div class="container">
                    <h2 class="col-12 text-center mt-4">Edition d'une catégorie</h2>
                        <div class="col-12"><a href="/admin/categories/"><- Retour aux catégories</a></div>
                        <!-- categorie DU CATALOGUE  -->
                        <div class="row">
                            <div class="card mt-3 p-0 col-11 col-sm-9 col-md-8 mx-auto">
                                <div class="card-header bg-dark text-white d-flex justify-content-around">
                                    <div class="col align-self-center"><?php echo $donneescategorie['nom']; ?></div> 
                                    <div class="col text-right"><?php if($donneescategorie['actif'] == 1){echo '<a class="btn btn-success ml-1" href="/administration/categories/ctrl/ctrl-categorie-online_offline.php?newValue=0&categorie='.$categorie.'" >EN LIGNE <i class="fas fa-exchange-alt"></i></a>';}else{ echo '<a class="btn btn-danger ml-1" href="/administration/categories/ctrl/ctrl-categorie-online_offline.php?newValue=1&categorie='.$categorie.'">HORS LIGNE <i class="fas fa-exchange-alt"></i></a>';} ?></div>
                                </div>
                                    <div class="card-body p-1">
                                        <div class="col-12 text-center">
                                            <div class="divImgPresentation">
                                                <?php
                                                //on cherche l'image du categorie
                                                $sqlImage = $bdd -> query("SELECT * FROM categories_image WHERE idCategorie = ".$donneescategorie['idCategorie']);
                                                $donneesImage = $sqlImage->fetch();
                                                if(is_array($donneesImage)){
                                                echo '<img class="img-thumbnail border mt-4" src="data:image/jpeg;base64,'.$donneesImage['image'].'"/>'; 
                                                }
                                                ?>
                                            </div>                                          
                                        </div>
                                        <div class="col-12 mt-4 mb-3">
                                            <form method="post" action="/administration/categories/ctrl/ctrl-edition.php" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label for="nom">Nom de la categorie:</label>
                                                    <input type="text" name="nom" class="form-control" pattern="{1,40}" maxlength="40" value="<?php echo $donneescategorie['nom'];?>">
                                                    <small class="form-text text-muted">Maximum 40 caractères</small>
                                                </div>
                                                <div class="form-group">
                                                    <label>Url:</label><br/>
                                                    <input type="text" name="urlNom" class="form-control" placeholder="Url propre sans espace uniquement avec tiraits (!!! aucun autre caractère)" value="<?php echo $donneescategorie['urlNom'];?>">
                                                </div>
                                                <div class="col text-center mt-4">
                                                    <!-- ICI TAILLE MAX DE L' IMAGE = 5MB -->
                                                    <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
                                                    <input type="hidden" name="idCategorie" value="<?php echo $donneescategorie['idCategorie'];?>">
                                                    <button type="submit" class="btn btn-success border border-primary">Enregistrer</button>
                                                </div>
                                            </form>
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