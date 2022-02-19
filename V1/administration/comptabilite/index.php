<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
require("../../bdd/table_config.php");
require("../../controles/fonctions/moisEnTexte.php");
$titreDeLaPage = "[ADMIN] - Comptabilité";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");


//si on recherche quelque chose
if(isset($_GET['jourDebut'])){
    if($_GET['moisDebut'] > $_GET['moisFin']){
        $erreur = "Erreur dans les mois !";
    }
    if($_GET['anneeDebut'] > $_GET['anneeFin']){
        $erreur = "Erreur dans les années !";
    }
    //on surveille les mois
    if($_GET['moisDebut'] == 2 && $_GET['jourDebut'] > 29 || $_GET['moisFin'] == 2 && $_GET['jourFin'] > 29){
        $erreur = "Février donc pas possible 30 ou 31...";
    }
    $mois31 = array(1,3,5,7,8,10,12);
    if(!in_array($_GET['moisDebut'],$mois31) && $_GET['jourDebut'] > 30 || !in_array($_GET['moisFin'],$mois31) && $_GET['jourFin'] > 30){
        $erreur = "Mois à 30 jours max...";
    }
    
    if(isset($erreur)){
        $_SESSION['alertMessage'] = $erreur;
        $_SESSION['alertMessageConfig'] = "warning";
    }else{
        $timeDebut = mktime(0, 00, 0, $_GET['moisDebut'], $_GET['jourDebut'], $_GET['anneeDebut']);
        $timeFin = mktime(23, 59, 59, $_GET['moisFin'], $_GET['jourFin'], $_GET['anneeFin']);

        $sqlRecherche = $bdd -> prepare("SELECT * FROM documents WHERE time_transaction BETWEEN ? AND ? AND numero_facture != ? ORDER BY numero_facture ASC");
        $sqlRecherche-> execute(array($timeDebut,$timeFin,""));
        $count = $sqlRecherche-> rowCount();

        //ON FAIT LE TOTAL DES LIGNES DU DOCUMENT
        $calcul = $bdd -> prepare("SELECT SUM(totalHT) AS total FROM documents WHERE time_transaction BETWEEN ? AND ?");
        $calcul-> execute(array($timeDebut,$timeFin));
        $donneesCalcul = $calcul -> fetch();
    }

    

}

