<?php
@session_start ();
require_once('../controles/fonctions/memberOnline.php');
include_once("../config.php");
$titreDeLaPage = "Espace membre | ".$GLOBALS['titreDePage'];
$descriptionPage = "Espace membre";
include_once("../bdd/connexion-bdd.php");

$sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idUser = ?");
$sqlClient-> execute(array($_SESSION['sessionId']));
$donneesClient = $sqlClient->fetch();


function random_strings($length_of_string) 
{ 
    // String of all alphanumeric character 
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@'; 

    // Shufle the $str_result and returns substring 
    // of specified length 
    return substr(str_shuffle($str_result),0, $length_of_string); 
} 

$validKey = random_strings(64);
$_SESSION['tokenPasswordChange'] = $validKey;

include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
?>

<div class="container-fluid mt-5">

    <?php require_once('./menuMembre.php'); ?>

    <div class="row mt-3">
        <div class="col-11 mx-auto col-lg-9">
            <?php
                if($_SESSION['levelUser'] > 1){
                    echo '  <div class="row justify-content-center">
                    <div class="input-group mb-3 col-12 col-sm-9 col-md-6">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Pseudo:</span>
                        </div>
                        <input type="text" class="form-control" placeholder="Pseudo" value="'.$donneesClient['pseudo'].'">
                    </div>
                </div>';
                }
            ?>
          
            <div class="row justify-content-center">
                <div class="input-group mb-3 col-12 col-sm-9 col-md-6">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">Adresse email:</span>
                    </div>
                    <input type="text" class="form-control" placeholder="Adresse email" value="<?php echo $donneesClient['email']; ?>" readonly>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="input-group mb-3 col-12 col-sm-9 col-md-6">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">Mot de passe:</span>
                    </div>
                    <input type="text" class="form-control" placeholder="Vous seul(e) le savez..." readonly>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="mb-3 col-12 col-sm-9 col-md-6 text-center">                      
                        <a href="/connexion/password/change/?email=<?php echo $donneesClient['email']; ?>&token=<?php echo $validKey; ?>&user=<?php echo $donneesClient['idUser']; ?>" class="btn bg-refaites text-white border-primary">Je veux changer mon mot de passe !</a>
                </div>
            </div>
            <div class="row justify-content-end">
                <div class="mb-3 col-12 col-sm-9 col-md-6 text-center">                      
                        <a href="#" onclick="confirmationSuppressionCompte()" class="btn bg-danger text-white border-primary">Supprimer mon compte</a>
                </div>
            </div>
    </div>
</div>
<script>
    let user = <?php echo json_encode($_SESSION['sessionId']); ?>;
    function confirmationSuppressionCompte(){
        var val = confirm("Vous êtes sûr de vouloir supprimer votre compte ? \n Vous n'aurez plus accès à votre historique de facture, pensez à les télécharger...");

        if( val == true ) {
            window.location.href = "/membre/deleteAccount/?client="+user; 
        } else {
            window.location.href = "/membre/mon-compte/"; 
        }
    }
</script>
<?php
    include_once("../commun/bas_de_page.php");
?>