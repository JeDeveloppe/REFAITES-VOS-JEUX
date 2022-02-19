<?php
@session_start (); 
include_once("../config.php");
$titreDeLaPage = "Confirmation | ".$GLOBALS['titreDePage'];
$descriptionPage = "";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
?>
<div class="container-fluid">
    <div class="row mt-5 mb-5">
        <div class="card p-0 col-11 col-md-8 col-lg-6 mt-4 mb-4 mx-auto">
            <div class="card-header bg-dark text-white text-center">✅ Confirmation</div>
            <div class="card-body">
                <div class="card-text col text-center align-middle">
                    <p>Un email de confirmation vient de vous être envoyé.<br/>Il est possible qu'il arrive dans vos spams...</p>
                    <p>Pour éviter cela, vous pouvez mettre cette adresse dans vos contacts:<br/><br/><?php echo $GLOBALS['compteMailCommande'];?><br/>😃</p>
                </div>
                <div class="card-text col text-center mt-4"><a href="/" class="btn btn-info">Retour à l'accueil !</a></div>
            </div>
        </div>
    </div>
</div>
<?php
include_once("../commun/bas_de_page.php");
?>
