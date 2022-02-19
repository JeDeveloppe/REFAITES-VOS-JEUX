<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');

$sqlHistorique = $bdd->query("SELECT * FROM historique ORDER BY date DESC");
$donneesHistorique = $sqlHistorique->fetchAll();



$MAX_SIZE_FILE = $donneesConfig[2]['valeur'] * 1024 * 1024;
                  
    $titreDeLaPage = "[GESTION HISTORIQUE] | ".$GLOBALS['titreDePage'];
    $descriptionPage = "";
    include_once("../../commun/haut_de_page.php");
    include_once("../../commun/alertMessage.php");
    ?>
 
    <div class="container-fluid">
        <h1 class="col-12 text-center mt-5">Gestion historique du service</h1>

        <div class="row mt-4">
            <div class="col-11 mx-auto">
                <table class="table table-sm text-center">
                    <thead>
                        <th>Date</th>
                        <th>Titre</th>
                        <th>Contenu</th>
                        <th>Type d'information</th>
                        <th>En ligne</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php
                            foreach($donneesHistorique as $histoire){
                                if($histoire['actif'] == true){
                                    $online = '<i class="fas fa-circle text-success"></i>';
                                }else{
                                    $online = '<i class="fas fa-circle text-danger"></i>';
                                }

                                foreach ($GLOBALS['medias_presse'] as $iconeMediaPresse){
                                    if($histoire['information'] == $iconeMediaPresse[0]){
                                        $icone = $iconeMediaPresse[2];
                                    }
                                }   
                             
                               
                                echo '<tr>
                                        <td class="align-middle">'.date('d-m-Y à H:m',$histoire['date']).'</td>
                                        <td class="align-middle">'.$histoire['titre'].'</td>
                                        <td class="align-middle">'.$histoire['content'].'</td>
                                        <td class="align-middle">'.$icone.'</td>
                                        <td class="align-middle">'.$online.'</td>
                                        <td><a class="btn btn-info" href="/admin/historique/'.$histoire['idHistorique'].'/edition/">Editer</a></td>
                                    </tr>';
                            }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>              
    </div>
<?php
include_once("../../commun/bas_de_page-admin.php");
?>