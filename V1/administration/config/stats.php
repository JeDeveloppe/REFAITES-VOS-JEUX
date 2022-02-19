<?php
@session_start ();
$titreDeLaPage = "[ADMIN] - Statistiques";
$descriptionPage = "";
require("../../controles/fonctions/adminOnline.php");
include_once('../../config.php');
include_once('../../bdd/connexion-bdd.php');
include_once("../../bdd/table_config.php");

//PREMIERE FACTURE
$sqlPremiereFacture = $bdd-> query("SELECT * FROM documents WHERE numero_facture != '' ORDER BY time ASC LIMIT 1");
$donneesPremiereFacture =$sqlPremiereFacture-> fetch();
$moisDebut =  date("m",$donneesPremiereFacture['time']);
//DERNIERE FACTURE
$sqlDerniereFacture = $bdd-> query("SELECT * FROM documents WHERE numero_facture != '' ORDER BY time DESC LIMIT 1");
$donneesDerniereFacture =$sqlDerniereFacture-> fetch();
$moisFin =  date("m",$donneesDerniereFacture['time']);
//CALCULE DIFFERENCE ENTRE LES MOIS POUR COMMISSION MENSUELLE
$nombreDeMois = $moisFin-$moisDebut;

//NOMBRE DE CLIENTS AYANT PASSER UNE DEMANDE DE DEVIS
$sqlUsers = $bdd -> query("SELECT * FROM clients") ;
$donneesUsers = $sqlUsers->fetch();
$nombreTotalUsers = $sqlUsers->rowCount();

//NOMBRE TOTAL DE DEVIS FAIT
$sqlDevisTotal = $bdd -> query("SELECT * FROM documents ORDER BY idDocument DESC LIMIT 1") ;
$donneesLastRow = $sqlDevisTotal-> fetch();

//NOMBRE TOTAL DE DEVIS EN ATTENTE DE TRANSFORMATION
$sqlDevis = $bdd -> query("SELECT * FROM documents WHERE etat = 1") ;
$donneesDevis = $sqlDevis->fetch();
$nombreTotalDevis = $sqlDevis->rowCount();

//NOMBRE TOTAL DE FACTURES
$sqlFactures = $bdd -> query("SELECT * FROM documents WHERE numero_facture != '' ") ;
$donneesFactures = $sqlFactures->fetch();
$nombreTotalFactures = $sqlFactures->rowCount();
//NOMBRE TOTAL DE FACTURES EN CB
$sqlFacturesCB = $bdd -> query("SELECT * FROM documents WHERE numero_facture != '' AND moyen_paiement = 'CB' ") ;
$donneesFacturesCB = $sqlFacturesCB->fetch();
$nombreTotalFacturesCB = $sqlFacturesCB->rowCount();
//NOMBRE TOTAL DE FACTURES EN VIREMENT
$sqlFacturesVIR = $bdd -> query("SELECT * FROM documents WHERE numero_facture != '' AND moyen_paiement = 'VIR' ") ;
$donneesFacturesVIR = $sqlFacturesVIR->fetch();
$nombreTotalFacturesVIR = $sqlFacturesVIR->rowCount();
//NOMBRE TOTAL DE FACTURES EN CHEQUE
$sqlFacturesCHQ = $bdd -> query("SELECT * FROM documents WHERE numero_facture != '' AND moyen_paiement = 'CHQ' ") ;
$donneesFacturesCHQ = $sqlFacturesCHQ->fetch();
$nombreTotalFacturesCHQ = $sqlFacturesCHQ->rowCount();
//NOMBRE TOTAL DE FACTURES EN ESPECE
$sqlFacturesESP = $bdd -> query("SELECT * FROM documents WHERE numero_facture != '' AND moyen_paiement = 'ESP' ") ;
$donneesFacturesESP = $sqlFacturesESP->fetch();
$nombreTotalFacturesESP = $sqlFacturesESP->rowCount();


//NOMBRE TOTAL DE PIECES COMMANDEE
$sqlJeuxCompleter = $bdd -> query('SELECT * FROM documents_lignes WHERE idDocument IN (SELECT idDocument FROM documents WHERE numero_facture != "")') ;
$nombreTotalJeuxCompleter = $sqlJeuxCompleter->rowCount();

