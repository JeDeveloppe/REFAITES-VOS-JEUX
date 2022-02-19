<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
$titreDeLaPage = "[ADMIN] - Les Commandes à préparer !";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");

//LES COMMANDES A ENVOYER
$sqlCommandesAenvoyer = $bdd -> prepare("SELECT * FROM documents WHERE etat = ? AND expedition NOT LIKE ? AND envoyer = ? ORDER BY time_transaction"); //etat 2 = payer
$sqlCommandesAenvoyer-> execute(array(2,"%retrait%",0));
$donneesCommandesAenvoyer = $sqlCommandesAenvoyer->fetch();
$countCommandesAenvoyer = $sqlCommandesAenvoyer -> rowCount();
//LES COMMANDES AVEC RETRAIT
$sqlCommandesApreparer = $bdd -> prepare("SELECT * FROM documents WHERE etat = ? AND expedition LIKE ? AND envoyer = ? ORDER BY time_transaction"); //etat 2 = payer
$sqlCommandesApreparer-> execute(array(2,"%retrait%",0));
$donneesCommandesApreparer = $sqlCommandesApreparer->fetch();
$countCommandesApreparer = $sqlCommandesApreparer -> rowCount();
//LES COMMANDES MISES DE COTE
$sqlCommandesMiseDeCote = $bdd -> prepare("SELECT * FROM documents WHERE etat = ? AND envoyer = ? ORDER BY time_transaction DESC"); //etat 2 = payer
$sqlCommandesMiseDeCote-> execute(array(3,0));
$donneesCommandesMiseDeCote = $sqlCommandesMiseDeCote->fetch();
$countCommandesMiseDeCote = $sqlCommandesMiseDeCote -> rowCount();
?>

