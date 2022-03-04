<?php
@session_start ();

if(!isset($_GET['token']) || !isset($_GET['email']) || !isset($_GET['user'])){
    $_SESSION['alertMessage'] = "Variable manquante !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /");
    exit();  
}else{
    $email = $_GET['email'];
    $tokenUser = $_GET['user'];
    $token = $_GET['token'];
}

if(!isset($_SESSION['tokenPasswordChange'])){
    $_SESSION['alertMessage'] = "TokenPasswordChange manquant !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /");
    exit();  
}
if($_SESSION['tokenPasswordChange'] !== $token){
    $_SESSION['alertMessage'] = "TokenPasswordChange invalide !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /");
    exit();  
}

include_once("../config.php");
$titreDeLaPage = "Nouveau mot de passe | ".$GLOBALS['titreDePage'];
$descriptionPage = "";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");


?>

<div class="container-fluid mt-5">
    <div class="row my-5">
        <div class="col-12 col-sm-9 col-lg-7 mx-auto">
            <div class="card col p-0">
                <div class="card-header bg-dark text-white"><i class="fas fa-save"></i> Nouveau mot de passe</div>
                <div class="card-body">
                <form class="col" method="post" action="/connexion_registration/ctrl/verifPasswordChange.php">
                    <div class="col d-flex flex-column flex-lg-row">
                        <!-- CONTACT -->
                        <div class="col-12 col-md-11 mx-auto col-lg-8 m-0 d-flex flex-wrap">
                                <div class="col-12 form-group mb-3">
                                    <label for="password">Mot de passe:<sup class="text-danger">*</sup></label>
                                    <input type="password" name="password" id="password" class="form-control col-12 col-md-9" placeholder="Quelque chose de robuste..." aria-label="Username" aria-describedby="basic-addon1" required>
                                    <span id="precision" class="jumbotron bg-primary p-1 col-10">
                                        <ul class="m-0">
                                            <li class= "text-danger" id="precision-li-caracteres">Minimum 8 caractères</li>
                                            <li class= "text-danger" id="precision-li-majuscule">1 lettre en majuscule</li>
                                            <li class= "text-danger" id="precision-li-chiffre">1 chiffre</li>
                                            <li class= "text-danger"id="precision-li-special" >1 caractère spécial parmis:<br/><span class="font-weight-normal letter-spacing-1">:$&+;=?@#|'<>^*()%!-</span></li>
                                        </ul>
                                    </span>
                                </div>
                                <div class="col-12 form-group mb-3">
                                    <label for="password2">Vérification du mot de passe:<sup class="text-danger">*</sup></label>
                                    <input type="password" name="password2" id="password2" class="form-control col-12 col-md-9" placeholder="Quelque chose de robuste..." aria-label="Username" aria-describedby="basic-addon1" required>
                                </div>
                        </div>
                    </div>
                    <!-- CAPTCHA ET BOUTONS -->
                    <div class="col-12 mt-4 text-center">
                        <div class="col-12 text-center">
                            <input type="hidden" name="email" value="<?php echo $email; ?>">
                            <input type="hidden" name="tokenUser" value="<?php echo $tokenUser; ?>">
                            <button type="submit" class="btn btn-success border border-primary">Mettre à jour</button>
                        </div>
                        <div class="col-12 text-danger text-left mt-3 mt-sm-0">
                            <sup>(*)</sup> Obligatoire.
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
        let password = document.getElementById('password');
        let password2 = document.getElementById('password2');

        let liCaracteres = document.getElementById('precision-li-caracteres');
        let liMajuscule = document.getElementById('precision-li-majuscule');
        let liChiffre = document.getElementById('precision-li-chiffre');
        let liSpecial = document.getElementById('precision-li-special');

        password.addEventListener('keyup', () => {
            
            if(password.value.length == 0){
                document.getElementById("precision").style.display = "none";
            }else{
                document.getElementById("precision").style.display = "block";
                //on verifie la longueur minimum
                if(password.value.length >= 8){
                    liCaracteres.classList.remove('text-danger');
                    liCaracteres.classList.add('text-success');
                }else{
                    liCaracteres.classList.add('text-danger');
                    liCaracteres.classList.remove('text-success');
                }
                //on verifie la presence d'une majuscule
                if(password.value.match(/[A-Z]/g)){
                    liMajuscule.classList.remove('text-danger');
                    liMajuscule.classList.add('text-success');
                }else{
                    liMajuscule.classList.add('text-danger');
                    liMajuscule.classList.remove('text-success');
                }
                //on verifie la presence d'un chiffre
                if(password.value.match(/\d/g)){
                    liChiffre.classList.remove('text-danger');
                    liChiffre.classList.add('text-success');
                }else{
                    liChiffre.classList.add('text-danger');
                    liChiffre.classList.remove('text-success');
                }
                //on verifie la presence d'un des caracteres spacial
                if(password.value.match(/[$&+:;=?@#|'<>^*()%!-]/g)){
                    liSpecial.classList.remove('text-danger');
                    liSpecial.classList.add('text-success');
                }else{
                    liSpecial.classList.add('text-danger');
                    liSpecial.classList.remove('text-success');
                }


                
                // Init a timeout variable to be used below
                let timeout = null;
                // Listen for keystroke events
                password.addEventListener('keyup', function (e) {
                    // Clear the timeout if it has already been set.
                    // This will prevent the previous task from executing
                    // if it has been less than <MILLISECONDS>
                    clearTimeout(timeout);
                    // Make a new timeout set to go off in 1000ms (1 second)
                    timeout = setTimeout(function () {
                        document.getElementById("precision").style.display = "none";
                    }, 2000);
                });
            }
        })
        password2.addEventListener('keyup', () => {
            if(password.value == password2.value){
                password2.classList.add('border-success');
            }else{
                password2.classList.remove('border-success');
            }
        })


</script>
<?php
include_once("../commun/bas_de_page.php");
?>