//CA TOTAL HT PORT
$sqlCATotalPreparation = $bdd -> query("SELECT SUM(prix_preparation) AS total FROM documents WHERE numero_facture != '' ") ;
$donneesCATotalPreparation = $sqlCATotalPreparation->fetch();
//CA TOTAL HT PORT
$sqlCATotalPort = $bdd -> query("SELECT SUM(prix_expedition) AS total FROM documents WHERE numero_facture != '' ") ;
$donneesCATotalPort = $sqlCATotalPort->fetch();

//CA TOTAL CB HT
$sqlCATotalCB = $bdd -> query("SELECT SUM(totalHT) AS total FROM documents WHERE numero_facture != '' AND moyen_paiement = 'CB' ") ;
$donneesCATotalCB = $sqlCATotalCB->fetch();
//CA TOTAL VIR HT
$sqlCATotalVIR = $bdd -> query("SELECT SUM(totalHT) AS total FROM documents WHERE numero_facture != '' AND moyen_paiement = 'VIR' ") ;
$donneesCATotalVIR = $sqlCATotalVIR->fetch();
//CA TOTAL CHQ HT
$sqlCATotalCHQ = $bdd -> query("SELECT SUM(totalHT) AS total FROM documents WHERE numero_facture != '' AND moyen_paiement = 'CHQ' ") ;
$donneesCATotalCHQ = $sqlCATotalCHQ->fetch();
//CA TOTAL ESP HT
$sqlCATotalESP = $bdd -> query("SELECT SUM(totalHT) AS total FROM documents WHERE numero_facture != '' AND moyen_paiement = 'ESP' ") ;
$donneesCATotalESP = $sqlCATotalESP->fetch();

//NOMBRE DE COMMANDES TOTAL PAR PAYS
$sqlTerritoires = $bdd -> query("SELECT * FROM pays WHERE actif = 1 ORDER BY nom_fr_fr ASC");
$donneesTerritoires = $sqlTerritoires-> fetchAll();


//////PARTIE SATISFACTION CLIENT
//ON COMTPE TOUTES LES DEMANDES
$sqlSatisfactionTotal = $bdd-> query("SELECT * FROM bouteille_mer");
$countSatisfactionTotal = $sqlSatisfactionTotal-> rowCount();
//ON COMTPE TOUTES LES DEMANDES TRAITEES
$sqlSatisfaction = $bdd-> query("SELECT * FROM bouteille_mer WHERE actif != 0");
$countSatisfaction = $sqlSatisfaction-> rowCount();
//ON COMPTE LES CONTANTS
$sqlSatisfactionOk = $bdd-> query("SELECT * FROM bouteille_mer WHERE actif = 3");
$countSatisfactionOk = $sqlSatisfactionOk-> rowCount();
//ON COMPTE LENS EUTRES
$sqlSatisfactionNeutre = $bdd-> query("SELECT * FROM bouteille_mer WHERE actif = 1");
$countSatisfactionNeutre = $sqlSatisfactionNeutre-> rowCount();
//ON COMTE LES PAS CONTANT
$sqlSatisfactionKo = $bdd-> query("SELECT * FROM bouteille_mer WHERE actif = 4");
$countSatisfactionKo = $sqlSatisfactionKo-> rowCount();

//ON COMTPE TOUTES LES MESSAGES DU LIVRE D'OR
$sqlLivreTotal = $bdd-> query("SELECT * FROM livreOr");
$countMessagesLivreTotal = $sqlLivreTotal-> rowCount();

//CALCULS DES COMMISSIONS
//CA TOTAL HT PORT
$sqlCommissionsSurLesVentes = $bdd -> query("SELECT SUM(commission_vente) AS totalCommissions FROM documents WHERE numero_facture != '' ") ;
$donneesCommissionsSurLesVentes = $sqlCommissionsSurLesVentes->fetch();
$commissionsSurLesVentes = $donneesCommissionsSurLesVentes['totalCommissions'];

$commissionsMensuelles = $nombreDeMois * $donneesConfig[24]['valeur'];
$totalDesCommissions = $commissionsSurLesVentes + $commissionsMensuelles;

include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
?>

