<?php
@session_start ();
include_once("./config.php");
include("./controles/fonctions/cleanUrl.php");
$titreDeLaPage = $GLOBALS['titreDePage'];
$descriptionPage = "Vous avez un jeu de société incomplet ? Refaites vos jeux vous propose un service pour donner une seconde vie à votre jeu.";
include_once("./bdd/connexion-bdd.php");
include_once("./commun/haut_de_page.php");
include_once("./commun/alertMessage.php");

$sqlDerniersJeux = $bdd-> query("SELECT * FROM catalogue WHERE actif = 1 ORDER BY idCatalogue DESC LIMIT 4");
$donneesJeux = $sqlDerniersJeux-> fetch();
//NOMBRE DE JEUX SAUVER
$sqlSauver = $bdd -> query('SELECT * FROM documents_lignes WHERE idDocument IN (SELECT idDocument FROM documents WHERE numero_facture != "")');
$nbrJeuxSauver = $sqlSauver->rowCount();
//NOMBRE DE JEUX TOTAL POUR PIECES
$sqlCatalogue = $bdd -> query("SELECT * FROM catalogue WHERE actif = 1 AND accessoire_idCategorie = 0");
$nbrJeuxTotalEnLigne = $sqlCatalogue->rowCount();
?>

<section class="pt-5 text-center container">
    <div class="row">
        <div class="col-11 col-md-9 col-lg-8 mx-auto">
            <h1 class="fw-light">Bienvenue à tous !</h1>
            <div class="lead text-muted">Vous avez un jeu de société incomplet ?<br/>
                Refaites vos jeux vous propose un service de vente de pièces détachées d'occasion.<br/>
                Le catalogue référence tous les jeux incomplets en stock.<br/>
                <p>En espérant que vous trouviez la pièce qu'il vous manque !</p>
                <p>Les arrivages sont régulièrement publiés sur la page <a class="cursor-alias" href="https://www.facebook.com/refaitesvosjeux" target="_blank">Facebook @refaitesvosjeux.</a></p>
                <p><a class="cursor-alias" href="/don-de-jeux/">Si vous souhaitez faire un don de jeu.</a></p>
            </div>
            <p class="mt-3">
                <a href="/catalogue/" class="btn btn-info bg-refaites my-2">Aller au catalogue</a>
            </p>
        </div>
    </div>
</section>
<?php
$sqlCatalogue = $bdd -> query("SELECT * FROM catalogue WHERE actif = 1 AND accessoire_idCategorie = 0 ORDER BY RAND () LIMIT 4");
$donneesJeux = $sqlCatalogue->fetch();

