<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
$titreDeLaPage = "[ADMIN] - Recherche d'un client'";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
?>
<div class="container-fluid">
    <div class="row mt-2">
        <div class="card col-xl-9 mx-auto p-0">
            <div class="card-header bg-secondary text-white">Recherche d'un client</div>
            <div class="card-body">
                <form method="get" action ="" class="d-flex">
                    <div class="form-group text-center">IdClient, nom ou adresse email:
                        <input type="text" name="recherche" maxlength="30" placeholder="MAX 30 caractères..." required/>
                    </div> 
                    <div class="col text-center mt-1 mb-2">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-info border border-secondary">Chercher</button>
                            <a href="/admin/client/" class="btn btn-warning border border-secondary">Reset</a>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">Résultat de la recherche:</div>
                <div class="card-body p-0">
                    <?php
                    if(!isset($_GET['recherche'])){
                        echo '<div class="card-text text-center h4 p-2"> AUCUNE RECHERCHE...</div>';
                    }else{
                        require("../../controles/fonctions/validation_donnees.php");
                        $recherche = valid_donnees($_GET['recherche']); 

                        //ON RECHERCHE
                            require_once("../../config.php");
                            require_once("../../bdd/connexion-bdd.php");
                            require("../../bdd/table_config.php");

                            $sqlRecherche = $bdd-> query("SELECT * FROM clients WHERE idClient LIKE '%$recherche%' OR nom LIKE '%$recherche%' OR email LIKE '%$recherche%' ");
                            $donneesRecherche = $sqlRecherche-> fetch();
                            $nb = $sqlRecherche -> rowCount();
                            

                            if($nb < 1){
                                echo '<div class="card-text text-center h4 ">AUCUN RESULTAT...</div>';
                            }else{
                                echo '<div class="col-12 h5 text-center p-2">'.$nb.' résultats</div>
                                <table class="table table-sm mt-2 text-center">
                                    <thead class="thead-dark text-center">
                                        <tr>
                                            <th scope="col">IdClient</th>
                                            <th scope="col">Nom</th>
                                            <th scope="col">Prénom</th>
                                            <th scope="col">Adresse</th>
                                            <th scope="col">Code postal</th>
                                            <th scope="col">Ville</th>
                                            <th scope="col">Téléphone</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                while($donneesRecherche){

                                    echo '<tr>
                                            <td>['.$donneesRecherche['idClient'].']</td>
                                            <td>'.$donneesRecherche['nom'].'</td>
                                            <td>'.$donneesRecherche['prenom'].'</td>
                                            <td>'.$donneesRecherche['adresse'].'</td>
                                            <td>'.$donneesRecherche['cp'].'</td>
                                            <td>'.$donneesRecherche['ville'].'</td>
                                            <td>'.$donneesRecherche['telephone'].'</td>
                                            <td>'.$donneesRecherche['email'].'</td>
                                          
                                        
                                            <td>
                                                <a href="/admin/client/edition/'.$donneesRecherche['idClient'].'" class="btn btn-warning">Voir et modifier</a>                                              
                                            </td>
                                          </tr>';
                                    $donneesRecherche = $sqlRecherche-> fetch();
                                }
                                echo '</table>';  
                            }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("../../commun/bas_de_page-admin.php");?>