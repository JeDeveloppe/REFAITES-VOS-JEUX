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
$titreDeLaPage = "1ère connexion | ".$GLOBALS['titreDePage'];
$descriptionPage = "";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
?>

<div class="container-fluid">
    <?php
        if(!isset($_SESSION['resendPassword'])){
            if(preg_match('#/connexion/forgotPassword/#',$_SERVER['REQUEST_URI'])){
                echo '<div class="row my-5 h-100">
                    <div class="col-12 text-center h1 mt-5"><h2>Mot de passe oublié</h2></div>
                    <div class="col-12 col-sm-9 col-md-6 mx-auto">
                        <div class="card col p-0">
                            <div class="card-header bg-dark text-white"><i class="fas fa-paper-plane"></i> Mot de passe oublié ?</div>
                            <div class="card-body">
                                <p class="card-text">
                                    <form class="form-signin" action="/connexion_registration/ctrl/ctrl-resend.php" method="post">
                                        <div class="form-group">
                                        <label class="text-danger">Adresse email enregistrée:</label>
                                            <input type="email" class="form-control" id="exampleInputEmail1" name="mail" aria-describedby="emailHelp" placeholder="Votre adresse mail..." required>
                                        </div>
                                        <button class="btn btn-primary" type="submit">Recevoir un lien pour changer mon mot de passe...</button>
                                    </form>
                                </p>
                                <p class="card-text text-right small">
                                    <a href="/connexion/">Se connecter</a><br/>
                                    <a href="/inscription/">S\'inscrire</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>';
            }else{
            echo '<div class="row my-5 h-100">
                    <div class="col-12 text-center h1 mt-5"><h2>Premier mot de passe</h2></div>
                    <div class="col-12 col-sm-9 col-md-6 mx-auto">
                        <div class="card col p-0">
                            <div class="card-header bg-dark text-white"><i class="fas fa-paper-plane"></i> Premier mot de passe</div>
                            <div class="card-body">
                                <p class="card-text">
                                    <form class="form-signin" action="/connexion_registration/ctrl/ctrl-resend.php" method="post">
                                        <div class="form-group">
                                        <label class="text-danger">Ravis de vous revoir ! Saisissez votre adresse email pour pouvoir générer un mot de passe !</label>
                                            <input type="email" class="form-control" id="exampleInputEmail1" name="mail" aria-describedby="emailHelp" placeholder="Votre adresse mail..." required>
                                        </div>
                                        <button class="btn btn-primary" type="submit">Demander</button>
                                    </form>
                                </p>
                                <p class="card-text text-right small">
                                    <a href="/connexion/">Se connecter</a><br/>
                                    <a href="/inscription/">S\'inscrire</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        }else{
            echo '<div class="row my-5 h-100">
            <div class="col-12 text-center h1 mt-5"><h2>Mot de passe</h2></div>
            <div class="col-12 col-sm-9 col-md-6 mx-auto">
                <div class="card col p-0">
                    <div class="card-header bg-dark text-white"><i class="fas fa-paper-plane"></i> Lien envoyé</div>
                    <div class="card-body">
                        <p class="card-text p-3 text-center">
                          Si cette adresse email existe, un email vient de vous être envoyé.<br/> (n\'oubliez pas de vérifier les SPAMS !!)
                        </p>
                        <p class="card-text text-right small">
                            <a href="/connexion/">Se connecter</a><br/>
                            <a href="/inscription/">S\'inscrire</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>';
            unset($_SESSION['resendPassword']);
        }
        ?>
</div>

<?php include_once("../commun/bas_de_page.php"); ?>
