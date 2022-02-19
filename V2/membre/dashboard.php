<?php
@session_start ();
require_once('../controles/fonctions/memberOnline.php');
include_once("../config.php");
$titreDeLaPage = "Espace membre | ".$GLOBALS['titreDePage'];
$descriptionPage = "Espace membre";
include_once("../bdd/connexion-bdd.php");

$sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idUser = ?");
$sqlClient-> execute(array($_SESSION['sessionId']));
$donneesClient = $sqlClient->fetch();

if(!preg_match('#^14#',$donneesClient['cpLivraison'])){
    $textRetrait = '<span class="text-danger">Trop loin de Caen... !</span>';
}else{
    $textRetrait = '<span class="text-success">Possible sur Caen !</span>';
}
if($donneesClient['isAssociation'] == 0){
    $textAssociation = "NON - ";
    $dateFinAssociation = "Il n'a pas encore eu d'achat sur la version 2...";
}else if($donneesClient['isAssociation'] < time()){
    $textAssociation = "NON - ";
    $dateFinAssociation = "Valable jusqu' au ".date('d.m.Y', $donneesClient['isAssociation']);
}else{
    $textAssociation = "OUI - ";
    $dateFinAssociation = "Valable jusqu' au ".date('d.m.Y', $donneesClient['isAssociation']);
}

if($donneesClient['nomFacturation'] == null){
    $textFacturation = 'Pas encore d\'adresse de saisie !';
}else{
    $textFacturation = '<span class="text-success">Ok saisie !</span>';
}
if($donneesClient['nomLivraison'] == null){
    $textLivraison = 'Pas encore d\'adresse de saisie !';
}else{
    $textLivraison = '<span class="text-success">Ok saisie !</span>';
}


include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
?>

<div class="container-fluid mt-5">

    <?php require_once('./menuMembre.php'); ?>

    <div class="row mt-3">
        <div class="col-11 mx-auto col-lg-9">
            <ul class="mt-5 list-group">
                <li class="list-group-item">Adhésion au service Refaites vos jeux: <?php echo $textAssociation.$dateFinAssociation; ?></li>
                <li class="list-group-item">Adresse de facturation: <?php echo $textFacturation; ?></li>
                <li class="list-group-item">Adresse de livraison: <?php echo $textLivraison; ?></li>
                <li class="list-group-item">Retrait des jeux d'occasion: <?php echo $textRetrait; ?></li>
            </ul>
        </div>
    </div>
</div>
<?php
include_once("../commun/bas_de_page.php");
?>