<div class="card col-11 mt-5 mx-auto p-0">
    <div class="card-header text-center bg-dark text-white">
        TABLEAU DE BORD
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 h4">Chiffres commerce:</div>
                <div class="col-12 d-flex flex-wrap justify-content-around text-center p-0">
                    <div class="card col-5 my-1 p-1">Nbre de demandes total: <?php echo $nombreTotalUsers; ?></div>
                    <div class="card col-5 my-1 p-1">Nbre de devis total créés: <?php echo $donneesLastRow['idDocument']; ?></div>
                    <div class="card col-5 my-1 p-1">Nbre de jeux complétés: <?php echo $nombreTotalJeuxCompleter; ?> (en tout: <?php echo $nombreTotalJeuxCompleter + 120; ?>)</div>
                    <div class="card col-5 my-1 p-1">Nbre de devis annulé: <?php echo $donneesLastRow['idDocument'] - ($nombreTotalDevis +$nombreTotalFactures); ?></div>
                    <div class="card col-5 my-1 p-1">Nbre de devis en attente: <?php echo $nombreTotalDevis; ?></div>
                    <div class="card col-5 my-1 p-1">Nbre de factures payées: <?php echo $nombreTotalFactures; ?> (CB: <?php echo $nombreTotalFacturesCB; ?> - VIR: <?php echo $nombreTotalFacturesVIR; ?> - CHQ: <?php echo $nombreTotalFacturesCHQ; ?> - ESP: <?php echo $nombreTotalFacturesESP; ?>)</div>
                    <div class="card col-5 my-1 p-1">CA TOTAL EXPEDITION (HT): <?php echo number_format($donneesCATotalPort['total'],2); ?></div>
                    <div class="card col-5 my-1 p-1">CA TOTAL PREPARATION (HT): <?php echo number_format($donneesCATotalPreparation['total'],2); ?></div>
                    <div class="card col-5 my-1 p-1">CA TOTAL PIECES (HT): <?php echo ($donneesCATotalCB['total']+$donneesCATotalVIR['total']+$donneesCATotalCQG['total']+$donneesCATotalESP['total'])-$donneesCATotalPort['total']-$donneesCATotalPreparation['total']; ?></div>
                    <hr class="col-10 mx-auto">
                    <div class="card col-5 my-1 p-1">CA TOTAL CB (HT): <?php echo number_format($donneesCATotalCB['total'],2); ?></div>
                    <div class="card col-5 my-1 p-1">CA TOTAL VIR (HT): <?php echo number_format($donneesCATotalVIR['total'],2); ?></div>
                    <div class="card col-5 my-1 p-1">CA TOTAL CQH (HT): <?php echo number_format($donneesCATotalCHQ['total'],2); ?></div>
                    <div class="card col-5 my-1 p-1">CA TOTAL ESP (HT): <?php echo number_format($donneesCATotalESP['total'],2); ?></div>
                    <div class="card col-5 my-1 p-1 bg-success">CA TOTAL (HT): <?php echo number_format($donneesCATotalCB['total']+$donneesCATotalVIR['total']+$donneesCATotalCQG['total']+$donneesCATotalESP['total'],2); ?></div>
                    <div class="card col-5 my-1 p-1 bg-success">MARGE AVANT COMMISSIONS (HT): <?php echo number_format(($donneesCATotalCB['total']+$donneesCATotalVIR['total']+$donneesCATotalCQG['total']+$donneesCATotalESP['total'])-$donneesCATotalPort['total'],2); ?></div>
                    <hr class="col-10 mx-auto">
                    <div class="col-12 h4 text-left">Les commissions:</div>
                    <div class="card col-5 my-1 p-1">COMMISSIONS SUR LES VENTES (HT): <?php echo number_format($commissionsSurLesVentes,2); ?></div>
                    <div class="card col-5 my-1 p-1">COMMISSIONS MENSUELLES (HT): <?php echo $nombreDeMois.' mois soit '. $commissionsMensuelles+3.93; ?></div>
                    <div class="card col-5 my-1 p-1 bg-warning">TOTAL DES COMMISSIONS (HT): <?php echo number_format($totalDesCommissions,2); ?></div>
                    <hr class="col-10 mx-auto">
                    <div class="col-12 h4 text-left">Marge net:</div>
                    <div class="card col-5 my-1 p-1 bg-success">MARGE BRUTE(HT): <?php echo number_format((($donneesCATotalCB['total']+$donneesCATotalVIR['total']+$donneesCATotalCQG['total']+$donneesCATotalESP['total'])-$donneesCATotalPort['total'])- $totalDesCommissions,2); ?></div>
                    <hr class="col-10 mx-auto">
                    <div class="col-12 h4 text-left">Ventes par pays: (<?php echo $nombreTotalFactures; ?>)</div>
                    <?php
                    $sqlCommandesFranceMetropolitaine = $bdd -> query('SELECT * FROM clients LEFT JOIN documents ON clients.idClient = documents.idUser WHERE clients.pays = "FR" AND documents.numero_facture != "" ') ;
                    $nombreTotalCommandesFranceMetropolitaine = $sqlCommandesFranceMetropolitaine->rowCount();
                    echo '<div class="card col-5 my-1 p-1">France métropolitaine: '.$nombreTotalCommandesFranceMetropolitaine.'</div>';
                    foreach($donneesTerritoires as $territoire){
                        $sqlCommandesParPays = $bdd -> prepare('SELECT * FROM clients LEFT JOIN documents ON clients.idClient = documents.idUser WHERE clients.pays = ? AND documents.numero_facture != "" ') ;
                        $sqlCommandesParPays-> execute(array($territoire['alpha2']));
                        $nombreTotalCommandesParPays = $sqlCommandesParPays->rowCount();
                        echo '<div class="card col-5 my-1 p-1">'.$territoire['nom_fr_fr'].': '.$nombreTotalCommandesParPays.'</div>';
                    }
                    ?>
                </div>
        </div>
        <hr class="border border-primary">
        <div class="row">
            <div class="col-12 h4">Bouteilles à la mer:</div>
                <div class="col-12 d-flex flex-wrap justify-content-around text-center p-0">
                    <div class="card col-5 my-1 p-1 align-self-center">Nombre de bouteilles totales: <?php echo $countSatisfactionTotal; ?></div>
                    <div class="card col-5 my-1 p-1"><i class="fas fa-info-circle" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Toutes bouteille doit en jour être retournée donc...<br />(objectif: 90%)"></i> Nombre de bouteilles traitées: <?php echo $countSatisfaction; ?> ( <?php echo number_format(($countSatisfaction/$countSatisfactionTotal)*100);?> %)</div>
                    <div class="card col-5 my-1 p-1 bg-success"><i class="fas fa-info-circle" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Un max de bouteilles doivent avoir un retour positif...<br/>(bjectif: 90%)"></i> Nombre satisfait: <?php echo $countSatisfactionOk;?> ( <?php echo number_format(($countSatisfactionOk/$countSatisfaction)*100);?> %)</div>
                    <div class="card col-5 my-1 p-1 bg-danger"><i class="fas fa-info-circle" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Un min de bouteilles doivent avoir un retour positif...<br/>(0bjectif max: 5%)"></i> Nombre NON satisfait: <?php echo $countSatisfactionKo;?> ( <?php echo number_format(($countSatisfactionKo/$countSatisfaction)*100);?> %)</div>
                    <div class="card col-5 my-1 p-1"><i class="fas fa-info-circle" data-html="true" data-toggle="tooltip" data-placement="bottom" title="tout le monde doit répondre...<br/>(Objectif max: 5%)"></i> Nombre traité non répondu: <?php echo $countSatisfactionNeutre;?> ( <?php echo number_format(($countSatisfactionNeutre/$countSatisfaction)*100);?> %)</div>
                </div>
        </div>
        <hr class="border border-primary">
        <div class="row">
            <div class="col-12 h4">Livre d'or:</div>
                <div class="col-12 d-flex flex-wrap justify-content-around text-center">
                    <div class="card col-3 m-3 p-0">Nombre de messages total: <?php echo $countMessagesLivreTotal; ?></div>
                </div>
        </div>
        <hr class="border border-primary">
        <div class="row">
            <div class="col-12 h4">Ratios:</div>
                <div class="col-12 d-flex flex-wrap justify-content-around text-center">
                    <div class="card col-5 my-1 p-0"><i class="fas fa-info-circle" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Une demande doit devenir un devis donc...<br />(objectif: 100%)"></i> Ratio devis / demande: <?php echo number_format(($donneesLastRow['idDocument'] / $nombreTotalUsers * 100),2)." %"; ?></div>
                    <div class="card col-5 my-1 p-0"><i class="fas fa-info-circle" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Un max de devis doit devenir une facture payée...<br/>(bjectif: 90%)"></i> Ratio facture / devis: <?php echo number_format(($nombreTotalFactures / $donneesLastRow['idDocument'] * 100),2)." %"; ?></div>
                </div>
        </div>
    </div>
</div>

<?php include_once("../../commun/bas_de_page-admin.php");?>