<?php
@session_start ();
include_once("../../config.php");
$titreDeLaPage = "Comment ça marche: les commandes | ".$GLOBALS['titreDePage'];
$descriptionPage = "Explication pour vous permettre de passée une commande sur le service d'achat du site !";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
?>
<div class="container-fluid mt-5">
    <h1 class="col-12 text-center mt-5">Passer une commande</h1>
    <div class="row py-2">
        <div class="col-11 col-lg-10 mx-auto py-3 px-0">
            <p class="py-2 col-11 mx-auto">
            Vous avez un ou plusieurs jeux incomplets ?<br />
            Pas de panique ! Le service a peut-être en stock la ou les pièces qu'il vous manque.<br/>
            Voici comment fonctionne le service :
            </p>
        </div>
        <div class="col-11 mx-auto py-3 mb-2">
                <ul class="mt-3">
                    <li class="mt-3 mb-2">Étape 1: remplir le formulaire de demande.</li>
                        <ol>Il est essentiel de bien préciser le nombre de pièces que vous souhaitez ainsi que les autres caractéristiques (noms, couleurs, formes).</ol>
                        <ol class="ml-5 text-info mb-2">Exemple : « Bonjour, je souhaiterais commander 3 jetons verts et 1 jeton rouge du jeu COLORINO ».</ol>
                        <ol>Il vous est possible de joindre 1 ou 2 photos pour expliciter votre demande.</ol>
                        <ol>Il ne vous reste plus qu’à cliquer sur le bouton « ajouter au panier ».</ol>

                    
                    <li class="mt-3 mb-2">Étape 2: validez votre panier.</li>
                        <ol>Il vous suffit de cliquer sur le symbole « panier » en haut à droite du site internet.<br/>
                            Après avoir renseigné le formulaire, vous n’avez plus qu’à cliquer sur le bouton « demander un devis ».<br/>
                            Un mail de confirmation vous est automatiquement envoyé (penser à vérifier vos spams).<br/>
                            Votre demande est traitée dès que possible.<br/>
                            Vous recevez alors un devis par mail vous indiquant les pièces disponibles ainsi que le montant de la commande.
                        </ol>
                    
                    <li class="mt-3 mb-2">Étape 3: paiement de la commande.</li>
                        <ol>Sur le devis se trouve un bouton « payer ma commande ». En cliquant sur le lien vous serez automatiquement dirigé vers la page de paiement sécurisé.</ol>
                        <ol>Le paiement sécurisé se fait par carte bancaire sur le site de <a href="<?php echo $GLOBALS['urlService']; ?>" class="text-info" target="_blank"><?php echo $GLOBAL['servicePaiement']; ?></a>.</ol>
                    
                    <li class="mt-3 mb-2">Étape 4: Envoi de votre commande.</li>
                        <ol>Dès validation du paiement, le service est tenu informé. Votre commande est alors préparée. En fonction de ce que vous avez choisi, elle est envoyée par voie postale ou déposée à la Coop 5 pour 100 à Caen.</ol>
                </ul>
                
        </div>
        <div class="col-12 mt-4 d-flex justify-content-around">
            <figure class="figure col-4 col-sm-3 col-md-2 col-lg-2">
                <img src="/images/photos/rangement1-min.JPG" class="figure-img img-fluid img-thumbnail border-primary" alt="photo rangement jeux">
                <figcaption class="figure-caption text-center">En pile...</figcaption>
            </figure>

            <figure class="figure col-4 col-sm-3 col-md-2 col-lg-2">
                <img src="/images/photos/rangement2-min.JPG" class="figure-img img-fluid img-thumbnail border-primary" alt="photo rangement jeux">
                <figcaption class="figure-caption text-center">En pile...</figcaption>
            </figure>

            <figure class="figure col-4 col-sm-3 col-md-2 col-lg-2">
                <img src="/images/photos/rangement3-min.JPG" class="figure-img img-fluid img-thumbnail border-primary" alt="photo rangement jeux">
                <figcaption class="figure-caption text-center">En pile...</figcaption>
            </figure>
        </div>
    </div>
</div>
<?php
include_once("../../commun/bas_de_page.php");
?>
