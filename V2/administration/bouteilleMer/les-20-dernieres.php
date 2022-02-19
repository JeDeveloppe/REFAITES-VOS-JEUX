<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");

include('../../config.php');

include('../../bdd/connexion-bdd.php');

$titreDeLaPage = "[ADMIN] - Bouteilles à la mer";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
?>

<div class="container-fluid mt-4">
    <div class="col-12 h2 text-center"><i class="fas fa-wine-bottle text-success"></i> LES BOUTEILLES A LA MER </div>
        <nav class="nav mt-5 border-bottom border-2 border-primary">
            <a class="nav-link active" href="/admin/bouteille-a-la-mer/">Liste des bouteilles</a>
            <a class="nav-link" href="/admin/bouteille-a-la-mer/les-20-dernieres/">Les 20 dernières reçues</a>
        </nav>

        <div class="row mt-5">
            <div class="col-12 table-responsive">
                <table class="table table-striped table-sm mx-auto text-center">
                    <thead>
                        <th>#</th>
                        <th>Nom du jeu</th>
                        <th>Éditeur</th>
                        <th>Année</th>
                        <th>Date de la bouteille</th>
                    </thead>
                    <tbody>                    
                    <?php
        
                            $searchAlphabet = $bdd-> query("SELECT * FROM bouteille_mer WHERE actif = 0 ORDER BY time DESC LIMIT 20");
                            $donneesAlphabet = $searchAlphabet-> fetchAll();
                            $countAlphabet = $searchAlphabet-> rowCount();

                            foreach($donneesAlphabet as $donnees){

                                $relanceBouteille = '<i class="fas fa-clock" data-html="true" data-toggle="tooltip" data-placement="top" title="'.date("d.m.Y",$donnees['end_time']).'"></i>';
 
                                echo '<tr>
                                        <td></td>
                                        <td class="text-left align-middle">'.$donnees['nom'].'</td>
                                        <td class="text-center align-middle">'.$donnees['editeur'].'</td>
                                        <td class="text-center align-middle">'.$donnees['annee'].'</td>
                                        <td class="text-center align-middle">Lancée le '.date("d.m.Y",$donnees['time']).' par <br/>'.$donnees['email'].'</td>
                                </tr>';
                            }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>

<?php include_once("../../commun/bas_de_page-admin.php");?>