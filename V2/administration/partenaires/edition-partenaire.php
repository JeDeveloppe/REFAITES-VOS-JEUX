<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');

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
            $sqlToutDuPartenaire = $bdd ->prepare("SELECT * FROM partenaires WHERE idPartenaire = :ligne") ;
            $sqlToutDuPartenaire->execute(array('ligne' => $partenaire)) ;
            $donneesPartenaire = $sqlToutDuPartenaire->fetch();
            $count = $sqlToutDuPartenaire-> rowCount();            

            if($count< 1){
                $_SESSION['alertMessage'] = "Partenaire inconnu !";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: /");
                exit(); 
            }else{
                if($donneesPartenaire['pays'] == "FR"){
                    $paysInTable = "villes_france_free";
                }else{
                    $paysInTable = "villes_belgique_free";
                }
                $sqlVilleFranceFree = $bdd->prepare("SELECT * FROM $paysInTable WHERE ville_id = ?");
                $sqlVilleFranceFree->execute(array($donneesPartenaire['id_villes_free']));
                $donneesVilleFranceFree = $sqlVilleFranceFree->fetch();
                $villePartenaire = $donneesVilleFranceFree['ville_nom'] ?? '';
                $iDvillePartenaire = $donneesVilleFranceFree['ville_id'] ?? '';

                if($donneesPartenaire['pays'] == "FR"){
                    $departement_province = $donneesVilleFranceFree['ville_departement'];
                }else{
                    $departement_province = $donneesVilleFranceFree['province'];
                }

                $titreDeLaPage = "[Édition d'un partenaire] | ".$GLOBALS['titreDePage'];
                $descriptionPage = "";
                include_once("../../commun/haut_de_page.php");
                include_once("../../commun/alertMessage.php");
                ?>
             
                <div class="container-fluid">
                    <h1 class="col-12 text-center mt-5">Mise à jour d'un partenaire</h1>
                    <div class="col-11 mx-auto"><a class="text-decoration-none" href="/admin/partenaires/"><i class="fas fa-chevron-left"> Retour à la liste des partenaires</i></a></div>
                    
                    <!-- BLOC PARTENAIRE -->
                    <div class="row mt-4">
                        <!-- formulaire de modification -->
                        <form method="post" class="d-flex" action="/administration/partenaires/ctrl/ctrl-edition.php" name="formulaire" enctype="multipart/form-data">
                            <div class="card mt-3 p-0 col-md-11 mx-auto">
                                <div class="card-body d-flex flex-wrap">
                                    <div class="col-6 d-flex flex-wrap">
                                        <div class="form-group col-6">
                                            <label for="nom">Nom:<sup class="text-danger">*</sup></label>
                                            <input class="form-control" type="text" name="nom" value="<?php echo $donneesPartenaire['nom']; ?>" placeholder="Nom du partenaire" size="60" maxlenght="60" required>
                                        </div>
                                        <div class="form-group col-6">
                                            <label for="onLine">En ligne:<sup class="text-danger">*</sup></label>
                                            <select name="onLine" class="form-control col-12" id="onLine" required>
                                                <option value='1' <?php if($donneesPartenaire['isActif'] == 1){echo 'selected'; }?>>En ligne</option>
                                                <option value='0' <?php if($donneesPartenaire['isActif'] == 0){echo 'selected'; }?>>Hors ligne</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-4">
                                            <label for="pays">Pays:<sup class="text-danger">*</sup></label>
                                            <select name="pays" class="form-control col-12" id="pays" required>
                                                <option value='FR' <?php if($donneesPartenaire['pays'] == "FR"){echo 'selected'; }?>>FRANCE</option>
                                                <option value='BE' <?php if($donneesPartenaire['pays'] == "BE"){echo 'selected'; }?>>BELGIQUE</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-4">
                                            <label for="departement">Département / province:<sup class="text-danger">*</sup></label>
                                            <select name="departement" class="form-control" id="departement" required>
                                                <option value="<?php echo $donneesVilleFranceFree['ville_id'];?>"><?php echo $departement_province ;?></option>
                                            </select>
                                        </div>
                                        <div class="form-group col-4">
                                            <label for="ville">Ville:<sup class="text-danger">*</sup></label>
                                            <select class="custom-select" id="ville" name="ville" required>
                                                <option value="<?php echo $iDvillePartenaire;?>"><?php echo $villePartenaire ;?></option>
                                            </select>
                                        </div>
                                        <div class="form-group col-10">
                                            <label class="col text-center">Lien du site web:<sup class="text-danger">*</sup></label>
                                            <input type="url" class="form-control" name="url" placeholder="https://www.nom-du-site.fr" value="<?php echo $donneesPartenaire['url']; ?>" required>
                                            <small class="form-text text-danger text-center">Mettre url en entier... .</small>
                                        </div>
                                        <div class="col-12 d-flex flex-wrap">
                                            <div class="form-group col-6">
                                                <label class="col text-center">Accepte les dons:</label>
                                                <select name="don" class="form-control" id="port" required>
                                                    <option value="">...</option>
                                                    <option value="1" <?php if($donneesPartenaire['don'] == 1){echo 'selected';} ?>>oui</option>
                                                    <option value="0" <?php if($donneesPartenaire['don'] == 0){echo 'selected';} ?>>non</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-6">
                                                <label class="col text-center">Vend des pièces détachées:</label>
                                                <select name="detachee" class="form-control" id="port" required>
                                                    <option value="">...</option>
                                                    <option value="1" <?php if($donneesPartenaire['detachee'] == 1){echo 'selected';} ?>>oui</option>
                                                    <option value="0" <?php if($donneesPartenaire['detachee'] == 0){echo 'selected';} ?>>non</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-6">
                                                <label class="col text-center">Vend des jeux complet:</label>
                                                <select name="complet" class="form-control" id="port" required>
                                                    <option value="">...</option>
                                                    <option value="1" <?php if($donneesPartenaire['complet'] == 1){echo 'selected';} ?>>oui</option>
                                                    <option value="0" <?php if($donneesPartenaire['complet'] == 0){echo 'selected';} ?>>non</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-6">
                                                <label class="col text-center">Site E-commerce:</label>
                                                <select name="ecommerce" class="form-control" required>
                                                    <option value="">...</option>
                                                    <option value="1" <?php if($donneesPartenaire['ecommerce'] == 1){echo 'selected';} ?>>oui</option>
                                                    <option value="0" <?php if($donneesPartenaire['ecommerce'] == 0){echo 'selected';} ?>>non</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-12 mt-2">
                                                <label for="vend" class="col text-center">Détail ventes:<sup class="text-danger">*</sup></label>
                                                <textarea class="form-control" rows="3" name="vend" placeholder="Liste des objets en vente..." minlength="15" maxlenght="300" required><?php echo $donneesPartenaire['vend']; ?></textarea>
                                                <small class="form-text text-danger text-center">Entre 15 et 500 caractères.</small>
                                            </div>
                                        </div>
                                    
                                    </div>
                                    <div class="col-6 text-center">
                                        <div class="divImgPresentationExempleAdmin my-5">
                                            <?php
                                            echo '<img src="data:image/jpeg;base64,'.$donneesPartenaire['image'].'"/>'; ?>
                                        </div>
                                        <div class="form-group bg-secondary p-0 mt-5 col-9 mx-auto">
                                            <label class="col text-center text-white">Image du partenaire:</label>
                                            <input class="col text-center bg-light p-3" type="file" name="photo" id="fileSelect">
                                        </div>
                                        <div class="col pl-4 text-danger text-center">
                                            <p>Format d'image accepté: .jpg, .jpeg, .gif, .png</p>
                                            <p>Taille maximum <?php echo $donneesConfig[2]['valeur'];?>MB et dimension minimum <?php echo $donneesConfig[3]['valeur'];?> x <?php echo $donneesConfig[4]['valeur'];?></p>
                                        </div>
                                        <div class="form-group">
                                            <label for="description" class="col text-center">Description:<sup class="text-danger">*</sup></label>
                                            <textarea class="form-control" rows="7" name="description" onKeyUp="limiteur();" placeholder="Un texte de description..." minlength="15" maxlenght="300" required><?php echo $donneesPartenaire['description']; ?></textarea>
                                            <small class="form-text text-danger text-center">Entre 15 et 500 caractères.</small>
                                            <div class="small text-center" id="caracteresRestantFormulaireContenuJeu">500 caractères restant...</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="description" class="col text-center">Détail collecte:<sup class="text-danger">*</sup></label>
                                            <textarea class="form-control" rows="3" name="collecte" placeholder="Liste des objets collectés..." minlength="15" maxlenght="300" required><?php echo $donneesPartenaire['collecte']; ?></textarea>
                                            <small class="form-text text-danger text-center">Entre 15 et 500 caractères.</small>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center mt-4">
                                        <input type="hidden" name="idDuPartenaire" value="<?php echo $donneesPartenaire['idPartenaire'];?>">
                                        <button type="submit" class="btn btn-success mt-3">Mettre à jour</button>
                                        <a href="/carte-des-partenaires/france/" class="btn btn-info">Retour sur la page des partenaires</a>
                                        <a href="/administration/partenaires/ctrl/ctrl-delete.php?partenaire=<?php echo $donneesPartenaire['idPartenaire'];?>" class="btn btn-danger" >Supprimer</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>        
                </div>
                <?php
            }//fin du if count
        }//fin du if pas un nombre
}//fin du if presence du get

