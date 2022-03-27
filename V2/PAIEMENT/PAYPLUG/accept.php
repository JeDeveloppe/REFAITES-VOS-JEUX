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
    $donneesDocument = $sqlDocExiste->fetch();
    $count = $sqlDocExiste->rowCount();

    if($count != 1){
        $_SESSION['alertMessage'] = "Incohérence état du document - client !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: /");
        exit();  
    }else{
        //ICI TOUT ET BON ON FAIT APPEL A L'API PAYPLUG

        $sqlClient = $bdd -> prepare("SELECT email FROM clients WHERE idClient = ?");
        $sqlClient-> execute(array($donneesDocument['idUser']));
        $donneesTableClient = $sqlClient-> fetch();

        //a partir des infos de la table documents
        $donneesClient = $donneesDocument['adresse_facturation'];
        $donneesClientDetails = explode('<br/>',$donneesClient);

        //si y a une association de saisie
        if(count($donneesClientDetails) == 5){
            $donneesClientNomPrenom = explode(' ',$donneesClientDetails[1]);
            $donneesClientCpVille = explode(' ',$donneesClientDetails[3]);
            $pays = $donneesClientDetails[4];

        }else{
            $donneesClientNomPrenom = explode(' ',$donneesClientDetails[0]);
            $donneesClientCpVille = explode(' ',$donneesClientDetails[2]);
            $pays = $donneesClientDetails[3];
        }

        $secretkey = $GLOBAL['secretPaiement'];
        $email = $donneesTableClient['email'];
        $doc = $donneesDocument['validKey'];
        $first_name = $donneesClientNomPrenom[1];
        $last_name = $donneesClientNomPrenom[0];
        $adresse = $donneesClientDetails[1];
        $ville = $donneesClientCpVille[1];
        $cp = $donneesClientCpVille[0];
        $montantTTC = $donneesDocument['totalTTC'];
        $amount = (int)($montantTTC);

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
                'first_name'   => $first_name,
                'last_name'    => $last_name,
                'email'        => $email,
                'address1'     => $adresse,
                'postcode'     => $cp,
                'city'         => $ville,
                'country'      => $pays,
                'language'     => 'fr'
            ),
            'shipping'          => array(
                'title'        => 'Mme / Mr',
                'first_name'   => $first_name,
                'last_name'    => $last_name,
                'email'        => $email,
                'address1'     => $adresse,
                'postcode'     => $cp,
                'city'         => $ville,
                'country'      => $pays,
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