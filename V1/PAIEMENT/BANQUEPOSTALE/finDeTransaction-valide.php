<?php
@session_start (); 
if(!isset($_SESSION['alertMessage'])){
    $_SESSION['alertMessage'] = "Mauvaise route !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /");
    exit();
}else{
include_once("../config.php");
$titreDeLaPage = "Fin de la transaction | ".$GLOBALS['titreDePage'];
$descriptionPage = "";
include_once("../commun/haut_de_page.php");
?>
<div class="container-fluid">
    <div class="row mt-5">
        <div class="card p-0 col-11 col-md-8 col-lg-6 mt-4 mb-4 mx-auto">
            <div class="card-header bg-dark text-white text-center">Confirmation</div>
            <div class="card-body">
                <div class="col-12 text-center align-middle"><i class="fas fa-check-square fa-2x text-success"></i> <?php echo $_SESSION['alertMessage']; ?></div>
                <div class="col-12 text-center">
                    <a href="/accueil/" class="btn btn-primary">Retour à l'accueil !</a>

                    <form method="post" action="/administration/facture/generation-pdf-envoi.php" class="mt-4">
                    <input type="hidden" name="document" value="<?php echo $_SESSION['boutonFacture']; ?>">
                    <button class="btn btn-primary">Je souhaite une facture !</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
  
    

</div>
<?php
}
unset($_SESSION['alertMessage']);
unset($_SESSION['boutonFacture']);
include_once("../commun/bas_de_page.php");
?>