include_once("../../commun/bas_de_page-admin.php");
?>
<script>
    // let partenaire = <?php echo json_encode($partenaire); ?>;
    // let choixVille = <?php echo json_encode($villePartenaire); ?>;
    // let iDchoixVille = <?php echo json_encode($iDvillePartenaire); ?>;
    // let departement = document.getElementById('departement');
    // let ville = document.getElementById('ville');

    // if(choixVille == ""){
    //     ville.innerHTML = "<option value=''>Attente du code postal...</option>";
    // }else{
    //     ville.innerHTML = "<option value='"+iDchoixVille+"'>"+choixVille+"</option>";
    // }

    // departement.addEventListener('change', () => {
    //     if(departement.value.length > 1 && departement.value.match(/[0-9]{2}/g)){
    //         fetch('../../../../requetes/codePostale-ville-partenaires.php?recherche='+departement.value)
    //             .then(response => response.text())
    //             .then((response) => {
    //                 ville.innerHTML = response;
    //             })
    //             .catch(err => console.log(err))
    //     }else{
    //         ville.innerHTML = "<option value=''>Attente du code postal...</option>";
    //     }
    // });

    let choix = document.getElementById('pays');
    let departements = document.getElementById('departement');
    let ville = document.getElementById('ville');

    choix.addEventListener('change', () => {
        let pays = choix.value;
        console.log(pays);
        ville.innerHTML = "<option value=''>Attente du département ou province...</option>";
        fetch('../../../../requetes/pays_dep-province.php?pays='+pays)
            .then(response => response.text())
            .then((response) => {
                departements.innerHTML = response;
            })
            .catch(err => console.log(err))
    })

    departements.addEventListener('change', () => {
        if(departements.value.length > 1){
            let pays = choix.value;
            fetch('../../../../requetes/codePostale-ville-partenaires.php?pays='+pays+'&recherche='+departement.value)
                .then(response => response.text())
                .then((response) => {
                    ville.innerHTML = response;
                })
                .catch(err => console.log(err))
        }else{
            ville.innerHTML = "<option value=''>Attente du département ou province...</option>";
        }
    });

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