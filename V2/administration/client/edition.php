<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
$titreDeLaPage = "[ADMIN] - Gestion d'un client";
$descriptionPage = "";

if(!isset($_GET['client'])){
    $_SESSION['alertMessage'] = "Pas de client sélectionné !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit(); 
}else{
    require('../../controles/fonctions/validation_donnees.php');
    
    $client = valid_donnees($_GET['client']);

        if(empty($_GET['client'])){
            $_SESSION['alertMessage'] = "Ce n'est pas un nombre...";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP-REFERER']);
            exit();            
        }else{

            $sqlToutDuclient = $bdd -> prepare("SELECT * FROM clients WHERE idUser = :ligne") ;
            $sqlToutDuclient->execute(array('ligne' => $client)) ;
            $donneesclient = $sqlToutDuclient->fetch();
            $count = $sqlToutDuclient-> rowCount();

            if($count< 1){
                $_SESSION['alertMessage'] = "client inconnu !";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit(); 
            }else{

                $sqlOptgroup = $bdd -> prepare("SELECT * FROM pays WHERE actif = 1 AND rattachement = ? ORDER BY nom_fr_fr ASC");
                $sqlOptgroup-> execute(array("FR-"));
                $donneesOptGroup = $sqlOptgroup-> fetch();

                $sqlPays = $bdd-> prepare("SELECT * FROM pays WHERE actif = 1 AND rattachement = ? ORDER BY nom_fr_fr ASC");
                $sqlPays-> execute(array("NONE"));
                $donneesPays = $sqlPays-> fetchAll();
                require("../../commun/haut_de_page.php");
                require("../../commun/alertMessage.php");
                ?>
                <div class="container-fluid">
                <div class="col-11 mx-auto mt-4"><a class="text-decoration-none" href="/admin/client/liste/"><i class="fas fa-chevron-left"> Retour au fichier client</i></a></div>
                    
                    <!-- BLOC PRESENTATION DU client  -->
                    <div class="col col-md-9 col-xl-10 mx-auto m-2 p-0">

                        <!-- client -->
                        <div class="row">
                            <div class="card mt-3 p-0 col-11 col-sm-9 col-md-8 mx-auto">
                                <div class="card-header bg-dark text-white">
                                    <div class="col align-self-center">Mise à jour d'un client (adresse de facturation uniquement)</div> 
                                </div>
                                    <div class="card-body p-1">
                                        <div class="col-12 mt-4 mb-3">
                                            <form method="post" action="/administration/client/ctrl/ctrl-edition.php">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="">Nom:</span>
                                                    </div>
                                                    <input type="text" name="nom" class="form-control" <?php echo 'value="'.$donneesclient['nomFacturation'].'"';?> pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlength="30" placeholder="Nom" required>
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text border-left-0" id="">Prénom:</span>
                                                    </div>
                                                    <input type="text" name="prenom" class="form-control" <?php echo 'value="'.$donneesclient['prenomFacturation'].'"';?> pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlength="30" placeholder="Prénom" required>
                                                </div>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1">Adresse</span>
                                                    </div>
                                                    <input type="text" name="adresse" class="form-control" placeholder="Numéro et rue" aria-label="Username" aria-describedby="basic-addon1" <?php echo 'value="'.$donneesclient['adresseFacturation'].'"';?> required>
                                                </div>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="">Code postale:</span>
                                                    </div>
                                                    <input type="text" id="codePostale" name="cp" class="form-control col-2" <?php echo 'value="'.$donneesclient['cpFacturation'].'"';?> pattern="[a-zA-Z0-9 -]{4,11}" placeholder="CP" maxlength="11" required>
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text border-left-0" id="">Ville:</span>
                                                    </div>
                                                    <input type="text" name="ville" class="form-control" <?php echo 'value="'.$donneesclient['villeFacturation'].'"';?> pattern="[a-zA-ZÀ-ÿ '-]{2,30}" maxlenght="30" placeholder="Ville" required>
                                                </div>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1">Pays</span>
                                                    </div>
                                                    <select name="pays" id="choixPays" class="custom-select" required>
                                                        <option value="">Choix...</option>
                                                        <option value="FR" <?php
                                                                if($donneesclient['paysFacturation'] == "FR"){
                                                                    echo 'selected';
                                                                }
                                                            ?>>France Métropolitaine</option>
                                                        <optgroup label="DOM- TOM">
                                                            <?php
                                                            while($donneesOptGroup){
                                                                echo '<option value="'.$donneesOptGroup['alpha2'].'"';
                                                                    if($donneesclient['paysFacturation'] == $pays['alpha2']){
                                                                        echo 'selected';
                                                                    }
                                                                    echo '>'.$donneesOptGroup['nom_fr_fr'].'</option>';
                                                            $donneesOptGroup = $sqlOptgroup-> fetch();
                                                            }
                                                            ?>
                                                        </optgroup>
                                                    <?php                        
                                                        foreach($donneesPays as $pays){
                                                            echo '<option value="'.$pays['alpha2'].'"';
                                                                if($donneesclient['paysFacturation'] == $pays['alpha2']){
                                                                    echo 'selected';
                                                                }
                                                            echo '>'.$pays['nom_fr_fr'].'</option>';
                                                        }
                                                    ?>
                                                    </select>
                                                </div>
                                                <div class="col text-center">
                                                    <input type="hidden" name="idDuClient" value="<?php echo $client;?>">
                                                    <button type="submit" class="btn btn-success border border-primary">Mettre à jour</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                            </div>
                        </div>        
                    </div>
                </div>
          
                <?php
            }//fin du if count
        }//fin du if pas un nombre
}//fin du if presence du get
include_once("../../commun/bas_de_page-admin.php");
?>