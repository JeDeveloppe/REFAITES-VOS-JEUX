<?php
@session_start ();
include_once("./config.php");
include("./controles/fonctions/cleanUrl.php");
$titreDeLaPage = $GLOBALS['titreDePage'];
$descriptionPage = "Vous avez un jeu de société incomplet ? Refaites vos jeux vous propose un service pour donner une seconde vie à votre jeu, nous avons plein de pièces détachées en stock.";
include_once("./bdd/connexion-bdd.php");
// $cacheMenuLeft = true;
include_once("./commun/haut_de_page.php");
include_once("./commun/alertMessage.php");
?>

<section class="mt-5 text-center container">
    <div class="row mt-5">
        <div class="col-11 mx-auto mt-4">
            <div class="lead text-muted">Un service pour compléter ses jeux de socièté, en acheter d'occasion et donner ceux dont on ne veut plus (même incomplets).
            </div>
        </div>
    </div>
</section>
<div class="container-fluid mt-5">
    <div class="col-12 col-lg-10 mx-auto p-0 d-flex justify-content-around flex-wrap">
        <a href="/catalogue-pieces-detachees/" class="m-2 text-decoration-none">
            <div class="jumbotron jb1-accueil m-0 text-center d-flex flex-wrap justify-content-center align-items-center">
                <span class="h3 text-white col-12 p-0">Pièces détachées</span>
                <span class="h4 text-white">Envoi &#127467;&#127479; & &#127463;&#127466;</span>
            </div>
        </a>
        <a href="/catalogue-jeux-occasion/" class="m-2 text-decoration-none">
            <div class="jumbotron jb2-accueil m-0 text-center d-flex flex-wrap justify-content-center align-items-center">
                <span class="h3 text-white col-12 p-0">Jeux d'occasion</span>
                <span class="h6 text-white">(Retrait sur Caen uniquement)</span>
            </div>
        </a>
        <a href="/don-de-jeux/partenaires/france/" class="m-2 text-decoration-none">
            <div class="jumbotron jb3-accueil m-0 text-center d-flex flex-wrap justify-content-center align-items-center">
                <span class="h3 text-white col-12 p-0">Donner ses jeux</span>
                <span class="h4 text-white">&#127467;&#127479; & &#127463;&#127466;</span>
            </div>
        </a>
    </div>
    <div class="col-12 text-center mt-5 h4">
        <a class="text-decoration-none text-info" href="/carte-des-partenaires/france/">
            <i class="fas fa-long-arrow-alt-down"></i> Carte des partenaires du réemploi du jouet. <i class="fas fa-long-arrow-alt-down"></i>
            <br/>
            <img class="col-5 col-md-3 col-lg-2 position-relative" src="/images/design/franceAccueil.png">
            <?php
                $sqlPartenaires = $bdd->prepare("SELECT * FROM partenaires WHERE pays = ? AND isActif = 1");
                $sqlPartenaires->execute(array("FR"));
                $nbrPartenaires = $sqlPartenaires->rowCount();
            ?>
            <span class="nbre-partenaire-sur-carte-accueil" data-html="true" data-toggle="tooltip" data-placement="top" title="Nombre de partenaires"><?php echo $nbrPartenaires-1; ?></span>
        </a>
    </div>
    <?php require_once($_SERVER['DOCUMENT_ROOT'].'/on-en-parle/compteurs.php'); ?>
</div>


<?php
include_once("./commun/bas_de_page.php");
?>