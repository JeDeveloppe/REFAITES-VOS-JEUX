<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');

$MAX_SIZE_FILE = $donneesConfig[2]['valeur'] * 1024 * 1024;

if(!isset($_GET['partenaire'])){
    $_SESSION['alertMessage'] = "Pas de partenaire sélectionné !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit(); 
}else{
    require('../../controles/fonctions/validation_donnees.php');
    
    $partenaire = valid_donnees($_GET['partenaire']);

        if(empty($_GET['partenaire']) || !preg_match('#^[0-9]{1,25}$#', $partenaire)){
            $_SESSION['alertMessage'] = "Ce n'est pas un nombre...";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP-REFERER']);
            exit();            
        }else{
            $sqlToutDuPartenaire = $bdd -> prepare("SELECT * FROM partenaires WHERE idPartenaire = :ligne") ;
            $sqlToutDuPartenaire->execute(array('ligne' => $partenaire)) ;
            $donneesPartenaire = $sqlToutDuPartenaire->fetch();
            $count = $sqlToutDuPartenaire-> rowCount();

            if($count< 1){
                $_SESSION['alertMessage'] = "Partenaire inconnu !";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: /accueil/");
                exit(); 
            }else{
                  
                $titreDeLaPage = "[Édition d'un partenaire] | ".$GLOBALS['titreDePage'];
                $descriptionPage = "";
                include_once("../../commun/haut_de_page.php");
                include_once("../../commun/alertMessage.php");
                ?>
                <script>
                    let partenaire = <?php echo json_encode($partenaire); ?>;

                    function limiteur(){
                        maximum = 500;
                        champ = document.formulaire.description;
                        indic = document.formulaire.indicateur;

                        if (champ.value.length > maximum)
                        champ.value = champ.value.substring(0, maximum);
                        else
                        document.getElementById("caracteresRestantFormulaireContenuJeu").innerHTML = maximum - champ.value.length+" caractères restant...";
                    }
                    function confirmationDelete(){
                        var val = confirm("Vous êtes sûr de vouloir supprimer ce partenaire ?");
                        if( val == true ) {
                            window.location.href = "/administration/partenaire/ctrl/ctrl-delete.php?partenaire="+partenaire;
                        } else {
                            window.location.href = "/administration/partenaire/edition-partenaire.php?partenaire="+partenaire;  
                        }
                    }

                </script>
                <div class="container-fluid d-flex flex-column p-0">
                    <h1 class="col text-center mt-5">Mise à jour d'un partenaire</h1>
                    <!-- BLOC  -->
                    <div class="col mt-4">
                        <div class="row">
                            <!-- formulaire de modification -->
                            <form method="post" class="d-flex" action="/administration/partenaire/ctrl/ctrl-edition.php" name="formulaire" enctype="multipart/form-data">
                                <div class="card mt-3 p-0 col-md-11 mx-auto text-dark">
                                    <div class="card-header bg-secondary h4 text-white">
                                    Nom: <input type="text" name="nom" value="<?php echo $donneesPartenaire['nom']; ?>" placeholder="Nom du partenaire" size="60" maxlenght="60" required><br/>
                                    Ville: <input type="text" name="ville" value="<?php echo $donneesPartenaire['ville']; ?>" placeholder="Ville du partenaire ou ?" size="40" maxlenght="80" required><br />
                                    Dép: <input type="text" name="departement" value="<?php echo $donneesPartenaire['departement']; ?>"  size="2" maxlenght="2" required></div>
                                        <div class="card-body d-flex p-0 pb-2">
                                            <div class="col-6 text-center p-0">
                                                <div class="divImgPresentation mt-3">
                                                    <?php
                                                    echo '<img src="data:image/jpeg;base64,'.$donneesPartenaire['image'].'"/>'; ?>
                                                </div>
                                                <div class="form-group bg-secondary p-0">
                                                    <label class="col text-center text-white">Image du partenaire:</label>
                                                    <input class="col text-center bg-light p-3" type="file" name="photo" id="fileSelect">
                                                </div>
                                                <div class="col pl-4 text-danger text-center">
                                                    <p>Format d'image accepté: .jpg, .jpeg, .gif, .png</p>
                                                    <p>Taille maximum <?php echo $donneesConfig[2]['valeur'];?>MB et dimension minimum <?php echo $donneesConfig[3]['valeur'];?> x <?php echo $donneesConfig[4]['valeur'];?></p>
                                                </div>
                                            </div>
                                            <div class="col-6 mt-3">
                                                <div class="form-group">
                                                    <label class="col text-center">Description:</label>
                                                    <textarea class="form-control" rows="7" name="description" onKeyUp="limiteur();" placeholder="Un texte de description..." minlength="15" maxlenght="300" required><?php echo $donneesPartenaire['description']; ?></textarea>
                                                    <small class="form-text text-danger text-center">Entre 15 et 500 caractères.</small>
                                                    <div class="small text-center" id="caracteresRestantFormulaireContenuJeu">500 caractères restant...</div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col text-center">Lien du site web:</label>
                                                    <input type="url" class="form-control" name="url" placeholder="https://www.nom-du-site.fr" value="<?php echo $donneesPartenaire['url']; ?>" required>
                                                    <small class="form-text text-danger text-center">Mettre url en entier... .</small>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col text-center">Nom propre du lien:</label>
                                                    <input type="text" class="form-control" name="site" placeholder="ex: La Coop 5 pour 100" value="<?php echo $donneesPartenaire['site']; ?>" required>
                                                </div>

                                                <div class="col text-center">
                                                    <!-- ICI TAILLE MAX DE L' IMAGE = 5MB -->
                                                    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $MAX_SIZE_FILE; ?>" />
                                                    <input type="hidden" name="idDuPartenaire" value="<?php echo $donneesPartenaire['idPartenaire'];?>">
                                                    <button type="submit" class="btn btn-success mt-3">Mettre à jour</button>
                                                    <a href="/partenaires/#<?php echo $donneesPartenaire['idPartenaire'];?>" class="btn btn-info">Retour sur la page des partenaires</a>
                                                    <button class="btn btn-danger" onclick="confirmationDelete()">Supprimer</button>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </form>
                        </div>        
                    </div>
                </div>
                <?php
            }//fin du if count
        }//fin du if pas un nombre
}//fin du if presence du get

include_once("../../commun/bas_de_page-admin.php");
?>