include_once("../../commun/alertMessage.php");
?>
<div class="container">
    <?php
    if(!isset($_GET['jourDebut'])){
    ?>
    <div class="row mt-5">
        <div class="card col-11 mx-auto p-0">
            <div class="card-header bg-secondary text-white">Séléction des dates:</div>
            <div class="card-body ">
                <form method="get" action ="" class="d-flex flex-wrap justify-content-between">
                    <div class="col-3 text-right">Début:</div>
                    <div class="col-9">
                        <div class="col-12 d-flex">
                            <div class="form-group col">
                                <label class="col text-center">Jour</label>
                                <select class="form-control" name="jourDebut" required>
                                    <option value=""></option>
                                    <?php
                                        for($j = 1; $j < 32; $j++){
                                            echo '<option value="'.$j.'"> '.$j.' </option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col">
                                <label class="col text-center">Mois</label>
                                <select class="form-control" name="moisDebut" required>
                                    <option value=""></option>
                                    <?php
                                        for($m = 01; $m < 13; $m++){
                                            echo '<option value="'.$m.'"> '.moisEnTexte($m).' </option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col">
                                <label class="col text-center">Année</label>
                                <select class="form-control" name="anneeDebut" required>
                                    <option value=""></option>
                                    <?php
                                        for($an = 2021; $an < 2023; $an++){
                                            echo '<option value="'.$an.'"> '.$an.' </option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 text-right">Fin:</div>
                    <div class="col-9">
                        <div class="col-12 d-flex">
                            <div class="form-group col">
                                <label class="col text-center">Jour</label>
                                <select class="form-control" name="jourFin" required>
                                    <option value=""></option>
                                    <?php
                                        for($j = 1; $j < 32; $j++){
                                            echo '<option value="'.$j.'"> '.$j.' </option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col">
                                <label class="col text-center">Mois</label>
                                <select class="form-control" name="moisFin" required>
                                    <option value=""></option>
                                    <?php
                                        for($m = 01; $m < 13; $m++){
                                            echo '<option value="'.$m.'"> '.moisEnTexte($m).' </option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col">
                                <label class="col text-center">Année</label>
                                <select class="form-control" name="anneeFin" required>
                                    <option value=""></option>
                                    <?php
                                        for($an = 2021; $an < 2023; $an++){
                                            echo '<option value="'.$an.'"> '.$an.' </option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
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
            <div class="col-12 text-center mt-5 h3">Affichage des factures du '.$_GET['jourDebut'].'/'.$_GET['moisDebut'].'/'.$_GET['anneeDebut'].' au '.$_GET['jourFin'].'/'.$_GET['moisFin'].'/'.$_GET['anneeFin'].'</div>
            <div class="col-12 text-center h3">Pas de résultat</div>';
        }else{
            echo '
            <div class="col-12 text-center mt-5"><a href="/admin/comptabilite/" class="btn btn-danger border border-secondary">Remettre à zéro</a></div>
            <div class="col-12 text-center mt-5 h3">Affichage des factures du '.$_GET['jourDebut'].'/'.$_GET['moisDebut'].'/'.$_GET['anneeDebut'].' au '.$_GET['jourFin'].'/'.$_GET['moisFin'].'/'.$_GET['anneeFin'].'</div>
            <table class="table table-sm mt-5 text-center">
            <thead class="thead-dark text-center">
                <tr>
                    <th scope="col">N° client</th>
                    <th scope="col">Client</th>
                    <th scope="col">Numéro de document</th>
                    <th scope="col">Paiement</th>
                    <th scope="col">Total HT</th>
                    <th scope="col">Total TTC</th>
                </tr>
            </thead>
            <tbody>';
                //si y a une recherche 
                $donneesRecherche = $sqlRecherche-> fetch();
                while($donneesRecherche){
                    //ON CHERCHE TOUT  DU CLIENT
                    $sqlClient = $bdd-> query("SELECT * FROM clients WHERE idClient = ".$donneesRecherche['idUser']);
                    $donneesClient = $sqlClient-> fetch();

                    echo "<tr><td>".$donneesRecherche['idUser']."</td><td>".$donneesClient['nom']." ".$donneesClient['prenom']."<br />".$donneesClient['adresse']."<br />".$donneesClient['cp']." ".$donneesClient['ville']." ".$donneesClient['pays']."</td><td>".$donneesRecherche['numero_facture']."</td><td>Le ".date("d-m-Y",$donneesRecherche['time_transaction'])." à ".date("G:i",$donneesRecherche['time_transaction'])." par ".$donneesRecherche['moyen_paiement']."</td><td>".$donneesRecherche['totalHT']."</td><td>".$donneesRecherche['totalTTC']."</td></tr>";
                    $donneesRecherche = $sqlRecherche-> fetch();
                }
            echo '</tbody></table>
            <table class="table table-sm mt-5 text-center col-9 mx-auto">
            <thead class="thead-dark text-center">
                <tr>
                    <th scope="col">Total HT</th>
                    <th scope="col">TVA</th>
                    <th scope="col">Total TTC</th>
                </tr>
            </thead>
            <tbody>
            <tr><td>'.number_format($donneesCalcul['total'],2).'</td><td>'.number_format((($donneesCalcul['total']*$donneesConfig[6]['valeur']) - $donneesCalcul['total']),2).'</td><td>'.number_format($donneesCalcul['total']*$donneesConfig[6]['valeur'],2).'</td></tr>
            </tbody>
            </table>
            <div class="col-12 text-center mt-5"><a href="/administration/comptabilite/generation-pdf-compta.php?jd='.$_GET['jourDebut'].'&md='.$_GET['moisDebut'].'&ad='.$_GET['anneeDebut'].'&jf='.$_GET['jourFin'].'&mf='.$_GET['moisFin'].'&af='.$_GET['anneeFin'].'" target="_blank" class="btn btn-success">Editer PDF</a></div>';
        }
    ?>

    <?php
    }
    ?>

</div>

<?php include_once("../../commun/bas_de_page-admin.php");?>