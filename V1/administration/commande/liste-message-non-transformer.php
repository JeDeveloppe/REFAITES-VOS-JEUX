<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
$titreDeLaPage = "[ADMIN] - Liste des messages en attente...";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");

$sqlRequete = $bdd -> prepare("SELECT * FROM listeMessages WHERE statut = ? ORDER BY time DESC");
$sqlRequete-> execute(array(0));
$donneesRequete = $sqlRequete-> fetch();
$count = $sqlRequete-> rowCount();
?>
<div class="container mt-5">
  <!-- <div class="text-center display-3">Aucun test en cours...</div> -->
  <div class="h4">Liste des messages non transformés...</div>
    <table class="table-sm table-responsive table-striped">
        <thead>
        <th>Jeu</th>
        <th>Date de saisie</th>
        </thead>
        <tbody>
        <?php
        while($donneesRequete){
            echo '<tr><td>'.$donneesRequete['idJeu'].'</td><td>'.date("d-m-Y",$donneesRequete['time']).'</td></tr>';
            $donneesRequete = $sqlRequete-> fetch();
        }

        ?>
        </tbody>
    </table>
</div>
<?php include_once("../../commun/bas_de_page-admin.php");?>