<?php
@session_start ();
include_once("../../config.php");
$titreDeLaPage = "Comment ça marche: les tarifs | ".$GLOBALS['titreDePage'];
$descriptionPage = "Les tarifs du service: prix des différentes pièces, des envois par La Poste, par Colissimo ou Mondial Relay !";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
include_once("../../bdd/table_config.php");
?>
<div class="container-fluid">
    <h1 class="col-12 text-center mt-5">Les tarifs</h1>
    <div class="row py-4">
        <div class="col-11 mx-auto text-center">
            <p>Un FORFAIT DE BASE de <?php echo str_replace(".",",",$donneesConfig[5]['valeur']);?> € s'applique pour toute commande d'une ou plusieurs pièces de jeu.</p>
            <p>Le forfait reste à <?php echo str_replace(".",",",$donneesConfig[5]['valeur']);?> € si vous commandez des pièces de différents jeux.</p>
            <p>Les pièces sont ensuite vendues à l'unité.</p>

            <p class="h3">
            <i class="fas fa-lightbulb text-info"></i> Pensez à regrouper vos commandes!
            </p>

        </div>
    </div>
    <!-- TARIFS DES PIECES -->
    <div class="row py-2">
        <div class="col-12 text-center">
            <h2>Tarifs des pièces de jeux</h2>
        </div>
        <div class="col-11 mx-auto py-3">
            <form method="post" action="">
            <table class="table table-sm col-12 col-sm-10 mt-4 mx-auto">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th scope="col">Pièce</th>
                            <th scope="col">Prix Unitaire HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Grand plateau de jeu / support de jeu en bois</td>
                            <td class="text-center">
                                <?php echo str_replace(".",",",$donneesConfig[12]['valeur']);?> €
                            </td>
                        </tr>
                        <tr>
                            <td>Grand plateau de jeu / support de jeu en carton ou en plastique</td>
                            <td class="text-center">
                                <?php echo str_replace(".",",",$donneesConfig[13]['valeur']);?> €
                            </td>
                        </tr>
                        <tr>
                            <td>Petit plateau de jeu / support de jeu en bois</td>
                            <td class="text-center">
                                <?php echo str_replace(".",",",$donneesConfig[14]['valeur']);?> €
                            </td>
                        </tr>
                        <tr>
                            <td>Petit plateau de jeu / support de jeu en carton ou en plastique</td>
                            <td class="text-center">
                                <?php echo str_replace(".",",",$donneesConfig[15]['valeur']);?> €
                            </td>
                        </tr>
                        <tr>
                            <td>Petit élément du jeu (souvent en grande quantité)<br/>
                            <span class="small">Exemples: jeton de Mastermind, de Touché-coulé…</span>
                            </td>
                            <td class="text-center">
                                <?php echo str_replace(".",",",$donneesConfig[16]['valeur']);?> €
                            </td>
                        </tr>
                        <tr>
                            <td>Élément moyen du jeu<br/>
                            <span class="small"> Exemples: pion, dé, carte…</span></td>
                            <td class="text-center">
                                <?php echo str_replace(".",",",$donneesConfig[17]['valeur']);?> €
                            </td>
                        </tr>
                        <tr>
                            <td>Gros élément du jeu <br />
                            <span class="small">Exemples: sonnette, sablier, règle du jeu…</span></td>
                            <td class="text-center">
                                <?php echo str_replace(".",",",$donneesConfig[18]['valeur']);?> €
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="col-11 col-md-9 col-xl-6 mx-auto text-justify my-4 d-flex">
            <div class="col-1"><i class="fas fa-lightbulb text-info fa-2x"></i></div>
            <div class="col-10">En fonction du nombre et de la taille des pièces achetées, un prix forfaitaire avantageux pourra être proposé (exemples : lot de cartes ou de jetons).</div>
        </div>
    </div>
    <!-- TARIFS DES ENVOIS -->
    <div class="row py-2">
        <div class="col-12 text-center mt-5">
            <h2>Tarifs d'envoi / de retrait</h2>
        </div>
        <div class="col-11 mx-auto py-3">
            <form method="post" action="">
                <table class="table table-sm col-12 col-sm-10 mt-4 mx-auto">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th scope="col">Mode d'envoi</th>
                            <th scope="col">Prix Unitaire HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Retrait à la Coop 5 pour 100 à Caen</td>
                            <td class="text-center">Gratuit</td>
                        </tr>
                        <tr>
                            <td>Enveloppe simple</td>
                            <td class="text-center"><?php echo str_replace(".",",",$donneesConfig[20]['valeur']);?> €</td>
                        </tr>
                        <tr>
                            <td>Enveloppe à bulles</td>
                            <td class="text-center">A partir de <?php echo str_replace(".",",",$donneesConfig[21]['valeur']);?> €</td>
                        </tr>
                        <tr>
                            <td>Colissimo</td>
                            <td class="text-center">A partir de <?php echo str_replace(".",",",$donneesConfig[22]['valeur']);?> €</td>
                        </tr>
                        <tr>
                            <td>Mondial Relay</td>
                            <td class="text-center">A partir de <?php echo str_replace(".",",",$donneesConfig[23]['valeur']);?> €</td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="col-11 col-md-9 col-xl-6 mx-auto text-justify my-4 d-flex">
            <div class="col-1"><i class="fas fa-lightbulb text-info fa-2x"></i></div>
            <div class="col-10">Les frais de port sont calculés en fonction du poids, de la taille des pièces commandées et du mode de livraison (enveloppe simple, enveloppe à bulles, Colissimo, Mondial Relay).</div>
        </div>
    </div>
    <!-- PAIEMENTS -->
    <div class="row py-2">
        <div class="col-11 col-md-10 col-xl-7 mx-auto mt-5">
            <h2>Paiements</h2>
        </div>
        <div class="col-11 col-md-9 col-xl-6 mx-auto text-justify my-4">
            <div class="col-12">Les paiements sont effectués par carte bancaire via des pages sécurisées.</div> 
            <div class="col-12 text-center mt-3">
                <img src="/PAIEMENT/<?php echo $GLOBAL['servicePaiement']; ?>/logo-<?php echo $GLOBAL['servicePaiement'];?>.png" alt="Paiement avec <?php echo $GLOBAL['servicePaiement']; ?>"/>
            
            </div>
            <div class="col-12 mt-5">Après le paiement de la commande il vous est possible de demander une facture.</div>
        </div>
        </div>
    </div>
</div>
<?php
include_once("../../commun/bas_de_page.php");
?>
