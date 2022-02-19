<?php
@session_start ();
include_once("../../config.php");
$titreDeLaPage = "Le projet: l'avenir | ".$GLOBALS['titreDePage'];
$descriptionPage = "Un petit peu de projection sur le développement de ce service de vente de pièces détachées pour vos jeux de socièté !";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
?>
<div class="container-fluid mt-5">
    <h1 class="col-12 text-center mt-5">Quel avenir pour cette activité ?</h1>
    <div class="row py-2">
        <div class="col-lg-7 col-md-9 col-11 mx-auto">
                <p class="py-2">
                Depuis son lancement, le service est chaleureusement accueilli. Le besoin est réel et les commandes sont nombreuses.
                </p>
                <p class="py-2">
                Le premier objectif de ce service est la réduction des déchets. Le maintien de prix accessibles reste une priorité  afin d’encourager les gens  à compléter leurs jeux au lieu de les jeter.
                </p>
                <p class="py-2">
                Éducateur spécialisé de métier, je souhaite également que le projet ait une dimension sociale. L'idée est d'impliquer dans ce projet des personnes de tous horizons.
                </p>
                <p class="py-2"> 
                Le projet n'est pas seulement de créer une plateforme de vente en ligne. Il s’agit d’inscrire le projet dans la dynamique de l'économie sociale et solidaire en favorisant le développement de services locaux, en lien avec les acteurs de leur territoire.
                </p>
                <p class="py-2">
                Le service est encore très jeune. De nombreuses réflexions restent à mener.
                </p>
                <p class="py-2">
                J' invite ceux qui le souhaitent à prendre part à ce projet.
                </p>
                <p class="py-2">
                Vous souhaitez partager votre réflexion ?<br/>
                Vous souhaitez développer ce service dans votre région ?<br />
                Vous souhaitez devenir partenaire du service ?
                </p>
                <p class="py-2">
                Vous pouvez m' écrire via le <a href="/contact/" class="text-info">formulaire de contact</a> disponible sur le site.
                </p>
                <p class="py-2 text-right">
                Longue vie à ce projet et longue vie aux vieux jeux de société !
                </p>
        </div>
    </div>
</div>
<?php
include_once("../../commun/bas_de_page.php");
?>
