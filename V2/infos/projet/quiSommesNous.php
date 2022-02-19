<?php
@session_start ();
include_once("../../config.php");
$titreDeLaPage = "La projet: qui sommes-nous ? | ".$GLOBALS['titreDePage'];
$descriptionPage = "Génèse du projet, ou comment à partir d'un service existant j'ai eu l'idée d'en créer un autre tout aussi utile !";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
?>
<div class="container-fluid mt-5">
    <h1 class="col-12 text-center mt-5">Qui sommes- nous ?</h1>
    <div class="row py-2">
        <div class="col-lg-7 col-md-9 col-11 mx-auto py-3">
            <p>Refaites vos jeux est un acteur du réemploi du jouet.</p>
            <p  class="h4 text-info my-4">A l’origine une idée simple : prolonger la vie des jeux de société.</p>
            <p class="text-justify">L’idée est née de l’expérience d’un bénévole dans une ressourcerie caennaise (La Coop 5 pour 100). Faute de stockage, de temps et de connaissances, de nombreux jeux de société incomplets étaient jetés. Antoine Gallée, fondateur du service, a alors eu l’idée de collecter ces jeux et de créer un catalogue en ligne de vente des pièces détachées d’occasion. Cela allait permettre aux gens de compléter leurs jeux plutôt que de les jeter.</p>
            <p>En novembre 2020, le service était lancé.</p>

            <p class="h4 text-info my-4">Ce projet s’inscrit dans une démarche écologique de réduction des déchets et de sobriété.</p>
            <p>Le monde du jeu de société est en plein essor et nous nous en réjouissons. Comme n’importe quel secteur en croissance, des dérives sont possibles. 
            <p>Chaque année, près de 1000 nouveaux jeux de société sortent en magasin et près de 100 000 tonnes de jouets sont jetées.
            <p>Nous nous inscrivons dans la réflexion des acteurs du monde du jouet pour continuer à soutenir la créativité tout en veillant à inscrire les jeux dans la durée.</p>

            <p class="h4 text-info mt-4">Les activités principales du service sont :</p>
            <ul>
                <li>La collecte de jeux de société complets et incomplets.</li>
                <li>Le réemploi de ces jeux (vente de pièces détachées / recomposition de jeux complets).</li>
                <li>La promotion des acteurs du réemploi du jouet (en France et en Belgique).</li>
            </ul>

            <p  class="h4 text-info mt-4">Le service s’appuie aujourd’hui sur un collectif.</p> 

            <p>Une réflexion est en cours pour faire évoluer le statut du service et créer une structure collective (association, société coopérative).</p>
            <p>Basé sur Caen, le service a dès sa première année complété près de 1000 jeux !</p>
        </div>
    </div>
</div>
<?php
include_once("../../commun/bas_de_page.php");
?>
