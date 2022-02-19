<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
require("../../bdd/table_config.php");
$tva = $donneesConfig[6]['valeur'];
require("../../controles/fonctions/moisEnTexte.php");
require("../../controles/fonctions/calculePrix.php");
$titreDeLaPage = "[ADMIN] - Comptabilité";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");


//si on recherche quelque chose
if(isset($_GET['debut']) && isset($_GET['fin'])){
    $debut = explode('/',$_GET['debut']);
    $fin = explode('/',$_GET['fin']);

    if($debut[1] > $fin[1]){
        $erreur = "Erreur dans les mois !";
    }
    if($debut[2] > $fin[2]){
        $erreur = "Erreur dans les années !";
    }
    //on surveille les mois
    if($debut[1] == 2 && $debut[0] > 29 || $fin[1] == 2 && $fin[0] > 29){
        $erreur = "Février donc pas possible 30 ou 31...";
    }
    $mois31 = array(1,3,5,7,8,10,12);
    if(!in_array($debut[1],$mois31) && $debut[0] > 30 || !in_array($fin[1],$mois31) && $fin[1] > 30){
        $erreur = "Mois à 30 jours max...";
    }
    
    if(isset($erreur)){
        $_SESSION['alertMessage'] = $erreur;
        $_SESSION['alertMessageConfig'] = "warning";
    }else{
        $timeDebut = mktime(0, 00, 0, $debut[1], $debut[0], substr($debut[2],-2));
        $timeFin = mktime(23, 59, 59, $fin[1], $fin[0], substr($fin[2],-2));

        $sqlRecherche = $bdd -> prepare("SELECT * FROM documents WHERE time_transaction BETWEEN ? AND ? AND etat = ? ORDER BY numero_facture ASC");
        $sqlRecherche-> execute(array($timeDebut,$timeFin,2));
        $count = $sqlRecherche-> rowCount();

        //ON FAIT LE TOTAL DES LIGNES DU DOCUMENT
        $calcul = $bdd -> prepare("SELECT SUM(totalHT) AS total FROM documents WHERE time_transaction BETWEEN ? AND ? AND etat = 2");
        $calcul-> execute(array($timeDebut,$timeFin));
        $donneesCalcul = $calcul -> fetch();
    }

    

}

include_once("../../commun/alertMessage.php");
?>
<div class="container">
    <?php
    if(!isset($debut[0])){
    ?>
    <div class="row mt-5">
        <div class="card col-11 mx-auto p-0">
            <div class="card-header bg-secondary text-white text-center h5">Comptabilité - [séléction des dates]:</div>
            <div class="card-body ">
                <form method="get" action ="" class="d-flex flex-wrap justify-content-between">
                    <div class="input-group mb-3 col-3 text-center">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Début:</span>
                        </div>
                        <input type="text" name="debut" class="form-control" placeholder="Format xx/xx/xxxx" pattern="^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$" aria-label="Username" aria-describedby="basic-addon1" required>
                    </div>
                    <div class="input-group mb-3 col-3 text-center">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Fin:</span>
                        </div>
                        <input type="text" name="fin" class="form-control" placeholder="Format xx/xx/xxxx" pattern="^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$" aria-label="Username" aria-describedby="basic-addon1" required>
                    </div>
                    <div class="col text-center mt-1 mb-2">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-info border border-secondary">Afficher</button>
                            <a href="/admin/comptabilite/" class="btn btn-warning border border-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    }else{
        if($count < 1){
            echo '<div class="col-12 text-center mt-5"><a href="/admin/comptabilite/" class="btn btn-danger border border-secondary">Remettre les dates à zéro</a></div>
            <div class="col-12 text-center mt-5 h3">Affichage des factures du '.$debut[0].'/'.$debut[1].'/'.$debut[2].' au '.$fin[0].'/'.$fin[1].'/'.$fin[2].'</div>
            <div class="col-12 text-center h3">Pas de résultat</div>';
        }else{
            echo '
            <div class="col-12 text-center mt-5"><a href="/admin/comptabilite/" class="btn btn-danger border border-secondary">Remettre à zéro</a></div>
            <div class="col-12 text-center mt-5 h3">Factures du '.$debut[0].'/'.$debut[1].'/'.$debut[2].' au '.$fin[0].'/'.$fin[1].'/'.$fin[2].'<br/>['.$count.' factures]</div>
            <table class="table table-sm mt-5 text-center table-bordered">
            <thead class="thead-dark text-center">
                <tr>
                    <th scope="col">Adresse de facturation</th>
                    <th scope="col">Adresse de livraison</th>
                    <th scope="col">Numéro de dacture</th>
                    <th scope="col">Paiement</th>
                    <th scope="col">Total HT</th>
                    <th scope="col">Total TTC</th>
                </tr>
            </thead>
            <tbody>';
                //si y a une recherche 
                $donneesRecherche = $sqlRecherche-> fetchAll();
                foreach($donneesRecherche as $doc){
                    $detailClientFacturation = explode('<br/>',$doc['adresse_facturation']);
                    $detailClientLivraison = explode('<br/>',$doc['adresse_livraison']);
                    echo '<tr>
                            <td class="align-middle">'.$detailClientFacturation[0].'<br/>'.$detailClientFacturation[1].'<br/>'.$detailClientFacturation[2].' - '.$detailClientFacturation[3].'</td>
                            <td class="align-middle">'.$detailClientLivraison[0].'<br/>'.$detailClientLivraison[1].'<br/>'.$detailClientLivraison[2].' - '.$detailClientLivraison[3].'</td>
                            <td class="align-middle">'.$doc['numero_facture'].'</td>
                            <td class="align-middle">Le '.date("d-m-Y",$doc['time_transaction']).' à '.date("G:i",$doc['time_transaction']).'<br/>par '.$doc['moyen_paiement'].'</td>
                            <td class="align-middle">'.affichageHTouTTC($doc['totalHT']).'</td>
                            <td class="align-middle">'.affichageHTouTTC($doc['totalTTC']).'</td>
                        </tr>';
                }
            echo '</tbody></table>
                <table class="table table-sm mt-5 text-center col-9 mx-auto table-bordered">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th scope="col">Total HT</th>
                            <th scope="col">TVA</th>
                            <th scope="col">Total TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>'.number_format($donneesCalcul['total'] / 100,2).'</td>
                            <td>'.number_format((($donneesCalcul['total']*$donneesConfig[6]['valeur']) - $donneesCalcul['total']) / 100,2).'</td>
                            <td>'.number_format($donneesCalcul['total']*$donneesConfig[6]['valeur'] / 100,2).'</td>
                        </tr>
                    </tbody>
                </table>
            <div class="col-12 text-center mt-3"><a href="/administration/comptabilite/generation-pdf-compta.php?jd='.$debut[0].'&md='.$debut[1].'&ad='.substr($debut[2],-2).'&jf='.$fin[0].'&mf='.$fin[1].'&af='.substr($fin[2],-2).'" target="_blank" class="btn btn-success">Editer PDF</a></div>';
        }
    ?>

    <?php
    }
    ?>

</div>

<?php require("../../commun/bas_de_page-admin.php");?>