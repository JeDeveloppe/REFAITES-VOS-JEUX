<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$titreDeLaPage = "[ADMIN] - Gestion d'un jeu";
$descriptionPage = "";
     
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
                        <div class="col align-self-center">Nouvelle catégorie en bdd...</div>
                        <div class="col text-right"><button class="btn btn-danger ml-1">HORS LIGNE</div>
                    </div>
                        <div class="card-body p-1">
                            <div class="col-12 text-center">
                                <div class="divImgPresentation mt-4">
                                    <img class="img-thumbnail border" src="data:image/jpeg;base64,'.$donneesImage['image'].'"/> 
                                </div>                                          
                            </div>
                            <div class="col-12 mt-2 mb-3">
                                <hr>
                                <form method="post" action="/administration/categories/ctrl/ctrl-new.php" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="nom">Nom de la catégorie</label>
                                        <input type="text" name="nom" class="form-control"  pattern="{1,40}" maxlength="40" required>
                                        <small class="form-text text-muted">Maximum 40 caractères</small>
                                    </div>
                                    <div class="form-group"> 
                                        <label for="editeurdujeu">Url propre:</label>
                                        <input type="text" name="urlNom" class="form-control" placeholder="pour url: sans espace et caractères spéciaux" pattern="[a-z-]{1,30}" maxlength="30" required>
                                        <small class="form-text text-muted">Maximum 30 caractères</small>
                                    </div>
                                    <!-- <div class="form-group p-0 col-11 mx-auto">
                                        <label class="col text-center text-white">L'image:</label>
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
                                    </div> -->
                                    <div class="col text-center">
                                        <!-- ICI TAILLE MAX DE L' IMAGE = 5MB -->
                                        <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
                                        <button type="submit" class="btn btn-success border border-primary">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                </div>
            </div>        
        </div>
    </div>
     
                <?php
include_once("../../commun/bas_de_page-admin.php");
?>