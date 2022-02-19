<?php
@session_start ();
include('../config.php');
include('../bdd/connexion-bdd.php');
include('../bdd/table_config.php');

$sqlToutDesPartenaire = $bdd -> query("SELECT * FROM partenaires ORDER BY departement, ville DESC");
$donneesPartenaire = $sqlToutDesPartenaire->fetch();
$count = $sqlToutDesPartenaire-> rowCount();


    $titreDeLaPage = "Nos partenaires | ".$GLOBALS['titreDePage'];
    $descriptionPage = "Cette page répertorie tous les partenaires du service. Il s’agit de personnes, d’initiatives ou d’entreprises qui s’inscrivent dans la même démarche autour du jeu, du développement durable, du réemploi et de la réduction des déchets.";
    include_once("../commun/haut_de_page.php");
    include_once("../commun/alertMessage.php");
    ?>
    <div class="container">
        <h1 class="col-12 text-center mt-4">Nos partenaires</h1>
        <!-- PRESENTATION -->
        <div class="row my-4">
            <div class="col-11 col-md-9 col-lg-8 lead text-muted mx-auto">
                Cette page répertorie tous les partenaires du service. Il s’agit de personnes, d’initiatives ou d’entreprises qui s’inscrivent dans la même démarche autour du jeu, du développement durable, du réemploi et de la réduction des déchets.
            </div>
        </div>
        <!-- BLOC DES PARTENAIRES  -->
        <div class="row">
      
                <?php
                while($donneesPartenaire){
                    if($donneesPartenaire['ville'] == "?"){
                        $affichageVillePartenaire = "";
                    }else{
                        $affichageVillePartenaire = $donneesPartenaire['ville'].' - ';
                    }
                    if($donneesPartenaire['departement'] == "?"){
                        $affichageDepartementPartenaire = "";
                    }else{
                        $affichageDepartementPartenaire = $donneesPartenaire['departement'];
                    }
                    echo '
                        <div class="card mt-3 p-0 col-11 col-sm-10 col-md-9 col-lg-7 mx-auto text-dark" id="'.$donneesPartenaire['idPartenaire'].'">
                            <div class="card-header bg-secondary h4 text-white d-flex">
                            <div class="col">'.$donneesPartenaire['nom'];
                            //BOUTON EDITION
                            if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                                echo '<a href="/admin/partenaires/'.$donneesPartenaire['idPartenaire'].'/edition/" class="btn btn-warning border-primary ml-2 p-1">Editer</a>';
                            }
                                echo '</div>
                            <div class="col text-right">'.$affichageVillePartenaire.$affichageDepartementPartenaire.'</div>
                            </div>
                                <div class="card-body d-flex flex-column flex-md-row flex-wrap p-1">
                                    <!-- image de la boite -->
                                    <div class="col-md-5 text-center mt-1">
                                        <div class="divImgCatalogue p-0">
                                            <img src="data:image/jpeg;base64,'.$donneesPartenaire['image'].'"/>
                                        </div>
                                    </div>
                                    <div class="col-11 col-md-7 align-self-center">
                                        <div class="col">
                                        '.$donneesPartenaire['description'].'
                                        </div>
                                        <div class="col text-center mt-2"><a href="'.$donneesPartenaire['url'].'" class="cursor-alias text-info" target="_blank">'.$donneesPartenaire['site'].'</a></div>
                                    </div>
                                </div>
                        </div>';
                $donneesPartenaire = $sqlToutDesPartenaire->fetch();
                }
                ?>

        </div>       

    </div>
<?php
include_once("../commun/bas_de_page.php");
?>