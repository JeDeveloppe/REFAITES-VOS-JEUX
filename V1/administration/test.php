<?php
@session_start ();
require("../controles/fonctions/adminOnline.php");
include('../config.php');
include('../bdd/connexion-bdd.php');
$titreDeLaPage = "[ADMIN] - Recherche d'un client'";
$descriptionPage = "";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");


?>
<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-12 h2 text-center mt-4">Liste des boites sans Gr de renseigné...</div>
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">Liste des boites sans Gr de renseigné...</div>
                <div class="card-body p-0">
                        <?php
                        //ON RECHERCHE
                            require_once("../config.php");
                            require_once("../bdd/connexion-bdd.php");
                            require("../bdd/table_config.php");

                            $sqlRecherche = $bdd-> query("SELECT * FROM catalogue WHERE poidBoite = 0 OR poidBoite IS NULL ORDER BY nom ASC ");
                            $donneesRecherche = $sqlRecherche-> fetch();
                            $nb = $sqlRecherche -> rowCount();
                            

                            if($nb < 1){
                                echo '<div class="card-text text-center h4 ">AUCUN RESULTAT...</div>';
                            }else{
                                echo '<div class="col-12 h5 text-center p-2">'.$nb.' boites</div>
                                <table class="table table-sm mt-2 text-center">
                                    <thead class="thead-dark text-center">
                                        <tr>
                                            <th scope="col">Nom</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                while($donneesRecherche){

                                    echo '<tr>
                                            <td>'.$donneesRecherche['nom'].'</td>
                                            <td><a class="btn btn-warning" href="/administration/jeu/edition.php?etat=offline&jeu='.$donneesRecherche['idCatalogue'].'" target="_blank">Modifier</a></td>
                                          </tr>';
                                    $donneesRecherche = $sqlRecherche-> fetch();
                                }
                                echo '</table>';  
                            }
                
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("../commun/bas_de_page-admin.php");?>