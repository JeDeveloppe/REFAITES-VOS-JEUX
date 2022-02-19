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

$sqlDemandes = $bdd -> prepare("SELECT DISTINCT idUser FROM listeMessages WHERE statut = :statut");
$sqlDemandes-> execute(array("statut" => 1));

$sqlDemandes = $bdd -> prepare("SELECT * FROM listeMessages WHERE statut = ? GROUP BY idUser ORDER BY time ASC");
$sqlDemandes-> execute(array(1));
$donneesDemandes = $sqlDemandes->fetch();
$countDemandes = $sqlDemandes -> rowCount();

$sqlDevis = $bdd -> prepare("SELECT * FROM documents WHERE numero_devis LIKE :numero AND end_validation < :timeActuelle AND etat = :etat"); //1 = DISPO A LA MODIFICATION
$sqlDevis-> execute(array("numero" => $donneesConfig[7]['valeur']."%", "timeActuelle" => time(), "etat" => 1));
$donneesDevis = $sqlDevis->fetch();
$countDevis = $sqlDevis -> rowCount();

$sqlDevisSupUtilisateur = $bdd -> prepare("SELECT * FROM documents WHERE numero_devis LIKE :numero AND etat = :etat"); //0 = REFUSER PAR UTILISATEUR
$sqlDevisSupUtilisateur-> execute(array("numero" => $donneesConfig[7]['valeur']."%", "etat" => 0));
$donneesDevisSupUtilisateur = $sqlDevisSupUtilisateur->fetch();
$countDevisSupUtilisateur = $sqlDevisSupUtilisateur -> rowCount();
?>

