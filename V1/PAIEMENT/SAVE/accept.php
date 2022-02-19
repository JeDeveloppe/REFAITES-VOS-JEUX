<?php
@session_start ();
require('../config.php');
require('../bdd/connexion-bdd.php');
require('../controles/fonctions/validation_donnees.php');

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

        //IDENTIFICATION PAYGREEN ET VALEURS UTILES
        define('ID_UNIQUE_PAYGREEN','35e1d4ad009c2601e9589a766dfc0e90');
        define('PRIVATE_KEY_PAYGREEN','d8b0-4df7-b6a7-30eafbb5618e');
        $montantEnCentime = $donneesDocument['totalTTC'] * 100;
        
        //url de l'appel vers api
        $url = "https://paygreen.fr/api/".ID_UNIQUE_PAYGREEN."/payins/transaction/cash";

        //DATAS
        $data = array(
            "orderId" => $donneesDocument['numero_devis'],                               //numero du devis
            "amount" => $montantEnCentime,                   //montant en centime du devis
            "currency" => "EUR",                         //monnaie utilisee
            "paymentType" => "CB",                       //uniquement les CB
            "returned_url" => $GLOBALS['domaine']."/paiement/notificationPaiement.php",                        //url retour client apres transaction
            "notified_url" => $GLOBALS['domaine']."/paiement/notificationPaiement.php",                        //url de notification vers serveur
            "buyer" => array(                             //OBJET CLIENT
                        "id" => $donneesClient['idClient'],              //idUser unique
                        "lastName" => $donneesClient['nom'],         //prenom
                        "firstName" => $donneesClient['prenom'],        //nom
                        "email" => $donneesClient['email'],            //adresse mail
                        "country" => $donneesClient['pays'],
                        ),
            "metadata" => array(
                "orderId" => $donneesDocument['numero_devis'],
                "display" => "0"
            ),
        );

        $postdata = json_encode($data);
        $authorization = "Authorization: Bearer ".PRIVATE_KEY_PAYGREEN; 
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        $result = curl_exec($ch);
        curl_close($ch);
        
            //SI ON A UN RETOUR DE L'API
            if(isset($result)){
                // print_r($result);
                // exit();
                //ON DECODE LE RESULTAT POUR RECUPERER URL DE DEMANDE
                $objet = json_decode($result,true);

                if($objet['success'] == "true"){ //echange OK on redirige vers l'url de paiement
                    //on garde le numero de transaction de l'API
                    $transaction = valid_donnees($objet['data']['id']);
                    $sqlUpdateDoc = $bdd -> prepare("UPDATE documents SET transaction = ? WHERE validKey = ? AND etat = ?");
                    $sqlUpdateDoc-> execute(array($transaction, $validKey, 1));
                    header("Location: ".$objet['data']['url']);
                    exit();  
                }else{
                    $_SESSION['alertMessage'] = "Échec de connexion à l'API de paiement !";
                    $_SESSION['alertMessageConfig'] = "danger";
                    header("Location: /");
                    exit();  
                }
            }
        }
}
?>