<div class="container p-0 mt-4">
    <div class="col h2 text-center">Commandes à traiter</div>
        <div class="row d-flex justify-content-around p-2">
            <!-- COMMANDE A ENVOYER -->
            <div class="col-12 mt-4">
                <?php
                //si y a plus rien dans la table on retourne au catalogue
                if($countCommandesAenvoyer < 1){
                    echo '<div class="card col mx-auto p-0">
                                <div class="card-header bg-dark text-white">Les Commandes à envoyer...</div>
                                <div class="card-body text-center align-middle"><i class="fas fa-angry text-danger"></i> Aucune à traiter !</div>
                            </div>';
                }else{
                ?>
                <div class="card p-0">
                    <div class="card-header bg-dark text-white">LES COMMANDES A ENVOYER...</div>
                        <div class="card-body table-responsive">
                        <div class="col-12 h5 text-center p-2"><?php echo $countCommandesAenvoyer;?> résultats</div>
                            <table class="table table-sm table-striped mt-4">
                                <thead class="thead-dark text-center">
                                    <tr>
                                        <th scope="col">N° Devis</th>
                                        <th scope="col">Payée le <i class="fas fa-info-circle text-info" data-html="true" data-toggle="tooltip" data-placement="right" title="pay_ : PAYPLUG<br/>tre : PAYGREEN"></i></th>
                                        <th scope="col">Client</th>
                                        <th scope="col">Pays</th>
                                        <th scope="col">Méthode d'envoi</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    while($donneesCommandesAenvoyer){
                                        $sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = :user");
                                        $sqlClient-> execute(array("user" => $donneesCommandesAenvoyer['idUser']));
                                        $donneesClient = $sqlClient-> fetch();
                                        if($donneesCommandesAenvoyer['page_controle'] == ""){
                                            $pageUpdateDocumentCaE = " (vide)";
                                        }else{
                                            $pageUpdateDocumentCaE = " (".$donneesCommandesAenvoyer['page_controle'].")";
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-center align-middle"><?php echo $donneesCommandesAenvoyer['numero_devis'];?></td>
                                            <td><?php echo date('d.m.Y',$donneesCommandesAenvoyer['time_transaction'])." à ".date('G:i',$donneesCommandesAenvoyer['time_transaction'])." par <span data-html='true' data-toggle='tooltip' data-placement='right' title='".$pageUpdateDocumentCaE."'>".$donneesCommandesAenvoyer['moyen_paiement'].'</span><br/>Num. transaction:<br/>'.$donneesCommandesAenvoyer['num_transaction'].'<br/>'.$donneesCommandesAenvoyer['numero_facture']; ?></td>
                                            <td class="text-center align-middle"><?php echo $donneesClient['nom'].' '.$donneesClient['prenom'].'<br/>'.$donneesClient['adresse'].'<br/>'.$donneesClient['cp'].' '.$donneesClient['ville'].'<br />'.$donneesClient['telephone'].' - '.$donneesClient['email'];?></td>
                                            <td class="text-center align-middle"><?php echo $donneesClient['pays'];?></td>
                                            <td class="text-center align-middle"><?php echo $donneesCommandesAenvoyer['expedition'];?></td>
                                            <td class="text-center align-middle">
                                                <a href="/administration/commande/ctrl/ctrl-mise-en-attente.php?newValue=3&doc=<?php echo $donneesCommandesAenvoyer['idDocument'];?>" class="btn btn-info">Mettre en attente !</a><br/>
                                                <?php if ($donneesCommandesAenvoyer['expedition'] == "colissimo"){
                                                    echo '<button class="btn btn-warning mt-2" onclick="colissimo('.$donneesCommandesAenvoyer['idDocument'].')">Numéro de colis</button>';
                                                }else{
                                                    echo '<a href="/administration/commande/ctrl/ctrl-envoi-commande-mail-envoi.php?doc='.$donneesCommandesAenvoyer['idDocument'].'" class="btn btn-warning mt-2">Ok envoyé!</a>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                    $donneesCommandesAenvoyer = $sqlCommandesAenvoyer->fetch();
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
            <!-- COMMANDE AVEC RETRAIT -->
            <div class="col-12 mt-4">
                <?php
                //si y a plus rien dans la table on retourne au catalogue
                if($countCommandesApreparer < 1){
                    echo '<div class="card col mx-auto p-0">
                                <div class="card-header bg-dark text-white">Les Commandes avec retrait...</div>
                                <div class="card-body text-center align-middle"><i class="fas fa-angry text-danger"></i> Aucune à traiter !</div>
                            </div>';
                }else{
                ?>
                <div class="card p-0">
                    <div class="card-header bg-dark text-white">LES COMMANDES AVEC RETRAIT...</div>
                        <div class="card-body table-responsive">
                            <table class="table table-sm table-striped mt-4">
                                <thead class="thead-dark text-center">
                                    <tr>
                                        <th scope="col">N° Devis</th>
                                        <th scope="col">Payée le <i class="fas fa-info-circle text-info" data-html="true" data-toggle="tooltip" data-placement="right" title="pay_ : PAYPLUG<br/>tre : PAYGREEN"></i></th>
                                        <th scope="col">Client</th>
                                        <th scope="col">Lieu de retrait</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    while($donneesCommandesApreparer){
                                        $sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = :user");
                                        $sqlClient-> execute(array("user" => $donneesCommandesApreparer['idUser']));
                                        $donneesClient = $sqlClient-> fetch();
                                        if($donneesCommandesApreparer['page_controle'] == ""){
                                            $pageUpdateDocumentCaP = " (vide)";
                                        }else{
                                            $pageUpdateDocumentCaP = " (".$donneesCommandesApreparer['page_controle'].")";
                                        }
                                        ?>
                                        <tr>

                                            <td class="text-center align-middle"><?php echo $donneesCommandesApreparer['numero_devis'];?></td>
                                            <td class="align-middle"><?php echo date('d.m.Y',$donneesCommandesApreparer['time_transaction'])." à ".date('G:i',$donneesCommandesApreparer['time_transaction'])." par <span data-html='true' data-toggle='tooltip' data-placement='right' title='".$pageUpdateDocumentCaE."'>".$donneesCommandesApreparer['moyen_paiement'].'</span><br/>Num. transaction:<br/>'.$donneesCommandesApreparer['num_transaction'].'<br/>'.$donneesCommandesApreparer['numero_facture']; ?></td>
                                            <td class="text-center align-middle"><?php echo $donneesClient['nom'].' '.$donneesClient['prenom'].'<br/>'.$donneesClient['adresse'].'<br/>'.$donneesClient['cp'].' '.$donneesClient['ville'].'<br />'.$donneesClient['telephone'].' - '.$donneesClient['email'];?></td>
                                            <td class="text-center align-middle"><?php echo $donneesCommandesApreparer['expedition'];?></td>
                                            <td class="text-center align-middle"><a href="/administration/commande/ctrl/ctrl-envoi-commande-mail-retrait.php?doc=<?php echo $donneesCommandesApreparer['idDocument'];?>" class="btn btn-warning">Ok déposé!</a></td>
                                        </tr>
                                    <?php
                                    $donneesCommandesApreparer = $sqlCommandesApreparer->fetch();
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
            <!-- COMMANDE MISE DE COTE -->
            <div class="col-12 mt-4">
                <?php
                //si y a plus rien dans la table on retourne au catalogue
                if($countCommandesMiseDeCote < 1){
                    echo '<div class="card col mx-auto p-0">
                                <div class="card-header bg-dark text-white">LES COMMANDES MISENT DE COTE...</div>
                                <div class="card-body text-center align-middle"><i class="fas fa-smile-beam text-success"></i> Aucune en attente !</div>
                            </div>';
                }else{
                ?>
                <div class="card p-0">
                    <div class="card-header bg-dark text-white">LES COMMANDES MISENT DE COTE...</div>
                        <div class="card-body table-responsive">
                            <table class="table table-sm table-striped mt-4">
                                <thead class="thead-dark text-center">
                                    <tr>
                                        <th scope="col">N° Devis</th>
                                        <th scope="col">Payée le <i class="fas fa-info-circle text-info" data-html="true" data-toggle="tooltip" data-placement="right" title="pay_ : PAYPLUG<br/>tre : PAYGREEN"></i></th>
                                        <th scope="col">Client</th>
                                        <th scope="col">Méthode d'envoi</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    while($donneesCommandesMiseDeCote){
                                        $sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = :user");
                                        $sqlClient-> execute(array("user" => $donneesCommandesMiseDeCote['idUser']));
                                        $donneesClient = $sqlClient-> fetch();
                                        if($donneesCommandesMiseDeCote['page_controle'] == ""){
                                            $pageUpdateDocumentMdC = " (vide)";
                                        }else{
                                            $pageUpdateDocumentMdC = " (".$donneesCommandesMiseDeCote['page_controle'].")";
                                        }
                                        ?>
                                        <tr>

                                            <td class="text-center align-middle"><?php echo $donneesCommandesMiseDeCote['numero_devis'];?></td>
                                            <td class="text-center align-middle"><?php echo date('d.m.Y',$donneesCommandesMiseDeCote['time_transaction'])." à ".date('G:i',$donneesCommandesMiseDeCote['time_transaction'])." par <span data-html='true' data-toggle='tooltip' data-placement='right' title='".$pageUpdateDocumentMdC."'>".$donneesCommandesMiseDeCote['moyen_paiement'].'</span><br/>Num. transaction:<br/>'.$donneesCommandesMiseDeCote['num_transaction']; ?></td>
                                            <td class="text-center align-middle"><?php echo $donneesClient['nom'].' '.$donneesClient['prenom'].'<br/>'.$donneesClient['adresse'].'<br/>'.$donneesClient['cp'].' '.$donneesClient['ville'].'<br />'.$donneesClient['telephone'].' - '.$donneesClient['email'];?></td>
                                            <td class="text-center align-middle"><?php echo $donneesCommandesMiseDeCote['expedition'];?></td>
                                            <td class="text-center align-middle">
                                                <a href="/administration/commande/ctrl/ctrl-mise-en-attente.php?newValue=2&doc=<?php echo $donneesCommandesMiseDeCote['idDocument'];?>" class="btn btn-info">Remettre à expédier !</a><br/>
                                                <?php if ($donneesCommandesMiseDeCote['expedition'] == "colissimo"){
                                                    echo '<button class="btn btn-warning p-0 mt-2" onclick="colissimo('.$donneesCommandesMiseDeCote['idDocument'].')">Numéro de colis</button>';
                                                }else{
                                                    echo '<a href="/administration/commande/ctrl/ctrl-envoi-commande-mail-envoi.php?doc='.$donneesCommandesMiseDeCote['idDocument'].'" class="btn btn-warning mt-2">Ok envoyé!</a>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                    $donneesCommandesMiseDeCote = $sqlCommandesMiseDeCote->fetch();
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
<script>
function colissimo(id) {
  var numeroColissimo = prompt("Numéro colissimo ?", "");

  if (numeroColissimo != null) {
    window.location.href = "/administration/commande/ctrl/ctrl-envoi-commande-mail-envoi.php?doc="+id+"&numeroColissimo="+numeroColissimo; 
  }
}
</script>
<?php include_once("../../commun/bas_de_page-admin.php");?>