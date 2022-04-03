<?php
@session_start ();
include_once("./config.php");
include("./controles/fonctions/cleanUrl.php");
$titreDeLaPage = $GLOBALS['titreDePage'];
$descriptionPage = "Refaites vos jeux vous propose un service avec pleins de pièces détachées pour vos jeux de socièté. Donnez leur une seconde vie.";
include_once("./bdd/connexion-bdd.php");
// $cacheMenuLeft = true;
include_once("./commun/haut_de_page.php");
include_once("./commun/alertMessage.php");
?>

<section class="mt-4 text-center container">
    <div class="row mt-5">
        <div class="col-11 mx-auto">
            <div class="lead text-secondary">Un service pour compléter ses jeux de société, en acheter d'occasion et donner ceux dont on ne veut plus (même incomplets).</div>
        </div>
    </div>
</section>
<div class="container-fluid mt-5">
    <div class="col-12 col-lg-10 mx-auto p-0 d-flex justify-content-around flex-wrap">
        <a href="/catalogue-pieces-detachees/" class="m-2 text-decoration-none">
            <div class="jumbotron jb1-accueil m-0 text-center d-flex flex-wrap justify-content-center align-items-center py-5 px-0">
                <span class="h3 text-white col-12 pt-1">Pièces détachées</span>
                <span class="h5 text-white col-12">Envoi 🇫🇷 & 🇧🇪 </span>
                <span class="col-12 text-white text-center small">Voir le catalogue</span>
            </div>
        </a>
        <a href="/catalogue-jeux-occasion/" class="m-2 text-decoration-none">
            <div class="jumbotron jb2-accueil m-0 text-center d-flex flex-wrap justify-content-center align-items-center py-5 px-0">
                <span class="h3 text-white col-12 pt-1">Jeux d'occasion</span>
                <span class="h5 text-white col-12">(Retrait sur Caen uniquement)</span>
                <span class="col-12 text-white text-center small">Voir le catalogue</span>
            </div>
        </a>
        <a href="/don-de-jeux/partenaires/france/" class="m-2 text-decoration-none">
            <div class="jumbotron jb3-accueil m-0 text-center d-flex flex-wrap justify-content-center align-items-center py-5 px-0">
                <span class="h3 text-white col-12 pt-1">Donner ses jeux</span>
                <span class="h5 text-white col-12">🇫🇷 & 🇧🇪 </span>
                <span class="col-12 text-white text-center small">Voir les cartes</span>
            </div>
        </a>
    </div>

    <div class="col-12 text-center mt-5 h4 px-0">
        <a class="text-decoration-none text-info small" href="/carte-des-partenaires/france/">
            <i class="fas fa-long-arrow-alt-down"></i> Les partenaires du réemploi du jouet. <i class="fas fa-long-arrow-alt-down"></i>
            <br/>
            <img class="col-5 col-md-3 col-lg-2 position-relative" src="/images/design/carteFranceAcceuilCarree.png">
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