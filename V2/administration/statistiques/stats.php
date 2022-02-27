<?php
@session_start ();
$titreDeLaPage = "[ADMIN] - Statistiques";
$descriptionPage = "";
require("../../controles/fonctions/adminOnline.php");
include_once('../../config.php');
include_once('../../bdd/connexion-bdd.php');
include_once("../../bdd/table_config.php");

//PREMIERE FACTURE
$sqlPremiereFacture = $bdd-> query("SELECT * FROM documents WHERE etat = 2 ORDER BY time ASC LIMIT 1");
$donneesPremiereFacture =$sqlPremiereFacture-> fetch();
$moisDebut =  date("m",$donneesPremiereFacture['time']);
//DERNIERE FACTURE
$sqlDerniereFacture = $bdd-> query("SELECT * FROM documents WHERE etat = 2 ORDER BY time DESC LIMIT 1");
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
$sqlFactures = $bdd -> query("SELECT * FROM documents WHERE etat = 2 ") ;
$donneesFactures = $sqlFactures->fetch();
$nombreTotalFactures = $sqlFactures->rowCount();
//NOMBRE TOTAL DE FACTURES EN CB
$sqlFacturesCB = $bdd -> query("SELECT * FROM documents WHERE etat = 2 AND moyen_paiement = 'CB' ") ;
$donneesFacturesCB = $sqlFacturesCB->fetch();
$nombreTotalFacturesCB = $sqlFacturesCB->rowCount();
//NOMBRE TOTAL DE FACTURES EN VIREMENT
$sqlFacturesVIR = $bdd -> query("SELECT * FROM documents WHERE etat = 2 AND moyen_paiement = 'VIR' ") ;
$donneesFacturesVIR = $sqlFacturesVIR->fetch();
$nombreTotalFacturesVIR = $sqlFacturesVIR->rowCount();
//NOMBRE TOTAL DE FACTURES EN CHEQUE
$sqlFacturesCHQ = $bdd -> query("SELECT * FROM documents WHERE etat = 2 AND moyen_paiement = 'CHQ' ") ;
$donneesFacturesCHQ = $sqlFacturesCHQ->fetch();
$nombreTotalFacturesCHQ = $sqlFacturesCHQ->rowCount();
//NOMBRE TOTAL DE FACTURES EN ESPECE
$sqlFacturesESP = $bdd -> query("SELECT * FROM documents WHERE etat = 2 AND moyen_paiement = 'ESP' ") ;
$donneesFacturesESP = $sqlFacturesESP->fetch();
$nombreTotalFacturesESP = $sqlFacturesESP->rowCount();


//NOMBRE TOTAL DE PIECES COMMANDEE
$sqlJeuxCompleter = $bdd ->query('SELECT * FROM documents_lignes WHERE idDocument IN (SELECT idDocument FROM documents WHERE numero_facture != "")') ;
$nombreTotalJeuxCompleter = $sqlJeuxCompleter->rowCount();

//NOMBRE TOTAL DE JEUX ACHETE
$sqlJeuxOccasionVendus = $bdd->query('SELECT * FROM documents_lignes_achats WHERE idDocument IN (SELECT idDocument FROM documents WHERE numero_facture != "")') ;
$nombreTotalJeuxOccasionVendus = $sqlJeuxOccasionVendus->rowCount();

$poidsTotalCO2 = 0;
$jeuxOccasions = $sqlJeuxOccasionVendus->fetchAll();
foreach($jeuxOccasions as $jeuOccassion){
    $sqlCataloguePoid = $bdd->prepare("SELECT poidBoite FROM catalogue WHERE idCatalogue = ?");
    $sqlCataloguePoid->execute(array($jeuOccassion['idCatalogue']));
    $donneesPoidBoite = $sqlCataloguePoid->fetch();
    $poidsTotalCO2 += $donneesPoidBoite['poidBoite'];
}

