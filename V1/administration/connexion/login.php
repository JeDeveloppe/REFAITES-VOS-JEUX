<?php
@session_start ();
//SI ON EST DEJA IDENTIFIE
if(isset($_SESSION['levelUser'])){
    $_SESSION['alertMessage'] = "Déja connecté(e) !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /accueil/");
    exit();  
}
include_once("../../config.php");
$titreDeLaPage = "Connexion | ".$GLOBALS['titreDePage'];
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");
?>
<div class="container-fluid">
    <div class="row mt-5">
        <div class="col-xl-6 mx-auto">
            <div class="card col p-0 mt-3 m-1">
                <div class="card-header bg-dark text-white"><i class="fas fa-sign-in-alt"></i> Connexion [ADMIN]</div>
                <div class="card-body">
                    <p class="card-text">
                        <form class="form-signin" action="/administration/connexion/ctrl/ctrl-connexion.php" method="get">
                            <div class="form-group">
                                <input type="email" class="form-control" id="exampleInputEmail1" name="mail" aria-describedby="emailHelp" placeholder="Votre adresse mail..." require>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="exampleInputPassword" name="passwordUser" aria-describedby="passwordHelp" placeholder="Votre mot de passe..." require>
                            </div>
                            <button class="btn btn-primary" type="submit">Connexion</button>
                        </form>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once("../../commun/bas_de_page.php");
?>
