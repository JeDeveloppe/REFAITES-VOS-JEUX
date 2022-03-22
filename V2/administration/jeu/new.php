<?php
@session_start();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$titreDeLaPage = "[ADMIN] - Gestion d'un jeu";
$descriptionPage = "";

include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
$sqlEditeur = $bdd->query("SELECT DISTINCT editeur FROM catalogue ORDER BY editeur");
$donneesEditeur = $sqlEditeur->fetchAll();

?>
<div class="container">
    <!-- JEU DU CATALOGUE  -->
    <div class="row mt-5">
        <div class="card mt-3 p-0 col-11 mx-auto">
            <div class="card-header bg-dark text-white d-flex justify-content-around">
                <div class="col align-self-center">Création d'un nouveau jeu</div>
                <div class="col text-right"><button class="btn btn-danger ml-1">HORS LIGNE</div>
            </div>
            <div class="card-body">
                <form class="col-12 mt-2 d-flex flex-wrap justify-content-around" method="post" action="/administration/jeu/ctrl/ctrl-new-jeu.php" enctype="multipart/form-data">
                    <div class="form-group col-6 text-center">
                        <label for="nom">Nom du jeu:</label>
                        <input type="text" name="nom" class="form-control" id="nondujeu" pattern="{1,50}" maxlength="50" required>
                        <small class="form-text text-muted">Maximum 40 caractères</small>
                    </div>
                    <div class="form-group col-3 text-center">
                        <label for="annee">Année du jeu:</label>
                        <input type="text" name="annee" class="form-control text-center" id="anneedujeu" placeholder="CHIFFRES OU ?" pattern="[0-9\?]{1,4}" maxlength="4" required>
                        <small class="form-text text-muted">Maximum 4 chiffres ou le caractère ?</small>
                    </div>
                    <div class="form-group col-12 text-center mt-4">
                        <label for="editeur1">Éditeur du jeu:</label>
                        <div class="col-12 d-flex justify-content-around">
                            <div class="col-5">
                                <select name="editeur1" id="editeur1" class="form-control text-center" onclick="requiredOff()">
                                    <option value="">Choisir un editeur existant...</option>
                                    <?php
                                    foreach ($donneesEditeur as $editeur) {
                                        echo '<option value="' . $editeur['editeur'] . '">' . $editeur['editeur'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-5">
                                <input type="text" name="editeur2" class="form-control" id="editeur2" placeholder="ou nouveau..." pattern="[a-zA-Z0-9 -']{1,30}" maxlength="30" onclick="requiredOff()" required>
                                <small class="form-text text-muted">Maximum 30 caractères</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group p-0 col-4 mt-4">
                        Contenu d'un jeu complet:<br />
                        <textarea name="pieces" cols="40" rows="5" class="p-4" placeholder="(vide)"></textarea>
                    </div>

                    <div class="form-group p-0 col-4 mt-4">
                        <label class="col text-center text-white">L'image:</label>
                        <div class="image-upload text-center">
                            <label for="file-input">
                                <i class="fas fa-camera fa-3x cursor-grab"></i>
                            </label>
                            <input type="file" name="photo" id="file-input" />
                        </div>
                        <div class="col-11 mx-auto text-warning text-center mt-3 pb-3">
                            <p>Format d'image accepté: .jpg, .jpeg, .gif, .png</p>
                            <p>Taille maximum <?php echo $donneesConfig[2]['valeur']; ?>MB et dimension minimum <?php echo $donneesConfig[3]['valeur']; ?> x <?php echo $donneesConfig[4]['valeur']; ?></p>
                        </div>
                    </div>

                                      <!-- PARTIE JEUX COMPLETS: -->
                    <h3 class="col-12 text-center mt-4">Partie jeu complet:</h3>
                    <div class="col-12 d-flex flex-wrap">
                        <div class="form-group text-center col-4">
                            <label for="nondujeu">Disponible en jeu complet:</label>
                            <select name="jeuComplet">
                                <option value="0">NON</option>
                                <option value="1">OUI</option>
                            </select>
                        </div>
                        <div class="form-group text-center col-4">
                            <label for="poidBoite">Prix HT de référence:</label>
                            <input class="text-center" type="text" name="prixHT" placeholder="10.00" pattern="([0-9]{1,2}).([0-9]{2})">
                        </div> 
                        <div class="form-group text-center col-4">
                            <label for="nondujeu">Disponible à la livraison:</label>
                            <select name="jeuCompletLivraison">
                                <option value="0">NON</option>
                                <option value="1">OUI</option>
                            </select>
                        </div>
                        <div class="form-group text-center col-4">
                            <label for="poidBoite">Poid de la boite:</label>
                            <input type="text" name="poidBoite" placeholder="En gramme">
                        </div>
                        <div class="col-4 text-center">
                            Jeu DEEE:
                            <select name="deee" required>
                                <option value="">...</option>
                                <option value="OUI">OUI</option>
                                <option value="NON">NON</option>
                            </select>
                        </div>
                        <div class="col-4 text-center">
                            A partir de:
                            <select name="age" required>
                                <option value="">...</option>
                                <?php
                                for($age= 1; $age<=10; $age++){
                                    echo '<option value="'.$age.'">'.$age.' ans</option>';

                                }
                                ?>
                                <option value="12">12 ans</option>
                                <option value="14">14 ans</option>
                                <option value="18">18 ans</option>
                            </select>
                        </div>
                        <div class="col-4 text-center">
                            Se joue:
                            <select name="joueurs" required>
                                <?php
                                    echo '<option value="" selected>...</option>';
                                    for($j=1;$j<=8;$j++){
                                        echo '<option value="'.$j.'">A partir de '.$j.' joueurs</option>';
                                    }
                                    echo '<option value="u1">Uniquement à 1 joueur</option>';
                                    echo '<option value="u2">Uniquement à 2 joueurs</option>';
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-12 text-center mt-5">
                        <!-- ICI TAILLE MAX DE L' IMAGE = 5MB -->
                        <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
                        <button type="submit" class="btn btn-success border border-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function requiredOff() {
        var inputEditeur = document.getElementById("editeur2").required;
        if (inputEditeur = true) {
            document.getElementById("editeur1").required = true;
            document.getElementById("editeur2").required = false;
            document.getElementById("editeur2").value = '';
        } else {
            document.getElementById("editeur2").required = true;
            document.getElementById("editeur1").required = false;
        }
    }
</script>

<?php
include_once("../../commun/bas_de_page-admin.php");
?>