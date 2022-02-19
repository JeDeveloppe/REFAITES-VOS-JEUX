<?php
@session_start (); 
include_once("../../config.php");
$titreDeLaPage = "Fin de transaction [cancel] | ".$GLOBALS['titreDePage'];
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");
?>
<div class="container">
    <div class="row mt-5 mb-5">
        <div class="card p-0 col-11 col-md-8 col-lg-6 mt-4 mb-4 mx-auto">
            <div class="card-header bg-dark text-white text-center">Fin de transaction</div>
            <div class="card-body">
                <div class="card-text col text-center align-middle"><i class="fas fa-exclamation-triangle fa-2x text-danger"></i> <p>Erreur dans la transaction ou annulation de votre part !</p></div>
                <div class="card-text col text-center mt-4"><a href="/accueil/" class="btn btn-info">Retour à l'accueil !</a></div>
            </div>
        </div>
    </div>
</div>
<?php
include_once("../../commun/bas_de_page.php");
?>
