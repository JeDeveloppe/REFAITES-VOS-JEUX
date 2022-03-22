<?php
@session_start ();
require("../controles/fonctions/adminOnline.php");
include('../config.php');
include('../bdd/connexion-bdd.php');
include('../bdd/table_config.php');
$titreDeLaPage = "[ADMIN] - Accueil";
$descriptionPage = "";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");

// $sqlDemandes = $bdd -> prepare("SELECT DISTINCT idUser FROM listeMessages WHERE statut = :statut");
// $sqlDemandes-> execute(array("statut" => 1));

$sqlDemandes = $bdd -> prepare("SELECT * FROM listeMessages WHERE statut = ? GROUP BY panierKey ORDER BY time ASC");
$sqlDemandes-> execute(array(1));
$donneesDemandes = $sqlDemandes->fetch();
$countDemandes = $sqlDemandes->rowCount();

$sqlDevis = $bdd -> prepare("SELECT * FROM documents WHERE numero_devis LIKE :numero AND end_validation < :timeActuelle AND etat = :etat"); //1 = DISPO A LA MODIFICATION
$sqlDevis-> execute(array("numero" => $donneesConfig[7]['valeur']."%", "timeActuelle" => time(), "etat" => 1));
$donneesDevis = $sqlDevis->fetchAll();
$countDevis = $sqlDevis -> rowCount();

$sqlDevisSupUtilisateur = $bdd -> prepare("SELECT * FROM documents WHERE numero_devis LIKE :numero AND etat = :etat"); //0 = REFUSER PAR UTILISATEUR
$sqlDevisSupUtilisateur-> execute(array("numero" => $donneesConfig[7]['valeur']."%", "etat" => 0));
$donneesDevisSupUtilisateur = $sqlDevisSupUtilisateur->fetchAll();
$countDevisSupUtilisateur = $sqlDevisSupUtilisateur->rowCount();
?>

