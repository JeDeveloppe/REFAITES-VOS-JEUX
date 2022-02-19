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
    <div class="col-12 text-center mb-4"><h1>Médias
    <?php
        if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
            echo '<a href="/admin/medias/new/" class="btn btn-info">+</a>';
        }
    ?>
    </h1>
    </div>
    <div class="timeline">
      <ul>
        <?php while($donneesTimeline){ ?>
            <li>
                <div class="row border border-primary p-0">
                    <time class="border border-primary" id="<?php echo $donneesTimeline['idMedia']; ?>"><?php echo date("d.m.Y",$donneesTimeline['date']);?></time>
                        <h2 class="col-12 mt-3"><?php echo $donneesTimeline['titre']; ?></h2>

                        <div class="col-12 text-center bg-success">
                            <div class="divImgCatalogue p-0">
                                <?php
                                    if(!empty($donneesTimeline['image'])){
                                        echo '<img src="data:image/jpeg;base64,'.$donneesTimeline['image'].'"/>';
                                    }else{
                                        echo '<img src="/images/design/media.default.png"';
                                    }
                                ?>
                            </div>
        
                                <a href="<?php echo $donneesTimeline['lien']; ?>" target="_blank" class="btn btn-primary mt-2">Voir l'article</a>
                            
                            <?php
                            if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                                echo '<a href="/admin/medias/'.$donneesTimeline['idMedia'].'/edition/" class="btn btn-warning border-primary ml-2 p-1">Editer</a>';
                            }
                            ?>
                        </div>
                </div>
            </li>
        <?php 
        $donneesTimeline = $sqlTimeline-> fetch();
        }
        ?>
      </ul>
    </div>


</div>
<script src="/js/<?php echo $GLOBAL['version'];?>/timeline.js"></script>
<?php
include_once("../commun/bas_de_page.php");
?>
