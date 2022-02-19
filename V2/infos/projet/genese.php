<?php
@session_start ();
include_once("../../config.php");
$titreDeLaPage = "La projet: la génèse | ".$GLOBALS['titreDePage'];
$descriptionPage = "Génèse du projet, ou comment à partir d'un service existant j'ai eu l'idée d'en créer un autre tout aussi utile !";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
?>
<div class="container-fluid mt-5">
    <h1 class="col-12 text-center mt-5">Genèse</h1>
    <div class="row py-2">
        <div class="col-lg-7 col-md-9 col-11 mx-auto py-3">
                <p class="py-2">
                A partir de 2015, je deviens bénévole à la Coop 5 pour 100 à Caen. Cette coopérative propose différentes activités dont une ressourcerie. En tant que coopérateur, je m'occupe notamment du rayon jeux de société. Je fais alors le constat qu’une partie importante des jeux récupérés sont incomplets. Au départ, certains étaient mis en vente avec l'étiquette "incomplet mais jouable" : ils ne se vendaient pas. Faute de temps, de compétences et de capacité de stockage, les jeux incomplets étaient depuis jetés en déchetterie.
                </p>
                <p class="py-2">
                En parallèle, je crée des jeux, dont certains se voient édités. Je découvre un monde du jeu qui sort près de 800 nouveautés par an, la grande majorité ayant une espérance de vie de quelques mois.
                </p>
                <p class="py-2">
                Sensibilisé aux questions de réduction des déchets et de sobriété, j'imagine alors un service de vente de pièces d'occasion pour permettre aux gens de compléter leurs jeux et ainsi prolonger leur durée de vie.
                </p>
                <p class="py-2"> 
                Lors du premier confinement de mars 2020, je décide de concrétiser ce projet ! Un partenariat est mis en place avec la Coop 5 pour 100. Les 6 mois qui suivent permettent la constitution d'un stock de jeux incomplets. C'est aussi le temps nécessaire pour réfléchir à l'organisation du service, créer l'auto-entreprise…
                </p>
                <p class="py-2">
                Le service est lancé début novembre 2020.
                </p>
                <div class="col-12 py-2 text-center text-md-right">
                    <figure class="figure col-5 col-sm-3">
                        <img src="/images/photos/antoine_gallee-min.JPG" class="figure-img img-fluid rounded" alt="photo d'Antoine Gallée">
                        <figcaption class="figure-caption text-center">Antoine Gallée.</figcaption>
                    </figure>
                </div>
        </div>
    </div>
</div>
<?php
include_once("../../commun/bas_de_page.php");
?>