//CA TOTAL HT PREPARATION
$sqlCATotalPreparation = $bdd->query("SELECT SUM(prix_preparation) AS total FROM documents WHERE etat = 2 ") ;
$donneesCATotalPreparation = $sqlCATotalPreparation->fetch();
//CA TOTAL HT PORT
$sqlCATotalPort = $bdd->query("SELECT SUM(prix_expedition) AS total FROM documents WHERE etat = 2 ") ;
$donneesCATotalPort = $sqlCATotalPort->fetch();
//CA TOTAL HT DES DOCUMENTS
$sqlCATotalDocuments = $bdd->query("SELECT SUM(totalHT) AS total FROM documents WHERE etat = 2 ") ;
$donneesCATotalDocuments = $sqlCATotalDocuments->fetch();

//CA TOTAL CB HT
$sqlCATotalCB = $bdd -> query("SELECT SUM(totalHT) AS total FROM documents WHERE etat = 2 AND moyen_paiement = 'CB' ") ;
$donneesCATotalCB = $sqlCATotalCB->fetch();
//CA TOTAL VIR HT
$sqlCATotalVIR = $bdd -> query("SELECT SUM(totalHT) AS total FROM documents WHERE etat = 2 AND moyen_paiement = 'VIR' ") ;
$donneesCATotalVIR = $sqlCATotalVIR->fetch();
//CA TOTAL CHQ HT
$sqlCATotalCHQ = $bdd -> query("SELECT SUM(totalHT) AS total FROM documents WHERE etat = 2 AND moyen_paiement = 'CHQ' ") ;
$donneesCATotalCHQ = $sqlCATotalCHQ->fetch();
//CA TOTAL ESP HT
$sqlCATotalESP = $bdd -> query("SELECT SUM(totalHT) AS total FROM documents WHERE etat = 2 AND moyen_paiement = 'ESP' ") ;
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

//on cherche l'année en cours
$anneeCivil = date("Y", time());

include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
?>

