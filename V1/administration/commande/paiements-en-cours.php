<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
$titreDeLaPage = "[ADMIN] - Tableau de bord";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");

$sqlRequete = $bdd -> prepare("SELECT * FROM documents WHERE page_controle = ? AND etat != 0 ORDER BY time_transaction DESC");
$sqlRequete-> execute(array("EN_COURS"));
$donneesRequete = $sqlRequete-> fetch();
$count = $sqlRequete-> rowCount();
?>
<div class="container mt-5">
    <div class="card p-0">
        <div class="card-header bg-dark text-white">LES PAIEMENTS EN COURS...</div>
            <div class="card-body table-responsive">
                <div class="col-12 text-center"><?php echo $count; ?> résultat(s)</div>
                <table class="table table-sm table-striped mt-4">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th scope="col">N° Devis</th>
                            <th scope="col">Début le <i class="fas fa-info-circle text-info" data-html="true" data-toggle="tooltip" data-placement="right" title="pay_ : PAYPLUG<br/>tre : PAYGREEN"></i></th>
                            <th scope="col">Client</th>
                            <th scope="col">Expédition / retrait</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        while($donneesRequete){
                            $sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = :user");
                            $sqlClient-> execute(array("user" => $donneesRequete['idUser']));
                            $donneesClient = $sqlClient-> fetch();
                            if($donneesRequete['page_controle'] == ""){
                                $pageUpdateDocumentMdC = " (vide)";
                            }else{
                                $pageUpdateDocumentMdC = " (".$donneesRequete['page_controle'].")";
                            }
                            ?>
                            <tr>

                                <td class="text-center align-middle"><?php echo $donneesRequete['numero_devis'];?></td>
                                <td class="text-center align-middle"><?php echo date('d.m.Y',$donneesRequete['time_transaction'])." à ".date('G:i',$donneesRequete['time_transaction']).'<br/>Num. transaction:<br/>'.$donneesRequete['num_transaction']; ?></td>
                                <td class="text-center align-middle"><?php echo $donneesClient['nom'].' '.$donneesClient['prenom'].'<br/>'.$donneesClient['adresse'].'<br/>'.$donneesClient['cp'].' '.$donneesClient['ville'].'<br/>'.$donneesClient['telephone'].' - '.$donneesClient['email'];?></td>
                                <td class="text-center align-middle"><?php echo $donneesRequete['expedition'];?></td>
                                <td class="text-center align-middle">
                                <a href="/admin/devis/delete/<?php echo $donneesRequete['numero_devis'];?>" class="btn btn-info">Voir</a>  
                                </td>
                            </tr>
                        <?php
                        $donneesRequete = $sqlRequete-> fetch();
                        }
                        ?>
                    </tbody>
                </table>
            </div>
    </div>
</div>
<?php include_once("../../commun/bas_de_page-admin.php");?>