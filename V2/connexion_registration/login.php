<?php
@session_start ();
//SI ON EST DEJA IDENTIFIE
if(isset($_SESSION['levelUser'])){
    $_SESSION['alertMessage'] = "Déja connecté(e) !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /");
    exit();  
}
include_once("../config.php");
$titreDeLaPage = "Connexion | ".$GLOBALS['titreDePage'];
$descriptionPage = "";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");

?>

<div class="container-fluid">

    <?php require_once('./menu-login-register.php') ?>
    
    <div class="row mt-5">
        <div class="col-12 col-sm-9 col-md-6 mx-auto">
            <div class="card col p-0">
                <div class="card-header bg-dark text-white">
                    <div class=""><i class="fas fa-sign-in-alt"></i> Connexion</div>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <form class="form-signin" action="/connexion_registration/ctrl/ctrl-connexion.php" method="post">
                            <div class="form-group">
                                <input type="email" class="form-control" id="exampleInputEmail1" name="mail" aria-describedby="emailHelp" placeholder="Votre adresse mail..." require>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="exampleInputPassword" name="passwordUser" aria-describedby="passwordHelp" placeholder="Votre mot de passe..." require>
                            </div>
                            <button class="btn btn-primary" type="submit">Connexion</button>
                            <p class="card-text small mt-3 pl-5">
                                <a href="/connexion/forgotPassword/">Mot de passe oublié ?</a>
                            </p>
                        </form>
                    </p>
                    <!-- <p class="card-text text-right small">
                        <a href="/first-connexion/">Première connexion ?</a>
                    </p> -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("../commun/bas_de_page.php");?>