<div class="container-fluid mt-4">
    <div class="col-12 h2 text-center">DEMANDES / DEVIS </div>
        <div class="row">
            <div class="col-12 mt-4">
                <?php
                //si y a plus rien dans la table on retourne au catalogue
                if($countDemandes < 1){
                    echo '<div class="card p-0">
                                <div class="card-header bg-dark text-white">Les demandes...</div>
                                <div class="card-body text-center align-middle"><i class="fas fa-angry text-danger"></i> Aucune à traiter !</div>
                            </div>';
                }else{
                ?>
                <div class="card p-0">
                    <div class="card-header bg-dark text-white">Les devis à faire...</div>
                        <div class="card-body table-responsive">
                            <div class="col-12 text-center"><?php echo $countDemandes;?> demande(s)</div>
                            <table class="table table-sm table-striped mt-2">
                                <thead class="thead-dark text-center">
                                    <tr>
                                        <th scope="col">IdClient</th>
                                        <th scope="col">Demande de devis</th>
                                        <th scope="col">Pays</th>
                                        <th scope="col">Date de la demande</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    while($donneesDemandes){
                                        $sqlClient = $bdd->prepare("SELECT * FROM clients WHERE idUser = ?");
                                        $sqlClient-> execute(array($donneesDemandes['idUser']));
                                        $donneesClient = $sqlClient->fetch();
                                      
                                        ?>
                                        <tr>
                                            <td class="text-center align-middle"><?php echo $donneesClient['idClient'];?></td>
                                            <td class="text-left align-middle"><?php echo $donneesClient['nomFacturation']." ".$donneesClient['prenomFacturation']." ".$donneesClient['adresseFacturation']." ".$donneesClient['cpFacturation']." ".$donneesClient['villeFacturation']."<br/>".$donneesClient['email']; ?></td>
                                            <td class="text-center align-middle"><?php echo $donneesClient['paysFacturation'];?></td>
                                            <td class="text-center align-middle"><?php echo date("d-m-Y",$donneesDemandes['time'])." à ".date("G:i",$donneesDemandes['time']);?></td>
                                            <td class="text-right align-middle"><a href="/admin/demande/creation-devis/<?php echo $donneesClient['idClient'];?>/<?php echo $donneesDemandes['panierKey'];?>" class="btn btn-info">Commencer un devis</a></td>
                                        </tr>
                                    <?php
                                    $donneesDemandes = $sqlDemandes->fetch();
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                </div>
                <?php
                }//fin du if count <1
                ?>
            </div>
            <div class="col-12 mt-4">
                <?php
                //si y a plus rien dans la table on retourne au catalogue
                if($countDevis < 1){
                    echo '<div class="card mx-auto p-0">
                                    <div class="card-header bg-dark text-white">Les devis à supprimer...</div>
                                    <div class="card-body text-center align-middle"><i class="far fa-smile-beam text-success"></i> Aucun !</div>
                                </div>';
                }else{
                ?>
                <div class="card p-0">
                    <div class="card-header bg-dark text-white">Les devis à supprimer... ( > à <?php echo $donneesConfig[11]['valeur']/ 86400;?> jours ) </div>
                        <div class="card-body table-responsive">
                            <div class="col-12 text-center"><?php echo $countDevis;?> résultat(s)</div>
                            <table class="table table-sm table-striped mt-2">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th scope="col">Client</th>
                                    <th scope="col">Numéro du devis</th>
                                    <th scope="col">Relancé</th>
                                    <th scope="col">Validitée</th>
                                    <th scope="col">Paiement commencé</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach($donneesDevis as $devis){
                                        $sqlClientDevis = $bdd->prepare("SELECT * FROM clients WHERE idClient = ?");
                                        $sqlClientDevis->execute(array($devis['idUser']));
                                        $donneesClientDevis = $sqlClientDevis->fetch();
                                        
                                        ?>
                                        <tr>
                                            <td class="text-left align-middle"><?php echo $donneesClientDevis['nomFacturation'].' '.$donneesClientDevis['prenomFacturation'].'<br/>'.$donneesClientDevis['adresseFacturation'].'<br/>'.$donneesClientDevis['cpFacturation'].' '.$donneesClientDevis['villeFacturation'].' '.$donneesClientDevis['paysFacturation']; ?></td>
                                            <td class="text-center align-middle"><?php echo $devis['numero_devis'].'<br/>Fait le '.date("d-m-Y",$devis['time']).'<br/>à '.date("G:i",$devis['time']); ?></td>
                                            <td class="text-center align-middle"><?php if($devis['relance_devis'] != 0){echo "OUI le<br/>".date("d-m-Y",$devis['time_mail_devis']).'<br/>à '.date("G:i",$devis['time_mail_devis']);}else{echo "NON";} ?></td>
                                            <td class="text-center align-middle"><?php echo "Jusqu' au<br/>".date("d-m-Y",$devis['end_validation']).'<br/>à '.date("G:i",$devis['end_validation']); ?></td>
                                            <td class="text-center align-middle"><?php if($devis['time_transaction'] != ""){echo "OUI";}else{echo "NON";}?></td>
                                            <td class="text-center align-middle">
                                                <div class="btn-group" role="group">
                                                    <a href="/admin/devis/delete/<?php echo $devis['numero_devis'];?>" class="btn btn-danger">Voir<br/><small>suppression</small></a>
                                                    <a href="/admin/devis/edition/<?php echo $devis['numero_devis'];?>" class="btn btn-info">Voir<br/><small>modification</small></a>   
                                                    <?php
                                                        if($devis['relance_devis'] == 0){
                                                            echo '<a href="/administration/devis/ctrl/ctrl-envoi-devis-mail.php?devis='.$devis['numero_devis'].'&relance=ok" class="btn btn-warning">Relancer<br/><small>le devis</small></a>';
                                                        }
                                                    ?>   
                                                </div>                                     
                                            </td>
                                        </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                }//fin du if count <1
                ?>               
            </div>
            <div class="col-12 mt-4">
                <?php
                //si y a plus rien dans la table on retourne au catalogue
                if($countDevisSupUtilisateur < 1){
                    echo '<div class="card mx-auto p-0">
                                    <div class="card-header bg-dark text-white">Les devis supprimés par l\'utilisateur lui même...</div>
                                    <div class="card-body text-center align-middle"><i class="far fa-smile-beam text-success"></i> Aucun !</div>
                                </div>';
                }else{
                ?>
                <div class="card p-0">
                    <div class="card-header bg-dark text-white">Les devis supprimés par l'utilisateur lui même...</div>
                        <div class="card-body table-responsive">
                            <table class="table table-sm table-striped mt-4">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th scope="col">Numéro du devis</th>
                                    <th scope="col">Supprimé le</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach($donneesDevisSupUtilisateur as $devisSup){
                                        $sqlClientDevisSuppUtilisateur = $bdd -> query("SELECT * FROM clients WHERE idClient = ".$devisSup['idUser']);
                                        $donneesClientDevisSuppUtilisateur = $sqlClientDevisSuppUtilisateur-> fetch();
                                        ?>
                                        <tr>
                                            <td class="text-left align-middle"><?php echo $devisSup['numero_devis'].'<br/>Fait le '.date("d-m-Y",$devisSup['time']).' à '.date("G:i",$devisSup['time']); ?></td>
                                            <td class="text-center align-middle"><?php echo date("d-m-Y",$devisSup['time_transaction']).'<br/>à '.date("G:i",$devisSup['time_transaction']);?></td>
                                            <td class="text-center align-middle"><?php echo $donneesClientDevisSuppUtilisateur['nomFacturation'].' '.$donneesClientDevisSuppUtilisateur['prenomFacturation'].'<br/>'.$donneesClientDevisSuppUtilisateur['adresseFacturation'].'<br/>'.$donneesClientDevisSuppUtilisateur['cpFacturation'].' '.$donneesClientDevisSuppUtilisateur['villeFacturation'].' - '.$donneesClientDevisSuppUtilisateur['paysFacturation']; ?></td>
                                            <td class="text-center align-middle">
                                            <a href="/admin/devis/delete/<?php echo $devisSup['numero_devis'];?>" class="btn btn-danger">Voir <br/><small>suppression</small></a>                                      
                                            </td>
                                        </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                }//fin du if count <1
                ?>               
            </div>
        </div>
</div>

<?php include_once("../commun/bas_de_page-admin.php");?>