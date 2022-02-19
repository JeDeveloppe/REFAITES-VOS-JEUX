<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');

if(!isset($_GET['historique'])){
    $_SESSION['alertMessage'] = "Pas d'historique'sélectionné !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit(); 
}else{
    require('../../controles/fonctions/validation_donnees.php');
    
    $historique = valid_donnees($_GET['historique']);

        if(empty($_GET['historique']) || !preg_match('#^[0-9]{1,25}$#', $historique)){
            $_SESSION['alertMessage'] = "Ce n'est pas un nombre...";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP-REFERER']);
            exit();            
        }else{
            $sqlToutDuMedia = $bdd -> prepare("SELECT * FROM historique WHERE idHistorique = :ligne") ;
            $sqlToutDuMedia->execute(array('ligne' => $historique)) ;
            $donneesMedia = $sqlToutDuMedia->fetch();
            $count = $sqlToutDuMedia-> rowCount();

            if($count< 1){
                $_SESSION['alertMessage'] = "Historique inconnu !";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: /accueil/");
                exit(); 
            }else{
                  
                $titreDeLaPage = "[Édition d'un historique] | ".$GLOBALS['titreDePage'];
                $descriptionPage = "";
                include_once("../../commun/haut_de_page.php");
                include_once("../../commun/alertMessage.php");
                ?>

    <div class="container-fluid d-flex flex-column p-0">
        <h1 class="col text-center mt-5">Édition d'un historique</h1>
        <!-- BLOC  -->
        <div class="col-12 mt-4">
            <div class="row">
                <!-- formulaire de modification -->
                <form method="post" class="card col-10 mx-auto d-flex" action="/administration/historique/ctrl/ctrl-edition.php" name="formulaire" enctype="multipart/form-data">
                    <div class="card-body p-0 pb-2 d-flex flex-wrap">
                        <div class="form-group col-12 mt-3">
                            <label class="col text-center">Date de publication:<sup class="text-danger">*</sup></label>
                            <div class="col d-flex">
                                <div class="form-group col">
                                    <label class="col text-center">Jour</label>
                                    <select class="form-control" name="jour" required>
                                        <option value=""></option>
                                        <?php
                                            for($j = 1; $j < 32; $j++){
                                                echo '<option value="'.$j.'"';
                                                if(date("d",$donneesMedia['date']) == $j){
                                                    echo 'selected';
                                                }
                                                echo'> '.$j.' </option>';
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
                                                echo '<option value="'.$m.'"';
                                                if(date("m",$donneesMedia['date']) == $m){
                                                    echo 'selected';
                                                }
                                                echo'> '.$m.' </option>';
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
                                                echo '<option value="'.$an.'"';
                                                if(date("Y",$donneesMedia['date']) == $an){
                                                    echo 'selected';
                                                }
                                                echo'> '.$an.' </option>';
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
                                                echo '<option value="'.$h.'"';
                                                if(date("H",$donneesMedia['date']) == $h){
                                                    echo 'selected';
                                                }
                                                echo'> '.$h.' </option>';
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
                                                echo '<option value="'.$min.'"';
                                                if(date("i",$donneesMedia['date']) == $min){
                                                    echo 'selected';
                                                }
                                                echo'> '.$min.' </option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group col-12">
                                <label for="exampleFormControlSelect1">Type d'historique:<sup class="text-danger">*</sup></label>
                                <select name="type" class="form-control" id="exampleFormControlSelect1" required>
                                            <?php
                                                foreach ($GLOBALS['medias_presse'] as $iconeMediaPresse){
                                                    echo '<option value="'.$iconeMediaPresse[0].'"';
                                                    if($donneesMedia['information'] == $iconeMediaPresse[0]){
                                                        echo 'selected';
                                                    }
                                                    echo ' >'.$iconeMediaPresse[1].'</option>';
                                                }
                                            ?>
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label class="col-12">Titre:<sup class="text-danger">*</sup></label>
                                <input class="form-control" type="text" name="nom" placeholder=" Titre du média / de la page... Max 80 caractères" value="<?php echo $donneesMedia['titre']; ?>" maxlenght="80" required>
                                <small class="form-text text-danger text-center">Max 80 caractères.</small>
                            </div>
                            <div class="form-group col-12">
                                <label class="col-12">Lien de la page:</label>
                                <input type="url" class="form-control" name="url" placeholder="Copier / coller url de la page en entier" value="<?php echo $donneesMedia['lien']; ?>">
                                <small class="form-text text-danger text-center">Mettre url en entier...</small>
                            </div>
                        </div>
                        <div class="form-group col-6 mt-4">
                            <label class="col-12 text-center">Description:<sup class="text-danger">*</sup></label>
                            <textarea class="form-control" rows="7" name="content" id="content" onKeyUp="limiteur();" placeholder="Un texte de description..." minlength="15" maxlenght="300" required><?php echo $donneesMedia['content']; ?></textarea>
                            <small class="form-text text-danger text-center">Entre 15 et 250 caractères.</small>
                            <div class="small text-center" id="caracteresRestantContent">250 caractères restant...</div>
                        </div>  
                        <div class="col-12 text-center">
                            <input type="hidden" name="idHistorique" value="<?php echo $donneesMedia['idHistorique'];?>">
                            <button type="submit" class="btn btn-success mt-3">Mettre à jour</button>
                            <a href="/on-en-parle/medias-presse/" class="btn btn-info">Retour sur la page de l'historique</a>
                            <button class="btn btn-danger" onclick="confirmationDelete()">Supprimer</button>
                        </div>
                          
                        </div>
                    
                </form>
            </div>        
        </div>
    </div>
    <script>
    let historique = <?php echo json_encode($historique); ?>;

    function limiteur(){
        maximum = 250;
        champ = document.formulaire.content;
        indic = document.formulaire.indicateur;

        if (champ.value.length > maximum)
        champ.value = champ.value.substring(0, maximum);
        else
        document.getElementById("caracteresRestantContent").innerHTML = maximum - champ.value.length+" caractères restant...";
    }
    function confirmationDelete(){
        var val = confirm("Vous êtes sûr de vouloir supprimer cet historique ?");
        if( val == true ) {
            window.location.href = "/administration/historique/ctrl/ctrl-delete.php?historique="+historique;
        } else {
            window.location.href = "/administration/historique/edition-medias.php?historique="+historique;  
        }
    }

    </script>
                <?php
            }//fin du if count
        }//fin du if pas un nombre
}//fin du if presence du get
include_once("../../commun/bas_de_page-admin.php");
?>