<div class="container-fluid mb-5">
    <div class="row">
        <div class="col-3 bg-vos">
            <div class="col-12 h3 text-center mt-5">Graphiques:</div>
            <div class="input-group col-11 mx-auto mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">Année:</span>
                </div>
                <input type="text" class="form-control text-center" placeholder="ex: 2021" id="anneeGraphique">
            </div>
            <div class="col-12 mt-4 text-center d-flex flex-column">
                <div>Les ventes:</div>
                <div id="ventes"></div>
                <div id="ventes_repartition"></div>
                <div id="ventes_comparaison"></div>
            </div>
            <div class="col-12 mt-2 text-center d-flex flex-column">
                <div>Les grammes: (total)</div>
                <div id="grammes"></div>
                <div id="grammes_comparaison"></div>
            </div>
            <div class="col-12 mt-2 text-center d-flex flex-column">
                <div>Les grammes: (juste DEEE)</div>
                <div id="grammesDeee"></div>
                <div id="grammes_comparaisonDeee"></div>
            </div>

        </div>
        <div class="col-9 border-left border-dark">
            <div class="col-12 h3 text-center mt-5">Statistiques générales (depuis le début)</div>
            <div class="col-12 h4">Commerce:</div>
                <div class="col-12 d-flex flex-wrap justify-content-around text-center p-0">
                    <div class="card col-5 my-1 p-1">CA TOTAL CB (HT): <?php echo number_format($donneesCATotalCB['total'] / 100,2); ?></div>
                    <div class="card col-5 my-1 p-1">CA TOTAL VIR (HT): <?php echo number_format($donneesCATotalVIR['total'] / 100,2); ?></div>
                    <div class="card col-5 my-1 p-1">CA TOTAL CHQ (HT): <?php echo number_format($donneesCATotalCHQ['total'] / 100,2); ?></div>
                    <div class="card col-5 my-1 p-1">CA TOTAL ESP (HT): <?php echo number_format($donneesCATotalESP['total'] / 100,2); ?></div>
                    <div class="col-12 my-3">
                    <div class="card col-5 my-1 mx-auto p-1 bg-success">CA TOTAL (HT): <?php echo number_format(($donneesCATotalCB['total']+$donneesCATotalVIR['total']+$donneesCATotalCHQ['total']+$donneesCATotalESP['total']) / 100,2); ?></div>
                    </div>
                    <div class="card col-5 my-1 p-1">CA TOTAL EXPEDITION (HT): <?php echo number_format($donneesCATotalPort['total'] /100,2); ?></div>
                    <div class="card col-5 my-1 p-1">CA TOTAL PREPARATION (HT): <?php echo number_format($donneesCATotalPreparation['total'] /100,2); ?></div>
                    <div class="card col-5 my-1 p-1">CA TOTAL PIECES (HT): <?php echo (($donneesCATotalCB['total']+$donneesCATotalVIR['total']+$donneesCATotalCHQ['total']+$donneesCATotalESP['total'])-$donneesCATotalPort['total']-$donneesCATotalPreparation['total']) /100; ?></div>
                    <hr class="col-10 mx-auto">
                    <div class="card col-5 my-1 p-1 bg-success">MARGE AVANT COMMISSIONS (HT)<br/>(CA TOTAL - CA TOTAL EXPEDITION):<br/> <?php echo number_format((($donneesCATotalCB['total']+$donneesCATotalVIR['total']+$donneesCATotalCHQ['total']+$donneesCATotalESP['total'])-$donneesCATotalPort['total']) / 100,2); ?></div>
                </div>
            <hr class="col-10 mx-auto">
            <div class="col-12 h4 mt-4">Ventes:</div>
                <div class="col-12 d-flex flex-wrap justify-content-around text-center p-0">
                    <div class="card col-5 my-1 p-1">Nbre de jeux d'occasion vendus: <?php echo $nombreTotalJeuxOccasionVendus; ?></div>
                    <div class="card col-5 my-1 p-1">Poid jeux d'occasion (en g): <?php echo $poidsTotalCO2; ?></div>
                    <div class="card col-5 my-1 p-1">Nbre de jeux complétés: <?php echo $nombreTotalJeuxCompleter; ?> (en tout: <?php echo $nombreTotalJeuxCompleter + 120; ?>)</div>
                </div>
            <hr class="col-10 mx-auto">
            <div class="col-12 h4 mt-4">Chiffres:</div>
                <div class="col-12 d-flex flex-wrap justify-content-around text-center">
                    <div class="card col-5 my-1 p-1">Nbre de demandes total: <?php echo $nombreTotalUsers; ?></div>
                    <div class="card col-5 my-1 p-1">Nbre de devis total créés: <?php echo $donneesLastRow['idDocument']; ?></div>
                    <div class="card col-5 my-1 p-1">Nbre de devis annulé: <?php echo $donneesLastRow['idDocument'] - ($nombreTotalDevis +$nombreTotalFactures); ?></div>
                    <div class="card col-5 my-1 p-1">Nbre de devis en attente: <?php echo $nombreTotalDevis; ?></div>
                    <div class="card col-5 my-1 p-0"><i class="fas fa-info-circle" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Une demande doit devenir un devis donc...<br />(objectif: 100%)"></i> Ratio devis / demande: <?php echo number_format(($donneesLastRow['idDocument'] / $nombreTotalUsers * 100),2)." %"; ?></div>
                    <div class="card col-5 my-1 p-0"><i class="fas fa-info-circle" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Un max de devis doit devenir une facture payée...<br/>(bjectif: 90%)"></i> Ratio facture / devis: <?php echo number_format(($nombreTotalFactures / $donneesLastRow['idDocument'] * 100),2)." %"; ?></div>
                </div>
        </div>
    </div>
</div>

<?php include_once("../../commun/bas_de_page-admin.php");?>

