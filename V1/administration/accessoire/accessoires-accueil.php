<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$titreDeLaPage = "[ADMIN] - Gestion des catégories";
$descriptionPage = "";

$sqlCategories = $bdd -> query("SELECT * FROM categories");
$donneesCategories = $sqlCategories-> fetchAll();

include('../../commun/haut_de_page.php');
include('../../commun/alertMessage.php');
?>
<div class="container">
    <h2 class="col-12 mt-4 text-center">Gestion des accessoires</h2>
    <div class="col-12 text-right"><a href="/admin/accessoire/new/" class="btn btn-info">+ Nouvel accessoire</a></div>
    <div class="col-12">
        <div class="input-group col-12">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">Voir les accessoires de la catégorie:</span>
            </div>
            <select name="recherche" id="recherche" onChange="search()" required>
                <option value="">...</option>
                <?php
                    foreach($donneesCategories as $categorie){
                        echo '<option value="'.$categorie['idCategorie'].'">'.$categorie['nom'].'</option>';
                    }
                ?>
            </select>
        </div>
    </div>
    <!-- affichage du resultat de la requete ajax -->
    <div id="resultat" class="row mt-4"></div>

</div>
<script>
    function search(){

        let recherche = document.getElementById("recherche").value;

            if(recherche.length > 0){
            $.ajax({
                url: "/administration/accessoire/requeteAjax-accessoire.php",
                method: "post",
                data: "categorie="+recherche,
                dataType: "html",
                success: function(datas){
                    document.getElementById("resultat").innerHTML = datas;
                }
            })
            }
    }
</script>

<?php
include_once("../../commun/bas_de_page-admin.php");
?>