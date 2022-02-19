<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$titreDeLaPage = "[ADMIN] - Gestion d'un accessoire";
$descriptionPage = "";

$sqlCategorie = $bdd-> query("SELECT * FROM categories");
$donneesCategorie = $sqlCategorie-> fetchAll();

include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");

?>
<div class="container d-flex flex-column flex-md-row p-0">
    <!-- BLOC PRESENTATION DU JEU  -->
    <div class="col col-md-9 col-xl-10 mx-auto m-2 p-0">
        <!-- JEU DU CATALOGUE  -->
        <div class="row">
            <div class="card mt-3 p-0 col-11 col-sm-9 col-md-8 mx-auto">
                <div class="card-header bg-dark text-white d-flex justify-content-around">
                    <div class="col align-self-center">Création d'un accessoire en bdd...</div>
                </div>
                    <div class="card-body p-1">

                        <div class="col-12 mt-2 mb-3">
                            <form method="post" action="/administration/accessoire/ctrl/ctrl-new-edition.php" enctype="multipart/form-data">
                                <div class="form-group mt-5 col-xl-5 mx-auto text-center">
                                    <label>Rattaché à la catégorie:</label>
                                        <select name="categorie" required>
                                            <option value="">...</option>
                                            <?php
                                                foreach($donneesCategorie as $categorie){
                                                    echo '<option value="'.$categorie['idCategorie'].'">'.$categorie['nom'].'</option>';
                                                }
                                            ?>
                                        </select>
                                </div>
                                <div class="form-group col-xl-5 text-center mx-auto mt-3">
                                    <label for="nondujeu">Nom de l'accessoire:</label>
                                    <input type="text" name="nom" class="form-control" id="nondujeu"  pattern="{1,40}" maxlength="40" required>
                                    <small class="form-text text-muted">Maximum 40 caractères</small>
                                </div>
                                <div class="jumbotron bg-vos col-11 mx-auto mb-3 p-3">
                                    Description:<br/>
                                    <textarea name="description" cols="60" rows="5" class="p-4" placeholder="(vide)" required></textarea>
                                </div>
                                <div class="form-group p-0 col-11 mx-auto">
                                    <label class="col text-center text-white">L'image:</label>
                                    <div class="image-upload text-center">
                                        <label for="file-input">
                                            <i class="fas fa-camera fa-3x cursor-grab"></i>
                                        </label>
                                        <input type="file" name="photo" id="file-input" required/>
                                    </div>
                                </div>
                                <div class="col text-warning text-center mt-3 pb-3">
                                    <p>Format d'image accepté: .jpg, .jpeg, .gif, .png</p>
                                    <p>Taille maximum <?php echo $donneesConfig[2]['valeur'];?>MB et dimension minimum <?php echo $donneesConfig[3]['valeur'];?> x <?php echo $donneesConfig[4]['valeur'];?></p>
                                </div>
                                <div class="col text-center">
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
include_once("../../commun/bas_de_page-admin.php");
?>