<script>
    let anneeGraphique = document.getElementById('anneeGraphique');
    let divVentes = document.getElementById('ventes');
    let divVentes_repartition = document.getElementById('ventes_repartition');
    let divVentes_comparaison = document.getElementById('ventes_comparaison');
    let divGrammes = document.getElementById('grammes');
    let divGrammes_comparaison = document.getElementById('grammes_comparaison');
    let divGrammesDeee = document.getElementById('grammesDeee');
    let divGrammes_comparaisonDeee = document.getElementById('grammes_comparaisonDeee');

    divVentes.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/ventes/">LES VENTES de l\'année N</a>';
    divVentes_repartition.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/ventes/repartition/">REPARTITION de l\'année N</a>';
    divVentes_comparaison.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/ventes/comparaison/">COMPARAISON Année N-1 et N</a>';

    divGrammes.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/grammes/">Évolution de l\'année N</a>';
    divGrammes_comparaison.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/grammes/comparaison/">COMPARAISON Année N-1 et N</a>';

    divGrammesDeee.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/grammes/deee/">Évolution de l\'année N</a>';
    divGrammes_comparaisonDeee.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/grammes/comparaison/deee/">COMPARAISON Année N-1 et N</a>';
        
    anneeGraphique.addEventListener('keyup', () => {
        if(anneeGraphique.value.length == 4 && anneeGraphique.value > 2020 && anneeGraphique.value.match(/^\d{4}$/)){
            let anneeBefore = anneeGraphique.value - 1;
            divVentes.innerHTML = '<a class="btn btn-info mb-2" href="/admin/statistiques/ventes/'+anneeGraphique.value+'/" target="_blank">LES VENTES de l\'année N</a>';
            divVentes_repartition.innerHTML = '<a class="btn btn-info mb-2" href="/admin/statistiques/ventes/repartition/'+anneeGraphique.value+'/" target="_blank">REPARTITION de l\'année N</a>';
            divVentes_comparaison.innerHTML = '<a class="btn btn-info mb-2" href="/admin/statistiques/ventes/comparaison/'+anneeBefore+'-'+anneeGraphique.value+'/" target="_blank">COMPARAISON  Année N-1 et N</a>';

            divGrammes.innerHTML = '<a class="btn btn-info mb-2" href="/admin/statistiques/grammes/'+anneeGraphique.value+'/" target="_blank">Évolution de l\'année N</a>';
            divGrammes_comparaison.innerHTML = '<a class="btn btn-info mb-2" href="/admin/statistiques/grammes/comparaison/'+anneeBefore+'-'+anneeGraphique.value+'/" target="_blank">COMPARAISON Année N-1 et N</a>';
            
            divGrammesDeee.innerHTML = '<a class="btn btn-info mb-2" href="/admin/statistiques/grammes/deee/'+anneeGraphique.value+'/" target="_blank">Évolution de l\'année N</a>';
            divGrammes_comparaisonDeee.innerHTML = '<a class="btn btn-info mb-2" href="/admin/statistiques/grammes/comparaison/deee/'+anneeBefore+'-'+anneeGraphique.value+'/" target="_blank">COMPARAISON Année N-1 et N</a>';
       
        }else{
            divVentes.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/ventes/">LES VENTES de l\'année N</a>';
            divVentes_repartition.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/ventes/repartition/">REPARTITION de l\'année N</a>';
            divVentes_comparaison.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/ventes/comparaison/">COMPARAISON Année N-1 et N</a>';
                
            divGrammes.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/grammes/">Évolution de l\'année N</a>';
            divGrammes_comparaison.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/grammes/comparaison/">COMPARAISON Année N-1 et N</a>';

            divGrammesDeee.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/grammes/deee/">Évolution de l\'année N</a>';
            divGrammes_comparaisonDeee.innerHTML = '<a class="btn btn-info disabled mb-2" href="/admin/statistiques/grammes/comparaison/deee/">COMPARAISON Année N-1 et N</a>';
       
        }
    })

</script>