<div class="container mt-4 p-0">
    <div class="col-12 h2 text-center">DEMANDES / DEVIS </div>
        <div class="row d-flex justify-content-around">
            <div class="col-12">
                <?php
                //si y a plus rien dans la table on retourne au catalogue
                if($countDemandes < 1){
                    echo '<div class="card mx-auto p-0">
                                <div class="card-header bg-dark text-white">Les demandes...</div>
                                <div class="card-body text-center align-middle"><i class="fas fa-angry text-danger"></i> Aucune à traiter !</div>
                            </div>';
                }else{
                ?>
                <div class="card p-0">
                    <div class="card-header bg-dark text-white">Les devis à faire...</div>
                        <div class="card-body table-responsive">
                            <div class="col-12 text-center"><?php echo $countDemandes;?> demande(s)</div>
                            <table class="bg-success table table-sm table-striped mt-2">
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
                                        $sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idUser = :user");
                                        $sqlClient-> execute(array("user" => $donneesDemandes['idUser']));
                                        $donneesClient = $sqlClient-> fetch();
                                        ?>
                                        <tr>
                                            <td class="text-center align-middle"><?php echo $donneesClient['idClient'];?></td>
                                            <td class="text-left align-middle"><?php echo $donneesClient['nom']." ".$donneesClient['prenom']." ".$donneesClient['adresse']." ".$donneesClient['cp']." ".$donneesClient['ville']."<br/>".$donneesClient['telephone']." - ".$donneesClient['email']; ?></td>
                                            <td class="text-center align-middle"><?php echo $donneesClient['pays'];?></td>
                                            <td class="text-center align-middle"><?php echo date("d-m-Y",$donneesDemandes['time'])." à ".date("G:i",$donneesDemandes['time']);?></td>
                                            <td class="text-right align-middle"><a href="/admin/demande/creation-devis/<?php echo $donneesClient['idClient'];?>/" class="btn btn-info">Commencer un devis</a></td>
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
                                while($donneesDevis){
                                        $sqlClientDevis = $bdd -> query("SELECT * FROM clients WHERE idClient = ".$donneesDevis['idUser']);
                                        $donneesClientDevis = $sqlClientDevis-> fetch();
                                        ?>
                                        <tr>
                                            <td class="text-left align-middle"><?php echo $donneesClientDevis['nom'].' '.$donneesClientDevis['prenom'].'<br/>'.$donneesClientDevis['adresse'].'<br/>'.$donneesClientDevis['cp'].' '.$donneesClientDevis['ville'].' '.$donneesClientDevis['pays']; ?></td>
                                            <td class="text-center align-middle"><?php echo $donneesDevis['numero_devis'].'<br/>Fait le '.date("d-m-Y",$donneesDevis['time']).'<br/>à '.date("G:i",$donneesDevis['time']); ?></td>
                                            <td class="text-center align-middle"><?php if($donneesDevis['relance_devis'] != 0){echo "OUI le<br/>".date("d-m-Y",$donneesDevis['time_mail_devis']).'<br/>à '.date("G:i",$donneesDevis['time_mail_devis']);}else{echo "NON";} ?></td>
                                            <td class="text-center align-middle"><?php echo "Jusqu' au<br/>".date("d-m-Y",$donneesDevis['end_validation']).'<br/>à '.date("G:i",$donneesDevis['end_validation']); ?></td>
                                            <td class="text-center align-middle"><?php if($donneesDevis['transaction'] != ""){echo "OUI";}else{echo "NON";}?></td>
                                            <td class="text-center align-middle">
                                            <a href="/admin/devis/delete/<?php echo $donneesDevis['numero_devis'];?>" class="btn btn-danger">Voir <br/><small>suppression</small></a>
                                            <a href="/admin/devis/edition/<?php echo $donneesDevis['numero_devis'];?>" class="btn btn-info">Voir <br/><small>modification</small></a>   
                                            <?php
                                            if($donneesDevis['relance_devis'] == 0){
                                                echo '<a href="/administration/devis/ctrl/ctrl-envoi-devis-mail.php?devis='.$donneesDevis['numero_devis'].'&relance=ok" class="btn btn-warning">Relancer</a>';
                                            }
                                            ?>                                        
                                            </td>
                                        </tr>
                                    <?php
                                $donneesDevis = $sqlDevis->fetch();
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
                    echo '<div class="card  mx-auto p-0">
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
                                while($donneesDevisSupUtilisateur){
                                        $sqlClientDevisSuppUtilisateur = $bdd -> query("SELECT * FROM clients WHERE idClient = ".$donneesDevisSupUtilisateur['idUser']);
                                        $donneesClientDevisSuppUtilisateur = $sqlClientDevisSuppUtilisateur-> fetch();
                                        ?>
                                        <tr>
                                            <td class="text-left align-middle"><?php echo $donneesDevisSupUtilisateur['numero_devis'].'<br/>Fait le '.date("d-m-Y",$donneesDevisSupUtilisateur['time']).'<br/>à '.date("G:i",$donneesDevisSupUtilisateur['time']); ?></td>
                                            <td class="text-center align-middle"><?php echo date("d-m-Y",$donneesDevisSupUtilisateur['time_transaction']).'<br/>à '.date("G:i",$donneesDevisSupUtilisateur['time_transaction']);?></td>
                                            <td class="text-center align-middle"><?php echo $donneesClientDevisSuppUtilisateur['nom'].' '.$donneesClientDevisSuppUtilisateur['prenom'].'<br/>'.$donneesClientDevisSuppUtilisateur['adresse'].'<br/>'.$donneesClientDevisSuppUtilisateur['cp'].' '.$donneesClientDevisSuppUtilisateur['ville'].' '.$donneesClientDevisSuppUtilisateur['pays']; ?></td>
                                            <td class="text-center align-middle">
                                            <a href="/admin/devis/delete/<?php echo $donneesDevisSupUtilisateur['numero_devis'];?>" class="btn btn-danger">Voir <br/><small>suppression</small></a>                                      
                                            </td>
                                        </tr>
                                    <?php
                                $donneesDevisSupUtilisateur = $sqlDevisSupUtilisateur->fetch();
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