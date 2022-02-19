<?php
@session_start (); 
require("../controles/fonctions/adminOnline.php");
include_once("../config.php");
$titreDeLaPage = "Inscription | ".$GLOBALS['titreDePage'];
$descriptionPage = "";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6 mx-auto">
            <div class="card col p-0 mt-3 m-1">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-save"></i> Inscription [ADMIN]
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <form class="form-register" action="/controles/verifFormRegister.php" method="post">
                            <div class="form-group">
                                <input type="text" class="form-control" id="exampleInputPseudo" name="pseudo" aria-describedby="pseudoHelp" placeholder="Choisissez un pseudo, max 15 caractères" maxlength="15" require>
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" id="exampleInputEmail1" name="mail" aria-describedby="emailHelp" placeholder="Votre adresse mail" require>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="exampleInputPassword1" name="password1" placeholder="Choisissez un mot de passe" require>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="exampleInputPassword2" name="password2" placeholder="Répéter votre mot de passe" require>
                            </div>
                                <button type="submit" class="btn btn-primary">Inscription</button>
                        </form>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once("../commun/bas_de_page.php");
?>
