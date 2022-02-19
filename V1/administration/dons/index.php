<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$titreDeLaPage = "[ADMIN] - Gestion des demandes de don";
$descriptionPage = "";
       
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");

$sqlDons = $bdd->query("SELECT * FROM dons ORDER BY nom ASC");
$donneesDons = $sqlDons->fetch();

?>
<div class="container-fluid">
    <h1 class="col-12 text-center mt-4">Les demandes de don</h1>
        <div class="row">
            <div class="card mt-3 p-0 col-11 mx-auto">
                <div class="card-header bg-dark text-white d-flex justify-content-around">
                    <div class="col align-self-center">Création d'une demande de don en bdd...</div>
                    <div class="col text-right"><button class="btn btn-danger ml-1">HORS LIGNE</div>
                </div>
                    <div class="card-body">
                            <form class="d-flex" method="post" action="/administration/dons/ctrl/ctrl-new.php" enctype="multipart/form-data">
                                <div class="form-group col text-center mx-auto">
                                    Nom du jeu:
                                    <input type="text" name="nom" class="form-control" id="nondujeu"  pattern="{1,40}" maxlength="40" required>
                                    <small class="form-text text-muted">Maximum 40 caractères</small>
                                </div>
                                <div class="form-group col-2">
                                    <label class="col text-center text-white">L'image:</label>
                                    <div class="image-upload text-center">
                                        <label for="file-input">
                                            <i class="fas fa-camera fa-3x cursor-grab"></i>
                                        </label>
                                        <input type="file" name="photo" id="file-input" required/>
                                    </div>
                                </div>
                                <div class="col text-warning text-center">
                                    <p>Format d'image accepté:<br/> .jpg, .jpeg, .gif, .png</p>
                                    <p>Taille maximum <?php echo $donneesConfig[2]['valeur'];?>MB et dimension minimum <?php echo $donneesConfig[3]['valeur'];?> x <?php echo $donneesConfig[4]['valeur'];?></p>
                                </div>
                                <div class="form-group col text-center mx-auto">
                                    Demande:
                                    <select name="demande" required>
                                        <option value="">...</option>
                                        <option value="2">6 demandes et +</option>
                                        <option value="1">de 1 a 5 demandes</option>
                                    </select>
                                </div>
                                <div class="col text-center">
                                    <!-- ICI TAILLE MAX DE L' IMAGE = 5MB -->
                                    <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
                                    <button type="submit" class="btn btn-success border border-primary">Enregistrer</button>
                                </div>
                            </form>
                    </div>
            </div>
        </div>     
        <div class="row mt-4">
            <table class="table table-sm col-11 text-center mx-auto">
                <thead>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Dernière modification</th>
                    <th>Demande</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    <?php
                        while($donneesDons){
                            ?>
                                <tr>
                                    <td>
                                        <div class="divImgPresentationExempleAdmin">
                                            <img class="img-thumbnail border" <?php echo 'src="data:image/jpeg;base64,'.$donneesDons['image'].'"'; ?> /></td>
                                        </div>
                                    <td class="align-middle"><?php echo $donneesDons['nom'];?></td>
                                    <td class="align-middle"><?php echo date("d.m.Y",$donneesDons['time']);?></td>
                                    <td class="align-middle">
                                        <?php 
                                            if($donneesDons['demande'] == 2){
                                                echo '<i class="fas fa-thermometer-full text-danger fa-2x"></i>';
                                            }else{
                                                echo '<i class="fas fa-thermometer-full text-warning fa-2x"></i>';
                                            }
                                        ?>
                                    </td>
                                    <td class="align-middle">
                                        <div class="btn-group">
                                            <?php
                                                if($donneesDons['actif'] == 0){
                                                    echo '<a href="/administration/dons/ctrl/ctrl-don-online_offline.php?don='.$donneesDons['idDons'].'&newValue=1" class="btn btn-danger">Mettre en ligne</a>';
                                                }else{
                                                    echo '<a href="/administration/dons/ctrl/ctrl-don-online_offline.php?don='.$donneesDons['idDons'].'&newValue=0" class="btn btn-success">Mettre hors ligne</a>';
                                                }
                                            
                                                echo '<a href="/administration/dons/ctrl/ctrl-don-delete.php?don='.$donneesDons['idDons'].'" class="btn btn-danger"><i class="fas fa-trash-alt"> Supprimer</i></a>';
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                        $donneesDons = $sqlDons->fetch();
                        }
                    ?>
                </tbody>
            </table>
        </div>   
</div>
                
<?php
include_once("../../commun/bas_de_page-admin.php");
?>