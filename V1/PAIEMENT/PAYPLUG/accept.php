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
        //ICI TOUT ET BON ON FAIT APPEL A L'API PAYPLUG

        $sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = ?");
        $sqlClient-> execute(array($donneesDocument['idUser']));
        $donneesClient = $sqlClient-> fetch();

        $secretkey = $GLOBAL['secretPaiement'];
        $email = $donneesClient['email'];
        $doc = $donneesDocument['validKey'];
        $first_name = $donneesClient['prenom'];
        $last_name = $donneesClient['nom'];
        $montantTTC = $donneesDocument['totalTTC'];
        $amount = (int)($montantTTC*100);

        require_once('./payplug_php/lib/init.php');
        Payplug\Payplug::init(array(
            'secretKey' => $secretkey,
            'apiVersion' => '2019-08-06',
          ));

        $payment = \Payplug\Payment::create(array(
            'amount'         => $amount,
            'currency'       => 'EUR',
            'billing'          => array(
                'title'        => 'Mme / Mr',
                'first_name'   => $donneesClient['prenom'],
                'last_name'    => $donneesClient['nom'],
                'email'        => $donneesClient['email'],
                'address1'     => $donneesClient['adresse'],
                'postcode'     => $donneesClient['cp'],
                'city'         => $donneesClient['ville'],
                'country'      => $donneesClient['pays'],
                'language'     => 'fr'
            ),
            'shipping'          => array(
                'title'        => 'Mme / Mr',
                'first_name'   => $donneesClient['prenom'],
                'last_name'    => $donneesClient['nom'],
                'email'        => $donneesClient['email'],
                'address1'     => $donneesClient['adresse'],
                'postcode'     => $donneesClient['cp'],
                'city'         => $donneesClient['ville'],
                'country'      => $donneesClient['pays'],
                'language'     => 'fr',
                'delivery_type' => 'BILLING'
            ),
            'hosted_payment' => array(
                'return_url'     => $GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/finDeTransaction-valide.php?doc='.$doc,
                'cancel_url'     => $GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/finDeTransaction-abandon.php'   //si y a abandon de paiement par utilisateur
            ),
            'notification_url' => $GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/notificationPaiement.php?doc='.$doc,
            'metadata'         => array(
                'customer_id'    => $doc
                )
        ));

        $payment_url = $payment->hosted_payment->payment_url;
        $payment_id = $payment->id;
        $_SESSION['payment_id'] = $payment_id;

        //a ce niveau j'ai le numero de transaction
        $sqlUpdateDoc = $bdd -> prepare("UPDATE documents SET time_transaction = ?, num_transaction = ?, page_controle = ? WHERE idDocument = ?");
        $sqlUpdateDoc-> execute(array(time(),$payment_id,"EN_COURS",$donneesDocument['idDocument']));
        header('Location:' . $payment_url);
        exit();
    }
}
?>