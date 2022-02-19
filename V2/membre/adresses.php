<?php
@session_start ();
require_once('../controles/fonctions/memberOnline.php');
include_once("../config.php");
$titreDeLaPage = "Espace membre | ".$GLOBALS['titreDePage'];
$descriptionPage = "Espace membre";
include_once("../bdd/connexion-bdd.php");

$sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idUser = ?");
$sqlClient-> execute(array($_SESSION['sessionId']));
$donneesClient = $sqlClient->fetch();

$sqlOptgroupFacturation = $bdd -> prepare("SELECT * FROM pays WHERE actif = 1 AND rattachement = ? ORDER BY nom_fr_fr ASC");
$sqlOptgroupFacturation-> execute(array("FR-"));
$donneesOptGroupFacturation = $sqlOptgroupFacturation-> fetch();

$sqlOptgroupLivraison = $bdd -> prepare("SELECT * FROM pays WHERE actif = 1 AND rattachement = ? ORDER BY nom_fr_fr ASC");
$sqlOptgroupLivraison-> execute(array("FR-"));
$donneesOptGroupLivraison = $sqlOptgroupLivraison-> fetch();

$sqlPays = $bdd-> prepare("SELECT * FROM pays WHERE actif = 1 AND rattachement = ? ORDER BY nom_fr_fr ASC");
$sqlPays-> execute(array("NONE"));
$donneesPays = $sqlPays-> fetchAll();

include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
?>

