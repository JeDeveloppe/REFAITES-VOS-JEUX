<?php
@session_start ();
require('../../config.php');
require('../../bdd/connexion-bdd.php');
require('../../controles/fonctions/validation_donnees.php');

if(!isset($_GET['doc']) || !isset($_GET['user'])){
    $_SESSION['alertMessage'] = "Il manque une variable utile...";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /");
    exit();
}else{

    $validKey = valid_donnees($_GET['doc']);
    $user = valid_donnees($_GET['user']);

    $sqlDocExiste = $bdd -> prepare("SELECT * FROM documents WHERE validKey = ? AND idUser = ? AND etat = ?");
    $sqlDocExiste-> execute(array($validKey,$user,1));
    $donneesDocument = $sqlDocExiste-> fetch();
    $count = $sqlDocExiste-> rowCount();

    if($count != 1){
        $_SESSION['alertMessage'] = "Incohérence état du document - client !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: /");
        exit();  
    }else{
        //ICI TOUT ET BON ON FAIT APPEL A L'API PAYGREEN

        $sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = ?");
        $sqlClient-> execute(array($donneesDocument['idUser']));
        $donneesClient = $sqlClient-> fetch();

        //IDENTIFICATION BANQUE POSTALE ET VALEURS UTILES
        
        /**
         * I initialize the PHP SDK
         */
        require_once('./vendor/autoload.php');
        require_once('./vendor/keys.php');
        // require_once('./vendor/helpers.php');

        /** 
         * Initialize the SDK 
         * see keys.php
         */
        $client = new Lyra\Client();

        /**
         * I create a formToken
         */
        $totalTTCcentimes = $donneesDocument['totalTTC'] * 100;
        $store = array(
            "amount" => $totalTTCcentimes, 
            "currency" => "EUR", 
            "orderId" => $donneesDocument['numero_devis'],
            "customer" => array(
                            "email" => $donneesClient['email']),
            //"ipnTargetUrl" => $GLOBALS['domaine']."/paiement/notificationPaiement.php"             //url appel ipn
            );
        $response = $client->post("V4/Charge/CreatePayment", $store);

        /* I check if there are some errors */
        if ($response['status'] != 'SUCCESS') {
            /* an error occurs, I throw an exception */
            $error = $response['answer'];
            throw new Exception("error " . $error['errorCode'] . ": " . $error['errorMessage'] );
        }

        /* everything is fine, I extract the formToken */
        $formToken = $response["answer"]["formToken"];

        $titreDeLaPage = "Page de paiement | ".$GLOBALS['titreDePage'];
        $descriptionPage = "";
        require_once("../../config.php");
        require_once("../../commun/haut_de_page.php");
        ?>
        <div class="container-fluid">
            <div class="row mt-5">
                <div class="col-12 text-center h3">Page de paiement</div>
                <div class="col-11 col-sm-8 col-md-5 col-lg-4 mt-2 mx-auto jumbotron bg-vos p-3">
                    <div class="col-12">
                        <div class="col-12 mx-auto text-center">
                            Avec...<br/>
                            <img class="col-4 col-md-6 col-lg-4" src="/PAIEMENT/BANQUEPOSTALE/logo-paiementDuService.png" alt="logo paiement La Banque Postale"/>
                        </div>
                        <!-- payment form -->
                        <div class="kr-embedded mx-auto mt-4" kr-form-token="<?php echo $formToken;?>">
                            <!-- payment form fields -->
                            <div class="kr-pan"></div>
                            <div class="kr-expiry"></div>
                            <div class="kr-security-code"></div>  
                            <!-- payment form submit button -->
                            <button class="kr-payment-button"></button>
                            <!-- error zone -->
                            <div class="kr-form-error"></div>
                        </div>
                        <div class="col-12 mt-2 text-center">DEVIS n° <?php echo $donneesDocument['numero_devis'];?></div>
                        <div class="col-12 text-center"><?php echo $donneesClient['nom'].' '.$donneesClient['prenom'];?></div>
                    </div>
                </div>
            </div>  
        </div>

<?php
    require_once("../../commun/bas_de_page.php");
        
        }
}
?>