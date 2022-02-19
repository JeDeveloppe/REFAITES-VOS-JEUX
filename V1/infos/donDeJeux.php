<?php
@session_start ();
include_once("../config.php");
$titreDeLaPage = "Dons de jeux | ".$GLOBALS['titreDePage'];
$descriptionPage = "Vous pouvez faire des dons de jeux, complet ou incomplet, le service saura leur donner une seconde vie !";
include_once("../bdd/connexion-bdd.php");
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");

$sqlDons = $bdd->query("SELECT * FROM dons ORDER BY nom");
$donneesDons = $sqlDons->fetch();
?>
<div class="container-fluid">
    <h1 class="col-12 text-center mt-4">Don de jeux</h1>
    <!-- <div class="row mt-5">
        <div class="col-12 text-center ">
            <ul class="nav nav-pills d-inline-block">
                <li class="nav-item bg-vos jumbotron p-1 d-inline float-left">
                    <a class="nav-link float-left" href="/don-de-jeux/">Comment ?</a>
                </li>
                <li class="nav-item bg-vos jumbotron p-1 d-inline float-left ml-2">
                    <a class="nav-link" href="/don-de-jeux/les-demandes/">Le service recherche...</a>
                </li>
            </ul>
        </div>
    </div> -->
    <div class="row py-2">
        <div class="col-11 col-lg-8 mx-auto mt-3">
            Vous souhaitez donner des jeux au service ?
            <ul class="mt-3">
                <li class="pt-3">Le service récupère les jeux complets et incomplets ainsi que les pièces détachées (pions, dés, sabliers…).
                    <ol><span class="text-danger">Nous ne récupérons pas les puzzles et les jouets.</span></ol>
                </li>
                <li class="pt-3">Si vous êtes sur Caen, vous pouvez les déposer à la Coop 5 pour 100 – 33 route de Trouville – Caen.</li>
                <li class="pt-3">Si vous êtes d’une autre région vous pouvez les envoyer par voie postale ou colissimo à l’adresse suivante :
                    <ol>Antoine Gallée – 24 rue froide – 14980 ROTS.</ol>
                    <ol><span class="text-danger">Le service ne peut pas prendre en charge les frais de port.</span></ol>
                </li>
                <li class="pt-3">Vous pouvez aussi les expédier via Mondial Relay:
                    <ol> -> point relais CORA CAEN à Rots.</ol>
                    <ol> Email: contact@refaitesvosjeux.fr</ol>
                </li>
            </ul>
            <p class="mt-2">
            Si vous habitez près de Rots vous pouvez aussi déposer vos jeux à mon domicile (24 rue froide).<br/>
            En mon absence il est possible de les déposer devant la porte, elle est abritée.<br/>
            Nous vous remercions par avance pour vos dons ! Nous nous ferons un plaisir de les ajouter aux catalogues !
            </p>

            <p class="mt-2">
            Le service espère voir se développer des activités similaires dans d’autres régions afin que vous puissiez déposer vos jeux incomplets près de chez vous !
            </p>

            <p class="mt-2">
            Pour toutes questions merci d'utiliser le <a href="/contact/" class="text-info">formulaire en ligne</a>. 
            </p>

            <div class="col-12 mt-5 text-center">
                <figure class="figure col-11 col-sm-8 col-md-6">
                    <img src="/images/photos/donOuverture.JPG" class="figure-img img-fluid rounded" alt="photo exemple de dons">
                    <figcaption class="figure-caption text-center">Exemple de dons.</figcaption>
                </figure>
            </div>
    </div>
</div>
<?php
include_once("../commun/bas_de_page.php");
?>