<div class="container-fluid mt-5">

    <?php require_once('./menuMembre.php'); ?>

    <div class="row mt-3" id="secteurfacturation">
        <form method="POST" action="/membre/ctrl/ctrl-adresses.php" class="card col-11 mx-auto col-sm-9 col-md-8 col-lg-6 p-0">
            <div class="card-body d-flex p-0 m-0">
                <div class="col-1 h4 vertical-text border-primary border-left p-0 d-flex justify-content-center align-items-center bg-vos m-0">Adresse de facturation</div>
                <div class="col-11 py-4 pl-4">
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-9">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Organisme:</span>
                            </div>
                            <input type="text" name="organisme-facturation" class="form-control" placeholder="Organisme" pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlength="30" value="<?php echo $donneesClient['organismeFacturation'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-9">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Nom:</span>
                            </div>
                            <input type="text" name="nom-facturation" class="form-control" placeholder="Nom" pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlength="30" value="<?php echo $donneesClient['nomFacturation'] ?? ''; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-9">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Prénom:</span>
                            </div>
                            <input type="text" name="prenom-facturation" class="form-control" placeholder="Prénom" pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlength="30" value="<?php echo $donneesClient['prenomFacturation'] ?? ''; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-9">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Adresse:</span>
                            </div>
                            <input type="text" name="adresse-facturation" class="form-control" placeholder="Numéro et rue" value="<?php echo $donneesClient['adresseFacturation'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Complément d'adresse:</span>
                            </div>
                            <input type="text" name="cadresse-facturation" class="form-control" placeholder="Complément d'adresse" value="<?php echo $donneesClient['cAdresseFacturation'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">CP:</span>
                            </div>
                            <input type="text" id="cp-facturation" name="cp-facturation" class="form-control" pattern="[0-9]{4,5}" placeholder="CP" maxlength="5" value="<?php echo $donneesClient['cpFacturation'] ?? ''; ?>" required>
                        </div>
                        <div class="input-group mb-3 mb-3 col-12 col-md-6">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="ville-facturation">Ville:</label>
                            </div>
                            <select class="custom-select" id="ville-facturation" name="ville-facturation" required></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-9">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Destination:</span>
                            </div>
                            <select name="pays-facturation" id="choixPays" class="custom-select" required>
                                <option value="">Choisir...</option>
                            <?php
                                if($donneesClient['paysFacturation'] == "FR"){ ?>
                                <option value="FR" <?php if(isset($donneesClient['paysFacturation']) && $donneesClient['paysFacturation'] == "FR"){echo ' selected';} ?>>France Métropolitaine</option>
                                <optgroup label="DOM- TOM">
                                    <?php
                                    while($donneesOptGroupFacturation){
                                        echo '<option ';if(isset($donneesClient['paysFacturation']) && $donneesClient['paysFacturation'] == $donneesOptGroupFacturation['alpha2']){echo 'selected ';} echo'value="'.$donneesOptGroupFacturation['alpha2'].'">'.$donneesOptGroupFacturation['nom_fr_fr'].'</option>';
                                    $donneesOptGroupFacturation = $sqlOptgroupFacturation-> fetch();
                                    }
                                    ?>
                                </optgroup>
                            <?php     
                                }
                                if($donneesClient['paysFacturation'] != "FR") {                  
                                    foreach($donneesPays as $pays){
                                        echo '<option value="'.$pays['alpha2'].'"';
                                            if(isset($donneesClient['paysFacturation']) && $donneesClient['paysFacturation'] == $pays['alpha2']){
                                                echo 'selected';
                                            }
                                        echo '>'.$pays['nom_fr_fr'].'</option>';
                                    }
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                            if(isset($_SESSION['alertMessagefacturation'])){
                                echo '<div class="col-12 text-right text-'.$_SESSION['alertMessageConfig'].' alertAdresseSaisieMembre">'.$_SESSION['alertMessagefacturation'].'</div>';
                            }
                            unset($_SESSION['alertMessagefacturation']);
                        ?>
                        <div class="col-12 text-right">
                            <input type="hidden" name="formName" value="facturation">
                            <button type="submit" class="btn btn-success">Mettre à jour</button>
                        </div>
                    </div>
                    <?php
                        if($nbreDeMessage > 0){
                            echo '<div class="row"><div class="col-12"><a class="cursor-alias h6" href="/panier/">Retour au panier</a></div></div>';
                        }
                    ?>
                </div>
            </div>
        </form>
    </div>

    <div class="row mt-5" id="secteurlivraison">
        <form method="POST" action="/membre/ctrl/ctrl-adresses.php" class="card col-11 mx-auto col-sm-9 col-md-8 col-lg-6 p-0">
            <div class="card-body d-flex p-0 m-0">
                <div class="col-1 h4 vertical-text border-primary border-left p-0 d-flex justify-content-center align-items-center bg-refaites m-0 text-white">Adresse de livraison</div>
                <div class="col-11 py-4 pl-4">
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-9">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Organisme:</span>
                            </div>
                            <input type="text" name="organisme-livraison" class="form-control" placeholder="Organisme" pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlength="30" value="<?php echo $donneesClient['organismeLivraison'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-9">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Nom:</span>
                            </div>
                            <input type="text" name="nom-livraison" class="form-control" placeholder="Nom" pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlength="30" value="<?php echo $donneesClient['nomLivraison'] ?? ''; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-9">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Prénom:</span>
                            </div>
                            <input type="text" name="prenom-livraison" class="form-control" placeholder="Prénom" pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlength="30" value="<?php echo $donneesClient['prenomLivraison'] ?? ''; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-9">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Adresse:</span>
                            </div>
                            <input type="text" name="adresse-livraison" class="form-control" placeholder="Numéro et rue" value="<?php echo $donneesClient['adresseLivraison'] ?? ''; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Complément d'adresse:</span>
                            </div>
                            <input type="text" name="cadresse-livraison" class="form-control" placeholder="Complément d'adresse" value="<?php echo $donneesClient['cAdresseLivraison'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-5">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">CP:</span>
                            </div>
                            <input type="text" id="cp-livraison" name="cp-livraison" class="form-control" pattern="[0-9]{4,5}" placeholder="CP" maxlength="5"  value="<?php echo $donneesClient['cpLivraison'] ?? ''; ?>" required>
                        </div>
                        <div class="input-group mb-3 col-12 col-md-7">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Ville:</span>
                            </div>
                            <select class="custom-select" id="ville-livraison" name="ville-livraison" required></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mb-3 col-12 col-md-9">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Destination:</span>
                            </div>
                            <select name="pays-livraison" id="choixPays" class="custom-select" required>
                                <option value="">Choisir...</option>
                                <?php if($donneesClient['paysLivraison'] == "FR"){ ?>
                                    <option value="FR" <?php if($donneesClient['paysLivraison'] == "FR"){echo 'selected';} ?>>France Métropolitaine</option>
                                    <optgroup label="DOM- TOM">
                                        <?php
                                        while($donneesOptGroupLivraison){
                                            echo '<option ';if($donneesClient['paysLivraison'] == $donneesOptGroupLivraison['alpha2']){echo 'selected ';} echo'value="'.$donneesOptGroupLivraison['alpha2'].'">'.$donneesOptGroupLivraison['nom_fr_fr'].'</option>';
                                        $donneesOptGroupLivraison = $sqlOptgroupLivraison-> fetch();
                                        }
                                        ?>
                                    </optgroup>
                            <?php         
                                }               
                                foreach($donneesPays as $pays){
                                
                                    echo '<option value="'.$pays['alpha2'].'"';
                                        if(isset($donneesClient['paysLivraison']) && $donneesClient['paysLivraison'] == $pays['alpha2']){
                                            echo 'selected';
                                        }
                                    echo '>'.$pays['nom_fr_fr'].'</option>';
                                 
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                            if(isset($_SESSION['alertMessagelivraison'])){
                                echo '<div class="col-12 text-right text-success alertAdresseSaisieMembre">'.$_SESSION['alertMessagelivraison'].'</div>';
                            }
                            unset($_SESSION['alertMessagelivraison']);
                        ?>
                        <div class="col-12 text-right">
                            <input type="hidden" name="formName" value="livraison">
                            <button type="submit" class="btn btn-success">Mettre à jour</button>
                        </div>
                    </div>
                    <?php
                        if($nbreDeMessage > 0){
                            echo '<div class="row"><div class="col-12"><a class="cursor-alias h6" href="/panier/">Retour au panier</a></div></div>';
                        }
                    ?>
                </div>
            </div>
        </form>
    </div>
 
</div>
<?php
include_once("../commun/bas_de_page.php");
?>
<script>
    
    let pays = <?php echo json_encode($donneesClient['paysFacturation']); ?>;
    let cpFacturation = document.getElementById('cp-facturation');
    let villeFacturation = document.getElementById('ville-facturation');
    let choixVilleFacturation = <?php echo json_encode($donneesClient['villeFacturation']); ?>;

    let cpLivraison = document.getElementById('cp-livraison');
    let villeLivraison = document.getElementById('ville-livraison');
    let choixVilleLivraison = <?php echo json_encode($donneesClient['villeLivraison']); ?>;

    if(choixVilleFacturation == "" || choixVilleFacturation == null){
        villeFacturation.innerHTML = "<option value=''>Attente du code postal...</option>";
    }else{
        villeFacturation.innerHTML = "<option value='"+choixVilleFacturation+"'>"+choixVilleFacturation+"</option>";
    }
    if(choixVilleLivraison == "" || choixVilleLivraison == null){
        villeLivraison.innerHTML = "<option value=''>Attente du code postal...</option>";
    }else{
        villeLivraison.innerHTML = "<option value='"+choixVilleLivraison+"'>"+choixVilleLivraison+"</option>";
    }

    cpFacturation.addEventListener('keyup', () => {
        if(cpFacturation.value.length > 3){
            fetch('../../requetes/codePostale-ville.php?pays='+pays+'&recherche='+cpFacturation.value)
                .then(response => response.text())
                .then((response) => {
                    villeFacturation.innerHTML = response;
                })
                .catch(err => console.log(err))
         }else{
            villeFacturation.innerHTML = "<option value=''>Attente du code postal...</option>";
        }
    });
    cpLivraison.addEventListener('keyup', () => {
        if(cpLivraison.value.length > 3){
            fetch('../../requetes/codePostale-ville.php?pays='+pays+'&recherche='+cpLivraison.value)
                .then(response => response.text())
                .then((response) => {
                    villeLivraison.innerHTML = response;
                })
                .catch(err => console.log(err))
         }else{
            villeLivraison.innerHTML = "<option value=''>Attente du code postal...</option>";
        }
    });
  
</script>