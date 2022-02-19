<?php
@session_start ();
include_once("../../config.php");
$titreDeLaPage = "Comment ça marche: expédition et retrait | ".$GLOBALS['titreDePage'];
$descriptionPage = "Explication des expéditions et retrait des pièces commandées sur le site.";
include_once("../../bdd/connexion-bdd.php");
require("../../bdd/table_config.php");
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
?>
<div class="container-fluid mt-5">
    <h1 class="col-12 text-center mt-5">Expédition et retrait</h1>
    <div class="row py-2">
        <div class="col-11 col-md-8 mx-auto py-3">
            <p class="py-2 col-11 mx-auto text-center p-0">
            Le service propose plusieurs options pour récupérer votre commande :
                <ul>
                    <li class="mt-3 mb-2">Le retrait à la Coop 5 pour 100, 33 route de Trouville 14000 Caen : pas de frais de port.</li>
                        <ol>La commande est déposée à la Coop dans un délai de maximum 7 jours après validation du paiement.<br/>
                        Vous pouvez la retirer à la caisse du magasin en indiquant votre nom et prénom <ins class="text-danger">(prévoir une pièce d'identité)</ins>.<br/>
                        Le magasin est ouvert du mercredi au vendredi de 11h à 19h et le samedi de 11h à 18h.</ol>
                    <li class="mt-3 mb-2">L'envoi par voie postale : </li>
                        <ol>Les petites pièces sont envoyées sous enveloppe / enveloppe à bulles (pas de suivi d'envoi). A partir de <?php echo str_replace(".",",",$donneesConfig[20]['valeur']);?> €.</ol>
                        <ol class="mt-2">Les plateaux de jeux ainsi que les pièces dont l'épaisseur dépasse 3 cm sont envoyés par colis.</ol>
                            <ul class="mt-3 ml-5">Vous avez le choix entre deux options :
                                <li>Mondial Relay : colis à retirer dans le point relais de votre choix. A partir de <?php echo str_replace(".",",",$donneesConfig[23]['valeur']);?> €.</li>
                                <li>Colissimo : colis expédié directement chez vous. A partir de <?php echo str_replace(".",",",$donneesConfig[22]['valeur']);?> €.</li>
                            </ul>
                </ul>
            </p>

            <p class="py-2 col-11 mx-auto text-center text-danger">
            Le service peut faire des envois à l'étranger, le frais postaux sont alors majorés.
            </p>
        </div>
    </div>
</div>
<?php
include_once("../../commun/bas_de_page.php");
?>
