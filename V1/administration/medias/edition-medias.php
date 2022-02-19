<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');

if(!isset($_GET['media'])){
    $_SESSION['alertMessage'] = "Pas de média sélectionné !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit(); 
}else{
    require('../../controles/fonctions/validation_donnees.php');
    
    $media = valid_donnees($_GET['media']);

        if(empty($_GET['media']) || !preg_match('#^[0-9]{1,25}$#', $media)){
            $_SESSION['alertMessage'] = "Ce n'est pas un nombre...";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP-REFERER']);
            exit();            
        }else{
            $sqlToutDuMedia = $bdd -> prepare("SELECT * FROM medias WHERE idmedia = :ligne") ;
            $sqlToutDuMedia->execute(array('ligne' => $media)) ;
            $donneesMedia = $sqlToutDuMedia->fetch();
            $count = $sqlToutDuMedia-> rowCount();

            if($count< 1){
                $_SESSION['alertMessage'] = "Média inconnu !";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: /accueil/");
                exit(); 
            }else{
                  
                $titreDeLaPage = "[Édition d'un média] | ".$GLOBALS['titreDePage'];
                $descriptionPage = "";
                include_once("../../commun/haut_de_page.php");
                include_once("../../commun/alertMessage.php");
                ?>

    <div class="container-fluid d-flex flex-column p-0">
        <h1 class="col text-center mt-5">Édition d'un média</h1>
        <!-- BLOC  -->
        <div class="col-12 mt-4">
            <div class="row">
                <!-- formulaire de modification -->
                <form method="post" class="col-12 d-flex" action="/administration/medias/ctrl/ctrl-edition.php" name="formulaire" enctype="multipart/form-data">
                    <div class="card mt-3 p-0 col-md-10 mx-auto text-dark">
                        <div class="card-header bg-secondary h4 text-white">
                            Titre: <input type="text" name="nom" placeholder=" Titre du média / de la page... Max 80 caractères" value="<?php echo $donneesMedia['titre']; ?>" maxlenght="80" required><br/>
                        </div>
                            <div class="card-body p-0 pb-2">
                                <div class="col-6 mt-3 mx-auto">
                                    <div class="col text-center p-0">
                                        <div class="divImgPresentation mt-3">
                                            <img class="img-thumbnail border" src="data:image/jpeg;base64,<?php echo $donneesMedia['image']; ?>"/>
                                        </div>
                                        <div class="form-group bg-secondary p-0 mt-4">
                                            <label class="col text-center text-white">Changer l'image':</label>
                                            <input class="col text-center bg-light p-3" type="file" name="photo" id="fileSelect" required>
                                        </div>
                                        <div class="col pl-4 text-danger text-center">
                                            <p>Format d'image accepté: .jpg, .jpeg, .gif, .png</p>
                                            <p>Taille maximum <?php echo $donneesConfig[2]['valeur'];?>MB et dimension minimum <?php echo $donneesConfig[3]['valeur'];?> x <?php echo $donneesConfig[4]['valeur'];?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col text-center">Description:</label>
                                        <textarea class="form-control" rows="7" name="content" id="content" onKeyUp="limiteur();" placeholder="Un texte de description..." minlength="15" maxlenght="300" required><?php echo $donneesMedia['content']; ?></textarea>
                                        <small class="form-text text-danger text-center">Entre 15 et 250 caractères.</small>
                                        <div class="small text-center" id="caracteresRestantContent">250 caractères restant...</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col text-center">Lien de la page:</label>
                                        <input type="url" class="form-control" name="url" placeholder="Copier / coller url de la page en entier" value="<?php echo $donneesMedia['lien']; ?>" required>
                                        <small class="form-text text-danger text-center">Mettre url en entier...</small>
                                    </div>
                                    <div class="form-group col">
                                        <label class="col text-center">Date de publication:</label>
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
                                    <div class="col text-center">
                                        <input type="hidden" name="idDuMedia" value="<?php echo $donneesMedia['idMedia'];?>">
                                        <button type="submit" class="btn btn-success mt-3">Mettre à jour</button>
                                        <a href="/on-en-parle/medias/#<?php echo $donneesMedia['idMedia'];?>" class="btn btn-info">Retour sur la page des medias</a>
                                        <button class="btn btn-danger" onclick="confirmationDelete()">Supprimer</button>
                                    </div>
                                </div>
                        </div>
                    </div>
                </form>
            </div>        
        </div>
    </div>
    <script>
    let media = <?php echo json_encode($media); ?>;

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
        var val = confirm("Vous êtes sûr de vouloir supprimer ce média ?");
        if( val == true ) {
            window.location.href = "/administration/medias/ctrl/ctrl-delete.php?media="+media;
        } else {
            window.location.href = "/administration/medias/edition-medias.php?media="+media;  
        }
    }

    </script>
                <?php
            }//fin du if count
        }//fin du if pas un nombre
}//fin du if presence du get
include_once("../../commun/bas_de_page-admin.php");
?>