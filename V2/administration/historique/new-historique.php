<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');

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
        <h1 class="col-12 text-center mt-5">Nouveau média</h1>
        <!-- BLOC  -->
        <div class="col-8 mx-auto mt-4">
                <!-- formulaire de modification -->
                <form method="post" class="d-flex" action="/administration/medias/ctrl/ctrl-new.php" name="formulaire" enctype="multipart/form-data">
                    <div class="card col-12 mt-3 p-0 text-dark">
                        <div class="card-header bg-secondary h4 text-white">
                            Titre: <input type="text" name="nom" placeholder=" Titre du média / de la page... Max 80 caractères" maxlenght="80" required><br/>
                        </div>
                            <div class="card-body p-0 pb-2">
                                <div class="col-12 mt-3">
                                    <div class="col-11 text-center p-0 mx-auto">
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
                                    <div class="form-group">
                                        <label class="col text-center">Description:</label>
                                        <textarea class="form-control" rows="7" name="content" onKeyUp="limiteur();" placeholder="Un texte de description..." minlength="15" maxlenght="300" required></textarea>
                                        <small class="form-text text-danger text-center">Entre 15 et 500 caractères.</small>
                                        <div class="small text-center" id="caracteresRestantFormulaireContenuJeu">500 caractères restant...</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col text-center">Lien de la page:</label>
                                        <input type="url" class="form-control" name="url" placeholder="Copier / coller url de la page en entier" required>
                                        <small class="form-text text-danger text-center">Mettre url en entier...</small>
                                    </div>
                                    <div class="form-group col-12">
                                        <label class="col text-center">Date de publication:</label>
                                        <div class="col d-flex">
                                            <div class="form-group col">
                                                <label class="col text-center">Jour</label>
                                                <select class="form-control" name="jour" required>
                                                    <option value=""></option>
                                                    <?php
                                                        for($j = 1; $j < 32; $j++){
                                                            echo '<option value="'.$j.'"> '.$j.' </option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col">
                                                <label class="col text-center">Mois</label>
                                                <select class="form-control" name="mois" required>
                                                    <option value=""></option>
                                                    <?php
                                                        for($m = 01; $m < 13; $m++){
                                                            echo '<option value="'.$m.'"> '.$m.' </option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col">
                                                <label class="col text-center">Année</label>
                                                <select class="form-control" name="annee" required>
                                                    <option value=""></option>
                                                    <?php
                                                        for($an = 2020; $an < 2023; $an++){
                                                            echo '<option value="'.$an.'"> '.$an.' </option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col">
                                                <label class="col text-center">Heure</label>
                                                <select class="form-control" name="heure" required>
                                                    <option value=""></option>
                                                    <?php
                                                        for($h = 0; $h < 24; $h++){
                                                            echo '<option value="'.$h.'"> '.$h.' </option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col">
                                                <label class="col text-center">Minutes</label>
                                                <select class="form-control" name="minutes" required>
                                                    <option value=""></option>
                                                    <?php
                                                        for($min = 0; $min < 60; $min++){
                                                            echo '<option value="'.$min.'"> '.$min.' </option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col text-center">
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