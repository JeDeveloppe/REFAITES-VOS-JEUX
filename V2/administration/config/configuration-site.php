<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include_once("../../config.php");
$titreDeLaPage = "[ADMIN] - Configuration du site";
$descriptionPage = "";
include_once("../../bdd/connexion-bdd.php");
include_once("../../commun/haut_de_page.php");

//on charge les donnees de la table
include_once("../../bdd/table_config.php");
include_once("../../commun/alertMessage.php");
?>
<div class="container-fluid">
    <div class="col text-center mt-5 h3"><i class="fas fa-exclamation-triangle text-danger"></i> Chaque mauvais changement peut faire planté le site !</div>
    <div class="col-11 mt-3 mx-auto">
        <div class="card p-0 mx-auto mt-3">
            <div class="card-header bg-dark text-white text-center h3">CONFIGURATION DU SITE</div>
            <div class="card-body">
                <form method="post" action="/administration/config/ctrl/ctrl_configuration-site.php">
                    <table class="table table-sm mt-4 col-11 mx-auto text-center">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th scope="col">Donnée pour:</th>
                                <th scope="col">Valeur</th>
                                <th scope="col">Explications / aide / <br/>IMPORTANT A RESPECTER</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            //on parcours le tableau
                                foreach($donneesConfig as $ligne){
                                    echo '
                                    <tr>
                                        <td>'.$ligne['nom'].'</td>
                                        <td><input type="text" name="valeur[]" ';
                                        if(strlen($ligne['attr']) > 0){
                                            echo $ligne['attr'];
                                        }
                                        echo ' value="'.$ligne['valeur'].'" required ><input type="hidden" name="nom[]" value="'.$ligne['nom'].'"></td>
                                        <td class="text-left">'.nl2br($ligne['explications']).'</td>
                                    </tr>';
                                $donneesConfig = $sqlConfig-> fetch();
                                    
                                }
                            ?>
                        </tbody>
                    </table>

                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success border-secondary">Enregistrer</button>
                    </div>
                </form>
            </div> 
        </div>
    </div>
</div>
<?php
    include_once("../../commun/bas_de_page-admin.php");
?>
