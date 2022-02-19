<?php
@session_start ();
include_once("../../config.php");
$titreDeLaPage = "Comment ça marche: les commandes | ".$GLOBALS['titreDePage'];
$descriptionPage = "Explication pour vous permettre de passée une commande sur le service d'achat du site !";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
?>
<div class="container-fluid">
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
                    <li class="mt-3 mb-2">Étape 1 : faire l'inventaire précis des pièces qui vous manquent.</li>
                        <ol>- Qui est l'éditeur du jeu ? Exemples : Ravensburger, MB, Haba, Hasbro…</ol>
                        <ol>- Quelle est la date de l'édition ? Elle est souvent marquée sur la boîte, parfois sur la règle. Elle n'est pas toujours indiquée. Cette information est importante car certaines boîtes se ressemblent mais le matériel n'est pas le même.</ol>
                        <ol>- Quelles sont précisément les pièces qu'il vous manque ? Quantités, couleurs, formes, noms…</ol>
                    
                    <li class="mt-3 mb-2">Étape 2 : consulter le catalogue du site.</li>
                        <ol>Ce catalogue référence tous les jeux incomplets dont le service dispose. Il est régulièrement mis à jour. Il est donc inutile de faire des demandes pour des jeux qui ne sont pas sur le catalogue !
                            Vous avez trouvé la même boîte de jeu avec le même éditeur et la même date d'édition ? Vous pouvez passer à l'étape 3.
                            Vous n'avez pas trouvé votre bonheur ? Cela viendra peut-être plus tard ? Vous pouvez consulter les arrivages sur la page Facebook @refaitesvosjeux. Vous pouvez aussi me faire part de votre souhait en lançant une <i class="fas fa-wine-bottle text-success"></i> <a href="/bouteille-a-la-mer/" class="text-info"> bouteille à la mer</a> ! Je vous alerterai par email lorsque le jeu que vous cherchez sera mis en ligne.</ol>
                    
                    <li class="mt-3 mb-2">Étape 3 : faire une demande de pièces pour un jeu.</li>
                        <ol>Il vous suffit de cliquer sur le bouton "faire une demande de pièces". Vous pouvez alors préciser la ou les pièces qu'il vous manque. N'oubliez pas d'être précis ! Quantités, couleurs, formes…
                        Vous pouvez éventuellement joindre une photo.
                        Il vous suffit ensuite de cliquer sur le bouton "Ajouter à mes demandes". La demande est ajoutée à votre liste de demandes.
                        Vous pouvez renouveler l'opération pour plusieurs jeux.</ol>
                    
                    <li class="mt-3 mb-2">Étape 4 : valider votre demande de pièces.</li>
                        <ol>Il vous suffit de cliquer sur l'icône "mes demandes" en haut à droite, de remplir le formulaire puis de cliquer sur le bouton « demander un devis ».
                        Un mail de confirmation vous est envoyé.
                        Votre demande sera alors traitée dès que possible. Vous recevrez un mail vous indiquant les pièces disponibles ainsi que le devis global.</ol>

                    <li class="mt-3 mb-2">Étape 5 : accepter le devis émis par le service.</li>
                        <ol>Sur le mail comprenant votre devis se trouve un bouton « payer ma commande ».  En cliquant sur le lien vous serez automatiquement dirigés vers la page de paiement sécurisé.</ol>
                    
                    <li class="mt-3 mb-2">Étape 6 : paiement de la commande.</li>
                        <ol>Le paiement sécurisé se fait par carte bancaire sur le site de <a href="<?php echo $GLOBALS['urlService']; ?>" class="text-info" target="_blank"><?php echo $GLOBAL['servicePaiement']; ?></a>.</ol>
                    
                    <li class="mt-3 mb-2">Étape 7 : Envoi de votre commande.</li>
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
