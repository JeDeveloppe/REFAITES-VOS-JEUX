<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');

$MAX_SIZE_FILE = $donneesConfig[2]['valeur'] * 1024 * 1024;
                  
    $titreDeLaPage = "[Nouveau partenaire] | ".$GLOBALS['titreDePage'];
    $descriptionPage = "";
    include_once("../../commun/haut_de_page.php");
    include_once("../../commun/alertMessage.php");
    ?>
    <script>
        function limiteur(){
            maximum = 500;
            champ = document.formulaire.description;
            indic = document.formulaire.indicateur;

            if (champ.value.length > maximum)
            champ.value = champ.value.substring(0, maximum);
            else
            document.getElementById("caracteresRestantFormulaireContenuJeu").innerHTML = maximum - champ.value.length+" caractères restant...";
        }
    </script>
    <div class="container d-flex flex-column p-0">
        <h1 class="col-12 text-center mt-5">Nouveau partenaire</h1>
        <!-- BLOC  -->
        <div class="col-12 mt-4">
                <!-- formulaire de modification -->
                <form method="post" class="d-flex" action="/administration/partenaire/ctrl/ctrl-new.php" name="formulaire" enctype="multipart/form-data">
                    <div class="card mt-3 p-0 col-md-11 mx-auto text-dark">
                    <div class="card-header bg-secondary h4 text-white">
                        Nom: <input type="text" name="nom" placeholder="Nom du partenaire" size="60" maxlenght="60" required><br/>
                        Ville: <input type="text" name="ville" placeholder="Ville du partenaire ou juste ?" size="40" maxlenght="80" required><br />
                        Dép: <input type="text" name="departement" placeholder="2 chiffres ou juste ?" size="2" maxlenght="2" required></div>
                            <div class="card-body d-flex p-0 pb-2">
                                <div class="col-6 text-center p-0">
                                    <div class="divImgPresentation mt-3">
                                        <img alt="PAS ENCORE D' IMAGE">
                                    </div>
                                    <div class="form-group bg-secondary p-0">
                                        <label class="col text-center text-white">Image du partenaire:</label>
                                        <input class="col text-center bg-light p-3" type="file" name="photo" id="fileSelect" required>
                                    </div>
                                    <div class="col pl-4 text-danger text-center">
                                        <p>Format d'image accepté: .jpg, .jpeg, .gif, .png</p>
                                        <p>Taille maximum <?php echo $donneesConfig[2]['valeur'];?>MB et dimension minimum <?php echo $donneesConfig[3]['valeur'];?> x <?php echo $donneesConfig[4]['valeur'];?></p>
                                    </div>
                                </div>
                                <div class="col-6 mt-3">
                                    <div class="form-group">
                                        <label class="col text-center">Description:</label>
                                        <textarea class="form-control" rows="7" name="description" onKeyUp="limiteur();" placeholder="Un texte de description..." minlength="15" maxlenght="300" required></textarea>
                                        <small class="form-text text-danger text-center">Entre 15 et 500 caractères.</small>
                                        <div class="small text-center" id="caracteresRestantFormulaireContenuJeu">500 caractères restant...</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col text-center">Lien du site web:</label>
                                        <input type="url" class="form-control" name="url" placeholder="https://www.nom-du-site.fr" maxlength="150" required>
                                        <small class="form-text text-danger text-center">Mettre url en entier... Max 150 caractères .</small>
                                    </div>
                                    <div class="form-group">
                                        <label class="col text-center">Nom propre du lien:</label>
                                        <input type="text" class="form-control" name="site" placeholder="ex: La Coop 5 pour 100" maxlength="50" required>
                                        <small class="form-text text-danger text-center">Max 50 caractères .</small>
                                    </div>

                                    <div class="col text-center">
                                        <!-- ICI TAILLE MAX DE L' IMAGE = 5MB -->
                                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $MAX_SIZE_FILE; ?>" />
                                        <button type="submit" class="btn btn-success mt-3">Créer</button>
                                    </div>
                                </div>
                        </div>
                    </div>
                </form>      
        </div>
    </div>
<?php
include_once("../../commun/bas_de_page-admin.php");
?>