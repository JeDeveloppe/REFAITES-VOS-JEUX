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
<div class="container-fluid mt-5">
    <h1 class="col-12 text-center my-5">Les tarifs</h1>
    <div class="row py-4">
        <div class="col-11 mx-auto text-center">
            <p class="text-justify my-4 col-11 col-md-8 mx-auto">Lors de la première commande, une adhésion client de <?php echo str_replace(".",",",$donneesConfig[28]['valeur']);?> euros est demandée. Elle représente un soutien au projet et valorise le temps passé par les membres du service.
               Cette adhésion est valable 1 an de date à date. Pour toute nouvelle commande passée durant l’année, seuls le tarif des jeux, des pièces détachées et des éventuels frais de port seront facturés.
            </p>
        </div>
    </div>
    <!-- TARIFS DES JEUX D'OCCASION -->
    <div class="row my-5">
        <h2 class="col-12 text-center">Tarifs des jeux</h2>

        <div class="col-11 col-md-9 col-xl-6 mx-auto text-justify my-4">
        Le tarif des jeux complets correspond au maximum à 50% du prix du jeu neuf.<br/>
        Ce tarif est minoré si l’état de la boîte ou du matériel est moyen ou si la règle du jeu est une photocopie.<br/>
        Ce tarif est majoré si le jeu est comme neuf.
        </div>
    </div>
    <!-- TARIFS DES PIECES -->
    <div class="row my-5">
        <div class="col-12 text-center">
            <h2>Tarifs des pièces détachées</h2>
        </div>
   
        <div class="col-11 mx-auto py-3">
            <table class="table table-sm col-12 col-sm-10 mt-4 mx-auto">
                <thead class="thead-dark text-center">
                    <tr>
                        <th scope="col">Pièce</th>
                        <th scope="col">Prix Unitaire TTC</th>
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
                        <td>Pièce en 1 seul exemplaire dans le jeu<br/>
                        <span class="small">Exemples : dé spécial, sablier, pion spécifique…</span>
                        </td>
                        <td class="text-center">
                            <?php echo str_replace(".",",",$donneesConfig[18]['valeur']);?> €
                        </td>
                    </tr>
                    <tr>
                        <td>Pièces en bois ou en métal<br/>
                        </td>
                        <td class="text-center">
                            <?php echo str_replace(".",",",$donneesConfig[18]['valeur']);?> €
                        </td>
                    </tr>
                    <tr>
                        <td>Pièces en grande quantité dans le jeu<br/>
                        <span class="small"> Exemples : jetons de Mastermind, de Touché-Coulé</span></td>
                        <td class="text-center">
                            <?php echo str_replace(".",",",$donneesConfig[16]['valeur']);?> €
                        </td>
                    </tr>
                    <tr>
                        <td>Autres pièces de jeu</td>
                        <td class="text-center">
                            <?php echo str_replace(".",",",$donneesConfig[17]['valeur']);?> €
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-11 mx-auto py-3">
            <table class="table table-sm col-12 col-sm-10 mt-4 mx-auto">
                <thead class="thead-dark text-center">
                    <tr>
                        <th scope="col">Type de pièce</th>
                        <th scope="col">Disponible à la vente</th>
                        <th scope="col">Disponible en retrait sur Caen</th>
                        <th scope="col">Envoi postal possible</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Pièce de moins de 3cm d'épaisseur</td>
                        <td class="text-center">✅</td><td class="text-center">✅</td><td class="text-center">✅</td>
                    </tr>
                    <tr>
                        <td>Pièce de plus de 3cm d'épaisseur</td>
                        <td class="text-center">✅</td><td class="text-center">✅</td><td class="text-center">❌</td>
                    </tr>
                    <tr>
                        <td>Plateau de jeu</td>
                        <td class="text-center">✅</td><td class="text-center">✅</td><td class="text-center">Suivant format</td>
                    </tr>
                    <tr>
                        <td>Règle du jeu</td>
                        <td class="text-center">❌</td><td class="text-center">❌</td><td class="text-center">❌</td>
                    </tr>
                    <tr>
                        <td>Boite incomplète</td>
                        <td class="text-center">❌</td><td class="text-center">❌</td><td class="text-center">❌</td>
                    </tr>
                    <tr>
                        <td>Boite seule</td>
                        <td class="text-center">❌</td><td class="text-center">❌</td><td class="text-center">❌</td>
                    </tr>
                   
                </tbody>
            </table>
        </div>
        <div class="col-11 col-md-9 col-xl-6 mx-auto text-justify my-4 d-flex">
            <div class="col-1"><i class="fas fa-lightbulb text-info fa-2x"></i></div>
            <div class="col-10">En fonction du nombre des pièces achetées, un prix forfaitaire avantageux pourra être proposé (exemples : lot de cartes ou de jetons).</div>
        </div>
    </div>
    <!-- TARIFS DES ENVOIS -->
    <div class="row my-5">
        <h2  class="col-12 text-center">Tarifs d'envoi / de retrait</h2>
        <div class="col-11 mx-auto py-3">
            <form method="post" action="">
                <table class="table table-sm col-12 col-sm-10 mt-4 mx-auto">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th scope="col">Mode d'envoi</th>
                            <th scope="col">Prix Unitaire TTC</th>
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
                    </tbody>
                </table>
            </form>
        </div>
        <div class="col-11 col-md-9 col-xl-6 mx-auto text-justify my-4 d-flex">
            <div class="col-1"><i class="fas fa-lightbulb text-info fa-2x"></i></div>
            <div class="col-10">Les frais de port sont calculés en fonction du poids, de la taille des pièces commandées.</div>
        </div>
    </div>
    <!-- PAIEMENTS -->
    <div class="row my-5">
        <h2 class="col-12 text-center">Paiements</h2>
        <div class="col-11 col-md-9 col-xl-6 mx-auto text-justify my-4">
            <div class="col-12">Les paiements sont effectués par carte bancaire via des pages sécurisées.</div> 
            <div class="col-12 text-center mt-3">
                <img class="img-thumbnail" src="/PAIEMENT/<?php echo $GLOBAL['servicePaiement']; ?>/logo-<?php echo $GLOBAL['servicePaiement'];?>.png" alt="Paiement avec <?php echo $GLOBAL['servicePaiement']; ?>"/>
            
            </div>
            <div class="col-12 mt-5">Après le paiement de la commande il vous est possible de demander une facture.</div>
        </div>
        </div>
    </div>
</div>
<?php
include_once("../../commun/bas_de_page.php");
?>
