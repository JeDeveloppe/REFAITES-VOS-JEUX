<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$titreDeLaPage = "[ADMIN] - Gestion des catégories";
$descriptionPage = "";

$sqlCategories = $bdd -> query("SELECT * FROM categories");
$donneesCategories = $sqlCategories-> fetch();

include('../../commun/haut_de_page.php');
include('../../commun/alertMessage.php');
?>
<div class="container">
    <h2 class="col-12 mt-4 text-center">Gestion des catégories</h2>
    <div class="col-12 text-right"><a href="/admin/categories/new/" class="btn btn-info">+ Nouvelle catégorie</a></div>
    <div class="col-12 d-flex">
        <?php 
            while($donneesCategories){
                echo '<div class="card m-1">
                        <div class="card-body">
                                <div class="col-12 text-right mb-2">';
                                    if($donneesCategories['actif'] == 0){
                                        echo 'En ligne: <i class="fas fa-circle text-danger"></i>';
                                    }else{
                                        echo 'En ligne: <i class="fas fa-circle text-success"></i>';
                                    }
                          echo '</div>
                                <div class="col-12">'.$donneesCategories['nom'].'</div>
                                <div class="col-12 text-center mt-2"><a href="/admin/categories/edition/'.$donneesCategories['idCategorie'].'/" class="btn btn-info">Modifier</a></div>
                        </div>
                    </div>';

            $donneesCategories = $sqlCategories-> fetch(); 
            }
        ?>
    </div>


</div>


<?php
include_once("../../commun/bas_de_page-admin.php");
?>