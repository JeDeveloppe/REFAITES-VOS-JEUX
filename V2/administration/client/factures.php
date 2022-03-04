<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");

include('../../config.php');

include('../../bdd/connexion-bdd.php');

$titreDeLaPage = "[ADMIN] - Factures client";
$descriptionPage = "";
include_once("../../bdd/table_config.php");
$tva = $donneesConfig[6]['valeur'];

$sqlClient = $bdd->prepare("SELECT * FROM clients WHERE idUser = ?");
$sqlClient->execute([$_GET['client']]);
$donneesClient = $sqlClient->fetch();

$sqlDocument = $bdd->prepare("SELECT * FROM documents WHERE idUser = ? ORDER BY idDocument DESC");
$sqlDocument-> execute(array($donneesClient['idClient']));
$donneesDocument = $sqlDocument->fetchAll(); 

include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
include_once("../../controles/fonctions/calculePrix.php");

?>

<div class="container-fluid mt-5">
    <h3 class="col-12 text-center my-5">Les documents de:
            <p class="mt-3"><?php echo $donneesClient['nomFacturation'].' '.$donneesClient['prenomFacturation']; ?></p>
    </h3>

    <div class="col-11 mx-auto mY-4"><a class="text-decoration-none" href="<?php echo $_SERVER['HTTP_REFERER']; ?>"><i class="fas fa-chevron-left"> Retour</i></a></div>

    <div class="row mt-3">
        <div class="col-11 mx-auto col-lg-9 container-historique">
            <div id="accordion">
                <?php
                $accordion = 1;
           
                if(count($donneesDocument) > 0){
                    foreach($donneesDocument as $doc){
                        
                        if($doc['numero_facture'] != ""){
                            echo '<div class="card mb-2">
                                    <div class="card-header d-flex flex-wrap align-items-center">
                                        <div class="col-12 col-sm-6 col-md-3 col-lg">Facture n°: '.$doc['numero_facture'].'</div>
                                        <div class="col-12 col-sm-6 col-md-3 col-lg">Date d\' achat: '.date('d.m.Y',$doc['time_transaction']).'</div>
                                        <div class="col-12 col-sm-6 col-md-3 col-lg">Paiement par: <span class="badge bg-warning text-dark">'.$doc['moyen_paiement'].'</span></div>
                                        <div class="col-12 col-sm-6 col-md-3 col-lg">Total TTC: '.htEnTtc($doc['totalTTC'],$tva).' €</div>
                                        <div class="col-12 col-lg text-right"><a href="/administration/client/generation-pdf.php?document='.$doc['validKey'].'" target="_blank" class="btn btn-info border-primary"><i class="fas fa-print"></i> Facture en pdf</a></div>
                                    </div>
                                </div>';
                        }else{
                            if($doc['relance_devis'] == 1){
                                $textRelance = "<span class='badge bg-warning text-secondary'>Devis relancé de ".($donneesConfig[11]['valeur'] / 86400)." jours</span><br/>";
                            }else{
                                $textRelance = '';
                            }
                            //recherche si y a eu des achats de jeu complet
                            $sqlDetailsDocumentsAchats = $bdd-> prepare("SELECT * FROM documents_lignes_achats WHERE idDocument = ?");
                            $sqlDetailsDocumentsAchats->execute(array($doc['idDocument']));
                            $donneesDetailsDocumentAchats = $sqlDetailsDocumentsAchats->fetchAll();
                            //recherche si y eu des demande de pieces
                            $sqlDetailsDocumentsDemandes = $bdd-> prepare("SELECT * FROM documents_lignes WHERE idDocument = ?");
                            $sqlDetailsDocumentsDemandes->execute(array($doc['idDocument']));
                            $donneesDetailsDocumentDemandes = $sqlDetailsDocumentsDemandes->fetchAll();
                            $urlPaiement = $GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/accept.php?doc='.$doc['validKey'].'&user='.$_SESSION['idClient'];
                            echo '<div class="card mb-3 ">
                                    <div class="card-header d-flex flex-wrap align-items-center">
                                        <div class="col-12 col-sm-6 col-md-3 col-lg">Devis n°: '.$doc['numero_devis'].'</div>
                                        <div class="col-12 col-sm-6 col-md-3 col-lg">Date de création: '.date('d.m.Y',$doc['time']).'<br/>'.$textRelance.'Valable jusqu\'au: '.date('d.m.Y',$doc['end_validation']).'</div>
                                        <div class="col-12 col-sm-6 col-md-3 col-lg">Total TTC: '.htEnTtc($doc['totalTTC'],$tva).' €</div>
                                        <div class="col-12 col-sm-6 col-md-3 col-lg"><span class="badge bg-danger text-white">En attente de paiement</span><a href="'.$urlPaiement.'" class="btn btn-success ml-3 mt-sm-2">Payer</a></div>
                                        <div class="col-12"><button class="btn" data-toggle="collapse" data-target="#collapse'.$accordion.'" aria-expanded="true" aria-controls="collapse'.$accordion.'">Voir les détails <i class="fas fa-chevron-right"></i></button></div>
                                    </div>
                                    <div id="collapse'.$accordion.'" class="collapse" aria-labelledby="heading'.$accordion.'" data-parent="#accordion">
                                    <div class="card-body">';
                                                if(count($donneesDetailsDocumentAchats) > 0){
                                                    echo '<table class="table table-sm table-bordered">
                                                    <thead class="text-center">
                                                        <th>Jeu</th>
                                                        <th>Détails</th>
                                                        <th>Qté</th>
                                                        <th>Prix TTC</th>
                                                    </thead>
                                                    <tbody>';
                                                    foreach($donneesDetailsDocumentAchats as $detail){
                                                            //on recupere tout de la boite de jeu
                                                            $sqlJeuxCatalogue = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = ".$detail['idCatalogue']);
                                                            $donneesJeuxCatalogue = $sqlJeuxCatalogue -> fetch();
                                                            echo '<tr>
                                                                <td class="align-middle">'.$donneesJeuxCatalogue['nom'].'<br/>'.$donneesJeuxCatalogue['editeur'].'<br/>'.$donneesJeuxCatalogue['annee'].'</td>
                                                                <td class="align-middle"><p>Jeu complet:</p>'.$detail['detailsComplet'].'</td>
                                                                <td class="align-middle text-center">'.$detail['qte'].'</td>
                                                                <td class="align-middle text-right">'.htEnTtc($detail['prix'],$tva).'</td>
                                                            
                                                            </tr>';
                                                    }
                                                    echo '</tbody></table>';
                                                }
                                                if(count($donneesDetailsDocumentDemandes) > 0){
                                                    echo '<table class="table table-sm table-bordered">
                                                    <thead class="text-center">
                                                        <th>Jeu</th>
                                                        <th>Demande</th>
                                                        <th>Prix TTC</th>
                                                    </thead>
                                                    <tbody>';
                                                    foreach($donneesDetailsDocumentDemandes as $detail){
                                                            //on recupere tout de la boite de jeu
                                                            $sqlJeuxCatalogue = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = ".$detail['idJeu']);
                                                            $donneesJeuxCatalogue = $sqlJeuxCatalogue -> fetch();
                                                            echo '<tr>
                                                                <td class="align-middle">'.$donneesJeuxCatalogue['nom'].'<br/>'.$donneesJeuxCatalogue['editeur'].'<br/>'.$donneesJeuxCatalogue['annee'].'</td>
                                                                <td class="align-middle"><p>Votre demande:</p>'.$detail['question'].'</td>
                                                                <td class="align-middle text-right">'.htEnTtc($detail['prix'],$tva).'</td>
                                                            </tr>';
                                                    }
                                                    echo '</tbody></table>';
                                                }
                                            echo '<div class="col-12 p-0 d-flex justify-content-between">
                                                    <span class="col-6 p-0 text-center">Expédition: '.htEnTtc($doc['prix_expedition'],$tva).'</span>
                                                    <span class="col-6 p-0 text-center text-success">Adhésion au service: '.htEnTtc($doc['prix_preparation'],$tva).'</span>
                                                </div>
                                                
                                    </div>
                                    </div>
                                </div>';
                            $accordion++;
                        }
                    }
                }else{
                    echo '<div class="card mb-2">
                    <div class="card-header text-center">
                        Rien pour le moment...
                    </div>
                </div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include_once("../../commun/bas_de_page-admin.php");?>