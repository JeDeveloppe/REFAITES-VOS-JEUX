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
$titreDeLaPage = "Inscription | ".$GLOBALS['titreDePage'];
$descriptionPage = "";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");

$sqlPays = $bdd-> prepare("SELECT * FROM pays WHERE actif = 1 AND rattachement = ? ORDER BY nom_fr_fr ASC");
$sqlPays-> execute(array("NONE"));
$donneesPays = $sqlPays-> fetchAll();

$sqlOptgroup = $bdd -> prepare("SELECT * FROM pays WHERE actif = 1 AND rattachement = ? ORDER BY nom_fr_fr ASC");
$sqlOptgroup-> execute(array("FR-"));
$donneesOptGroup = $sqlOptgroup-> fetch();

?>

<div class="container-fluid">
    
    <?php require_once('./menu-login-register.php') ?>

    <div class="row my-5">
        <div class="col-12 col-sm-9 col-lg-7 mx-auto">
            <div class="card col p-0">
                <div class="card-header bg-dark text-white"><i class="fas fa-save"></i> Inscription</div>
                <div class="card-body">
                <form class="col" method="post" action="/connexion_registration/ctrl/verifFormRegister.php" name="contactretrait">
                    <div class="col d-flex flex-column flex-lg-row">
                        <!-- CONTACT -->
                        <div class="col-12 col-md-11 mx-auto col-lg-9 m-0 d-flex flex-wrap">
                                <div class="col-12 col-sm-8 form-group mb-3">
                                    <label for="email">Email:<sup class="text-danger">*</sup></label>
                                    <input type="email" name="email" class="form-control" placeholder="Adresse email valide merci !" <?php if(isset($_SESSION['email'])){echo 'value="'.$_SESSION['email'].'"';}?> required>
                                </div>
                                <div class="col-12 col-sm-8 form-group mb-3">
                                    <label for="email">Téléphone:<sup class="text-danger">*</sup></label>
                                    <input type="text" name="telephone" class="form-control" pattern="^[0-9]{10,14}$"  <?php if(isset($_SESSION['telephone'])){echo 'value="'.$_SESSION['telephone'].'"';}?> required>
                                </div>
                                <div class="col-6 col-sm-8 col-md-6 form-group mb-3">
                                    <label for="pays">Pays:<sup class="text-danger">*</sup></label>
                                    <select name="pays" class="form-control col-12" id="pays" required>
                                        <option value=''>Choisir un pays...</option>
                                        <option value='FR'>FRANCE</option>
                                        <option value='BE'>BELGIQUE</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-8 col-md-6 form-group mb-3">
                                    <label for="cp">Département / Province:<sup class="text-danger">*</sup></label>
                                    <select name="cp" class="form-control col-12" id="departements" required>
                                        <option value=''>En attente du pays...</option>
                                    </select>
                                </div>
                     
                           
                                
                      
                                <div class="col-12 col-sm-8 col-md-6 col-lg-6 form-group mb-3">
                                    <label for="password">Mot de passe:<sup class="text-danger">*</sup></label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Quelque chose de robuste..." aria-label="Username" aria-describedby="basic-addon1" required>
                                    <span id="precision" class="jumbotron bg-primary p-1 col-10">
                                        <ul class="m-0">
                                            <li class= "text-danger" id="precision-li-caracteres">Minimum 8 caractères</li>
                                            <li class= "text-danger" id="precision-li-majuscule">1 lettre en majuscule</li>
                                            <li class= "text-danger" id="precision-li-chiffre">1 chiffre</li>
                                            <li class= "text-danger"id="precision-li-special" >1 caractère spécial parmis:<br/><span class="font-weight-normal letter-spacing-1">:$&+;=?@#|'<>^*()%!-</span></li>
                                        </ul>
                                    </span>
                                </div>
                                <div class="col-12 col-sm-8 col-md-6 col-lg-6 form-group mb-3">
                                    <label for="password2">Vérification du mot de passe:<sup class="text-danger">*</sup></label>
                                    <input type="password" name="password2" id="password2" class="form-control" placeholder="Quelque chose de robuste..." aria-label="Username" aria-describedby="basic-addon1" required>
                                </div>
                            
                        </div>
                    </div>
                    <!-- CAPTCHA ET BOUTONS -->
                    <div class="col mt-4 text-center">
                        <div class="col text-center mb-3">
                            <input id="lu" type="checkbox" name="conditionBienOk" value="lu" required> <label for="lu">J'ai lu et j'accepte les</label> <a href="/mentions-legales/#cgu">conditions générales d'utilisation</a>.<sup>(1)</sup>
                        </div>
                        <div class="col-12 text-center">
                            <input type="hidden" id="recaptchaResponse" name="recaptcha-response">
                            <button type="submit" class="btn btn-success border border-primary">S'inscrire</button>
                        </div>
                        <div class="col text-danger text-left mt-3 mt-sm-0">
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
        let choix = document.getElementById('pays');
        let departements = document.getElementById('departements');
        // GESTION CODE POSTAUX FRANCE


        choix.addEventListener('change', () => {
            let pays = choix.value;
            fetch('../../requetes/pays_dep-province.php?pays='+pays)
                .then(response => response.text())
                .then((response) => {
                    departements.innerHTML = response;
                })
                .catch(err => console.log(err))
        })



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
require_once("../captcha/captchaGoogle.php");
include_once("../commun/bas_de_page.php");
?>
