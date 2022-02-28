<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');

$sqlPartenaires = $bdd->query("SELECT * FROM partenaires ORDER BY pays,nom");
$donneesPartenaires = $sqlPartenaires->fetchAll();



$MAX_SIZE_FILE = $donneesConfig[2]['valeur'] * 1024 * 1024;
                  
    $titreDeLaPage = "[GESTION DES PARTENAIRES] | ".$GLOBALS['titreDePage'];
    $descriptionPage = "";
    include_once("../../commun/haut_de_page.php");
    include_once("../../commun/alertMessage.php");
    ?>
 
    <div class="container-fluid">
        <h1 class="col-12 text-center mt-5">Gestion des partenaires</h1>

        <div class="row my-3 justify-content-center">
            <a href="/carte-des-partenaires/france/" class="btn btn-secondary">Retour sur la page des partenaires &#127467;&#127479;</a>
        </div>

        <div class="row mt-4">
            <div class="col-10 mx-auto">
                <table class="table table-sm text-center">
                    <thead>
                        <th>Pays</th>
                        <th>Nom</th>
                        <th>Département / Province - VILLE</th>
                        <th>Accepte les dons</th>
                        <th>Site E-commerce</th>
                        <th>Vend pièces détachées<br/>et visible catalogue</th>
                        <th>Vend des jeux complets</th>
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        <?php
                            foreach($donneesPartenaires as $partenaire){
                                if($partenaire['pays'] == "FR"){
                                    $paysInTable = "villes_france_free";
                                }else{
                                    $paysInTable = "villes_belgique_free";
                                }
                                $sqlVilleFranceFree = $bdd->prepare("SELECT * FROM $paysInTable WHERE ville_id = ?");
                                $sqlVilleFranceFree->execute(array($partenaire['id_villes_free']));
                                $donneesVilleFranceFree = $sqlVilleFranceFree->fetch();

                                if($partenaire['isActif'] == true){
                                    $isOnLine = '<i class="fas fa-circle text-success"></i>';
                                }else{
                                    $isOnLine = '<i class="fas fa-circle text-danger"></i>';
                                }
                                if($partenaire['ecommerce'] == false){
                                    $colorEcommerce = '<i class="fas fa-times text-danger"></i>';
                                }else{
                                    $colorEcommerce = '<i class="fas fa-check text-success"></i>';
                                }
                                if($partenaire['detachee'] == false){
                                    $colorDetachee = '<i class="fas fa-times text-danger"></i>';
                                }else{
                                    $colorDetachee = '<i class="fas fa-check text-success"></i>';
                                }
                                if($partenaire['complet'] == false){
                                    $colorComplet = '<i class="fas fa-times text-danger"></i>';
                                }else{
                                    $colorComplet = '<i class="fas fa-check text-success"></i>';
                                }
                                if($partenaire['don'] == false){
                                    $colorDon = '<i class="fas fa-times text-danger"></i>';
                                }else{
                                    $colorDon = '<i class="fas fa-check text-success"></i>';
                                }

                                
                                if($partenaire['pays'] == "FR"){
                                    $departement_province = $donneesVilleFranceFree['ville_departement'];
                                    $flag = "&#127467;&#127479;"; //french flag
                                }else{
                                    $departement_province = $donneesVilleFranceFree['province'];
                                    $flag = "&#127463;&#127466;"; //belgium flag
                                }
                                echo '<tr>
                                        <td class="align-middle">'.$flag.'</td>
                                        <td class="align-middle">'.$partenaire['nom'].' '.$isOnLine.'</td>
                                        <td class="align-middle">'.$departement_province.' - '.$donneesVilleFranceFree['ville_nom'].'</td>
                                        <td class="align-middle">'.$colorDon.'</td>
                                        <td class="align-middle">'.$colorEcommerce.'</td>
                                        <td class="align-middle">'.$colorDetachee.'</td>
                                        <td class="align-middle">'.$colorComplet.'</td>
                                     
                                        <td><a class="btn btn-info" href="/admin/partenaires/'.$partenaire['idPartenaire'].'/edition/">Editer</a></td>
                                    </tr>';
                            }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- BLOC  -->
        <div class="row mt-5">
            <div class="col-12 text-center">CREER UN NOUVEAU PARTENAIRE:</div>
            <form method="post" class="col-12" action="/administration/partenaires/ctrl/ctrl-new.php" name="formulaire" enctype="multipart/form-data">
                <div class="card mt-3 p-0 col-12">
                    <div class="card-body d-flex flex-wrap">
                        <div class="form-group col-12">
                            <label class="col-12 text-center">Image du partenaire:</label>
                            <input class="col-12 text-center bg-light" type="file" name="photo" id="fileSelect" required>
                            <p class="mt-4 text-danger text-center">Format d'image accepté: .jpg, .jpeg, .gif, .png <br/>
                            Taille maximum <?php echo $donneesConfig[2]['valeur'];?>MB et dimension minimum <?php echo $donneesConfig[3]['valeur'];?> x <?php echo $donneesConfig[4]['valeur'];?></p>
                        </div>
                        <div class="form-group col-6">
                            <label for="nom">Nom:</label>
                            <input class="form-control" type="text" name="nom" placeholder="Nom du partenaire" size="60" maxlenght="60" required>
                        </div>
                        <div class="form-group col-6">
                            <label class="col text-center">Lien du site web:</label>
                            <input type="url" class="form-control" name="url" placeholder="https://www.nom-du-site.fr" required>
                            <small class="form-text text-danger text-center">Mettre url en entier... .</small>
                        </div>
                        <div class="form-group col-4">
                            <label for="pays">Pays:<sup class="text-danger">*</sup></label>
                            <select name="pays" class="form-control col-12" id="pays" required>
                                <option value=''>...</option>
                                <option value='FR'>FRANCE</option>
                                <option value='BE'>BELGIQUE</option>
                            </select>
                        </div>
                        <div class="form-group col-4">
                            <label for="departement">Département / Province:</label>
                            <select name="departement" class="form-control" id="departement" required>
                                <option value=''>Choisir un pays...</option>
                            </select>
                        </div>
                        <div class="form-group col-4">
                            <label for="ville">Ville:</label>
                            <select class="custom-select" id="ville" name="ville" required>
                                <option value=''>Attente du département ou province...</option>
                            </select>
                        </div>
                        <div class="form-group col-3 mt-3">
                            <label class="col text-center">Accepte les dons:</label>
                            <select name="don" class="form-control" id="port" required>
                                <option value="">...</option>
                                <option value="1">oui</option>
                                <option value="0">non</option>
                            </select>
                        </div>
                        <div class="form-group col-3 mt-3">
                            <label class="col text-center">Vend des pièces détachées:</label>
                            <select name="detachee" class="form-control" id="port" required>
                                <option value="">...</option>
                                <option value="1">oui</option>
                                <option value="0">non</option>
                            </select>
                        </div>
                        <div class="form-group col-3 mt-3">
                            <label class="col text-center">Vend des jeux complet:</label>
                            <select name="complet" class="form-control" id="port" required>
                                <option value="">...</option>
                                <option value="1">oui</option>
                                <option value="0">non</option>
                            </select>
                        </div>
                        <div class="form-group col-3 mt-3 mr-auto">
                            <label class="col text-center">Site E-commerce:</label>
                            <select name="ecommerce" class="form-control" required>
                                <option value="">...</option>
                                <option value="1">oui</option>
                                <option value="0">non</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex flex-wrap mt-3">
                            <div class="form-group col-6">
                                <label for="description" class="col text-center">Description:<sup class="text-danger">*</sup></label>
                                <textarea class="form-control" rows="7" name="description" onKeyUp="limiteur();" placeholder="Un texte de description..." minlength="15" maxlenght="300" required></textarea>
                                <small class="form-text text-danger text-center">Entre 15 et 500 caractères.</small>
                                <div class="small text-center" id="caracteresRestantFormulaireContenuJeu">500 caractères restant...</div>
                            </div>
                            <div class="form-group col-6">
                                <label for="description" class="col text-center">Détail collecte:<sup class="text-danger">*</sup></label>
                                <textarea class="form-control" rows="3" name="collecte" placeholder="Liste des objets collectés..." minlength="15" maxlenght="300" required></textarea>
                                <small class="form-text text-danger text-center">Entre 15 et 500 caractères.</small>
                            </div>
                            <div class="form-group col-6">
                                <label for="vend" class="col text-center">Détail ventes:<sup class="text-danger">*</sup></label>
                                <textarea class="form-control" rows="3" name="vend" placeholder="Liste des objets en vente..." minlength="15" maxlenght="300" required></textarea>
                                <small class="form-text text-danger text-center">Entre 15 et 500 caractères.</small>
                            </div>
                        </div>
                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-success mt-3">Créer</button>
                        </div>
                    </div>
                </div>
            </form>
        </div> 
              
    </div>
<?php
include_once("../../commun/bas_de_page-admin.php");
?>
<script>
    let partenaire = <?php echo json_encode($partenaire); ?>;

    let choix = document.getElementById('pays');
    let departements = document.getElementById('departement');
    let departement = document.getElementById('departement');
    let ville = document.getElementById('ville');

    choix.addEventListener('change', () => {
        let pays = choix.value;
        ville.innerHTML = "<option value=''>Attente du département ou province...</option>";
        fetch('../../requetes/pays_dep-province.php?pays='+pays)
            .then(response => response.text())
            .then((response) => {
                departements.innerHTML = response;
            })
            .catch(err => console.log(err))
    })

    departement.addEventListener('change', () => {
        if(departement.value.length > 1){
            let pays = choix.value;
            fetch('../../../requetes/codePostale-ville-partenaires.php?pays='+pays+'&recherche='+departement.value)
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