<?php
@session_start ();
include_once("../config.php");
$titreDeLaPage = "On en parle dans les médias | ".$GLOBALS['titreDePage'];
$descriptionPage = "Retrouvez toutes les interviews d'Antoine qui parle du service de Refaites vos jeux.";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
include_once("../bdd/connexion-bdd.php");

$sqlTimeline = $bdd-> prepare("SELECT * FROM medias WHERE actif = ? ORDER BY date DESC");
$sqlTimeline-> execute(array(1));
$donneesTimeline = $sqlTimeline-> fetch();
?>
<div class="container-fluid mt-5">
    <div class="col-12 text-center mb-4">
        <h1>Médias
            <?php
                if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                    echo '<a href="/admin/medias/new/" class="btn btn-info">+</a>';
                }
            ?>
        </h1>
    </div>
    <div class="row">
        <div class="col-12 d-flex flex-wrap justify-content-around">
            <?php
                while($donneesTimeline){
                ?>
                <div class="card p-0 col-10 col-sm-9 col-md-4 col-lg-3 m-2">
                    <div class="card-header bg-refaites p-0">
                        <span class="h5 border border-primary bg-vos p-1 ml-1 my-2" id="<?php echo $donneesTimeline['idMedia']; ?>"><?php echo date("d.m.Y",$donneesTimeline['date']);?></span>
                        <div class="h4 col text-center ml-1 my-2"><?php echo $donneesTimeline['titre']; ?></div>
                    </div>
                    <div class="card-body">
                        <div class="col-12 text-center">
                            <div class="divImgCatalogue p-0">
                                <a href="<?php echo $donneesTimeline['lien']; ?>" target="_blank">
                                    <?php
                                        if(!empty($donneesTimeline['image'])){
                                            echo '<img src="data:image/jpeg;base64,'.$donneesTimeline['image'].'" alt="'.$donneesTimeline['lien'].'" data-html="true" data-toggle="tooltip" data-placement="right" title="Voir l\'article"/>';
                                        }else{
                                            echo '<img src="/images/design/media.default.png" alt="'.$donneesTimeline['lien'].'" data-html="true" data-toggle="tooltip" data-placement="right" title="Voir l\'article"/>';
                                        }
                                    ?>
                                </a>
                            </div>
                            <?php
                            if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                                echo '<a href="/admin/medias/'.$donneesTimeline['idMedia'].'/edition/" class="btn btn-warning border-primary my-2 p-1">Editer</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $donneesTimeline = $sqlTimeline-> fetch();
                }
            ?>
        </div>
    </div>
</div>
<?php
include_once("../commun/bas_de_page.php");
?>
