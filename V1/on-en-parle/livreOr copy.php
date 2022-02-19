<?php
@session_start ();
include_once("../config.php");
$titreDeLaPage = "On en parle dans le livre d'or | ".$GLOBALS['titreDePage'];
$descriptionPage = "Le livre d'or est là pour laisser des messages sur le service !";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
include_once("../bdd/connexion-bdd.php");

$sqlLivre = $bdd-> prepare("SELECT * FROM livreOr WHERE actif = ? ORDER BY time DESC");
$sqlLivre-> execute(array(1));
$donneesLivre = $sqlLivre-> fetch();
$countLivreOr = $sqlLivre -> rowCount();
?>
<div class="container-fluid mt-5 d-none">
    <div class="col-12 text-center mb-4"><h1>Livre d'or</h1></div>

    <div class="col-12 text-center my-5">Encore en construction...</div>

    <div class="col-12 text-center my-5">Revenez dans quelque temps...</div>

    <p class="col-12 text-center my-5">
        <a href="/catalogue/" class="btn btn-info bg-refaites my-2">Aller au catalogue</a>
    </p>
</div>
<div class="container-fluid mt-5">
    <div class="col-12 text-center mb-4"><h1>Livre d'or</h1></div>
    <!-- formulaire -->
    <div class="row">
        <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-5 text-center mx-auto">
            <form method="post" action="/on-en-parle/ctrl/ctrl-livreOr.php">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">Pseudo:</span>
                    </div>
                    <input type="text" class="form-control" name="pseudo" placeholder="Pseudo" pattern="[a-zA-ZÀ-ÿ]{3,30}" maxlength="30" value="<?php if(isset($_SESSION['pseudo'])){echo $_SESSION['pseudo'];}?>" required>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">@</span>
                    </div>
                    <input type="email" class="form-control" name="email" placeholder="Email valide" value="<?php if(isset($_SESSION['email'])){echo $_SESSION['email'];}?>" required>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Votre message:</span>
                    </div>
                    <textarea class="form-control" name="content" placeholder="Entre 15 et 300 caractères..." pattern="[a-zA-ZÀ-ÿ ]{15,300}" maxlength="300" required><?php if(isset($_SESSION['content'])){echo $_SESSION['content'];}?></textarea>
                </div>
                <div class="col-12 text-center my-3">
                    <input id="lu" type="checkbox" name="conditionBienOk" value="lu" required> <label for="lu">J'ai lu et j'accepte les</label> <a href="/mentions-legales/#cgu">conditions générales d'utilisation</a>.<sup>(1)</sup>
                </div>
                <input type="hidden" id="recaptchaResponse" name="recaptcha-response">
                <button class="btn btn-primary mt-2">Envoyer</button>
                <div class="col text-danger text-left mt-3 mt-sm-0">
                    <sup>(1)</sup> Obligatoire.
                </div>
            </form>
        </div>
    </div>
    <!-- les messages -->
    <div class="row mt-5 d-flex flex-wrap justify-content-start">
        <hr class="col-11 mx-auto">
        <?php
        //si y a pas de message
        if($countLivreOr < 1){
            echo '<div class="col-11 my-2 mx-auto">
                        <div class="col-11 text-center mx-auto"><i class="fas fa-smile-beam fa-2x text-warning"></i> Les messages vont arrivés...</div>
                    </div>';
        }else{
            while($donneesLivre){
                //pour chaque message utilisateur on regarde si admin a mis en remerciement
                $sqlMessageLivre = $bdd -> prepare("SELECT * FROM livreOr_messages WHERE idLivre = ?");
                $sqlMessageLivre-> execute(array($donneesLivre['idLivre']));
                $donneesMessageLivre = $sqlMessageLivre-> fetch();
                $count = $sqlMessageLivre -> rowCount();
                if($count == 1){
                    $messageAdmin = '<div class="jumbotron mt-4 mb-0 p-3 bg-light mb-0">'.$donneesMessageLivre['message'].'</div>';
                }else{
                    $messageAdmin = "";
                }
                echo '<div class="col-11 my-2 col-sm-6 col-md-4 mx-auto mx-sm-0" >
                        <div class="jumbotron py-2 bg-vos h-100">
                            <div class="col-12 h5 mt-2">Message de '.$donneesLivre['pseudo'].'</div>
                            <div class="col-12 text-right small">...le '.date("d.m.Y",$donneesLivre['time']).' à '.date("h:i:s",$donneesLivre['time']).'</div>
                            <div class="col-12 mt-3">'.$donneesLivre['content'].'</div>
                            '.$messageAdmin.'          
                        </div>
                    </div>';
            $donneesLivre = $sqlLivre-> fetch();
            }
        }
        ?>
    </div>

</div>
<?php
require_once("../captcha/captchaGoogle.php");
include_once("../commun/bas_de_page.php");
?>