?>
<div class="container-fluid mt-2">
    <div class="row">
      <div class="col-12 h3 fw-light text-center mb-3">Aperçu du catalogue</div>
    </div>
    <div class="row px-xl-9 d-flex flex-wrap justify-content-start">
    <?php
                $ordre = 1;
                while($donneesJeux){
                    //pour animation
                    switch ($ordre) {
                        case 1:
                            $animation = "fadeInLeft";
                            break;
                        case 2:
                            $animation = "fadeInDown";
                            break;
                        case 3:
                            $animation = "fadeInDown";
                            break;
                        case 4:
                            $animation = "fadeInRight";
                            break;
                    }
                    $urlEditeurCatalogue = clean_url($donneesJeux['editeur']);
                    $urlJeuCatalogue = clean_url($donneesJeux['nom']);

                    //on cherche l'image du jeu
                    $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$donneesJeux['idCatalogue']);
                    $donneesImage = $sqlImage->fetch();
                    ?>

                    <div class="col-8 col-sm-6 col-md-4 col-lg-3 mx-auto p-2" id="<?php echo $donneesJeux['idCatalogue']; ?>">
                           
                            <div class="col-12 p-2 bg-white border shadow"> 
                                <a class="text-decoration-none" href="/jeu/<?php echo $urlEditeurCatalogue;?>/<?php echo $donneesJeux['idCatalogue'];?>/<?php echo $urlJeuCatalogue;?>/">
                                    <div class="col-12 p-0">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="divImgCatalogue">
                                                    <?php echo '<img src="data:image/jpeg;base64,'.$donneesImage['image'].'" alt="Boite du jeu '.$donneesJeux['nom'].' par '.$donneesJeux['editeur'].'" />'; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2 p-2">
                                            <?php 
                                                if(strlen($donneesJeux['nom']) > 17){
                                                    echo '<div class="col-12 text-center" data-html="true" data-toggle="tooltip" data-placement="top" title="'.$donneesJeux['nom'].'">'.substr(nl2br($donneesJeux['nom']),0,17).'...</div>';
                                                }else{
                                                    echo '<div class="col-12 text-center">'.$donneesJeux['nom'].'</div>';
                                                }  
                                            ?>                                    
                                            <div class="col-12 mt-2 text-center"><?php echo $donneesJeux['editeur'];?></div>
                                            <div class="col-12 text-center"><?php echo $donneesJeux['annee']; ?></div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                    </div>
                <?php
                $donneesJeux = $sqlCatalogue->fetch();
                $ordre++;
                }
                ?>
       

    </div>
    <div class="row mt-4">
        <div class="col-12 d-md-flex text-center">
            <div class="col-12 col-md-6 p-0">
                <div class="col h3">Nombre de jeux en ligne :</div>
                <div id="odometerJeuxEnLigne" class="odometer col h3"></div>
            </div>
            <div class="col-12 col-md-6 p-0">
                <div class="col h3">Nombre de jeux complétés :</div>
                <div id="odometer" class="odometer col h3"></div>
            </div>
        </div>
    </div>
</div>
<script>
/*
 * ODOMETRE
 */
let jeuxEnLigne = <?php echo json_decode($nbrJeuxTotalEnLigne); ?>;
let jeuxSauverAvantSite = 120;
let jeuxSauverParLeSite = <?php echo json_encode($nbrJeuxSauver); ?>;
let totalJeuxSauver = Number(jeuxSauverAvantSite) + Number(jeuxSauverParLeSite);

if(jeuxEnLigne < 10){
    odometerJeuxEnLigne.innerHTML = 4;
}else if(jeuxEnLigne > 9 && jeuxEnLigne < 100){
    odometerJeuxEnLigne.innerHTML = 31;
}else if(jeuxEnLigne > 99 && jeuxEnLigne < 1000){
    odometerJeuxEnLigne.innerHTML = 300;
}else if(jeuxEnLigne > 999 && jeuxEnLigne < 10000){
    odometerJeuxEnLigne.innerHTML = 1983;
}else if(jeuxEnLigne > 9999 && jeuxEnLigne < 100000){
    odometerJeuxEnLigne.innerHTML = 22220;
}

if(totalJeuxSauver < 10){
    odometer.innerHTML = 4;
}else if(totalJeuxSauver > 9 && totalJeuxSauver < 100){
    odometer.innerHTML = 31;
}else if(totalJeuxSauver > 99 && totalJeuxSauver < 1000){
    odometer.innerHTML = 711;
}else if(totalJeuxSauver > 999 && totalJeuxSauver < 10000){
    odometer.innerHTML = 1983;
}else if(totalJeuxSauver > 9999 && totalJeuxSauver < 100000){
    odometer.innerHTML = 22220;
}
setTimeout(function(){
    odometerJeuxEnLigne.innerHTML = jeuxEnLigne;
    odometer.innerHTML = totalJeuxSauver;
    odometerJeuxCompletEnLigne.innerHTML = jeuxCompletEnLigne;
}, 2500);
</script>
<script src="/js/<?php echo $GLOBAL['versionJS'];?>/odometre.js"></script>

<?php
include_once("./commun/bas_de_page.php");
?>