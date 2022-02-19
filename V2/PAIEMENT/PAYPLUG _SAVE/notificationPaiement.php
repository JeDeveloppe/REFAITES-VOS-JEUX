<?php
//UNIQUEMENT SI Y A UNE VALIDE KEY APPELLER DOC sur la page accept.php
if(isset($_GET['doc'])){
    include_once("../../config.php");
    require("../../controles/fonctions/validation_donnees.php");

    $validKey = valid_donnees($_GET['doc']);

    $secretkey = $GLOBAL['secretPaiement'];

    require_once('./payplug_php/lib/init.php');
    Payplug\Payplug::init(array(
        'secretKey' => $secretkey,
        'apiVersion' => $GLOBALS['api-version'],
    ));

    $input = file_get_contents('php://input');

    try {
    $resource = \Payplug\Notification::treat($input);

    print_r($resource);

        if($resource instanceof \Payplug\Resource\Payment
            && $resource->is_paid) {
                echo "PAIEMENT OK ->";
            $num_transaction = $resource->id;
            $payment_state = $resource->is_paid;
            $datePaiement = $resource->hosted_payment->paid_at;
            $payment_amount = $resource->amount;
            $payment_data = $resource->metadata[customer_id];

            require('../../bdd/connexion-bdd.php');
            require('../../bdd/table_config.php');
            //IL FAUT RECUPERER NUMERO DU DEVIS
            $sqlDocument = $bdd -> prepare("SELECT * FROM documents WHERE validKey = ? AND etat > ?");
            $sqlDocument-> execute(array($validKey,0));
            $countDocument = $sqlDocument-> rowCount();
            //si on trouve le document associe
            if($countDocument == 1){

                $donneesDocument = $sqlDocument-> fetch();
                $numDevis = $donneesDocument['idDocument'];

                //SI Y A PAS ENCORE DE NUMERO DE FACTURE C'EST QUE UTILISATEUR A QUITTER APRES LE PAIEMENT SANS RETOUR SUR LE SITE
                if($donneesDocument['numero_facture'] == ""){

                    echo " Numéro de FACTURE vide -> ";

                    //IL FAUT INCREMENTER LE NUMERO DE FACTURE
                    //on recupere l'année en cours au moment de l'enregistrement
                    $anneeCivil = date("Y", time());

                    //on cherche le dernier enregistrement
                    $sqlDernierEnregistrement = $bdd -> prepare("SELECT * FROM documents WHERE annee = ? AND numero_facture LIKE ? ORDER BY numero_facture DESC LIMIT 1");
                    $sqlDernierEnregistrement-> execute(array($anneeCivil,$donneesConfig[8]['valeur']."%"));
                    $donneesLastRow = $sqlDernierEnregistrement-> fetch();
                    $nbRow = $sqlDernierEnregistrement-> rowCount();


                    if($nbRow == 0){  //pas encore d'enregistrement
                        $chiffreDocument = 1;
                    }else{
                        $lastAnneeEnCours = $donneesLastRow['annee'];
                            if($lastAnneeEnCours == $anneeCivil){
                                $rest = substr($donneesLastRow['numero_facture'], -4);
                                $chiffreDocument = $rest + 1;
                            }else{
                                $chiffreDocument = 1;
                            }
                    }

                    //on incremente le numero
                    require_once("../../controles/fonctions/incrementation.php");
                    $numeroFAC = incrementation($donneesConfig[8]['valeur'],$chiffreDocument);

                    //on calcule la commission sur la vente
                    $commissionVente = number_format(($donneesDocument['totalHT'] * $donneesConfig[25]['valeur']) + $donneesConfig[26]['valeur'],2);

                    $sqlUpdateDoc = $bdd -> prepare("UPDATE documents SET etat = ?, numero_facture = ?, num_transaction = ?, page_controle = ?, commission_vente = ?, time_transaction = ?, moyen_paiement = ?, envoyer = ? WHERE idDocument = ?");
                    $sqlUpdateDoc-> execute(array(2,$numeroFAC,$num_transaction,"page_notif",$commissionVente,$datePaiement,"CB",0,$donneesDocument['idDocument']));
                    $factureGeneree =  " OK FACTURE GENEREE";
                    echo $factureGeneree;

                    //on met a jour association du client pour 1 an
                    $sqlUpdateClient = $bdd->prepare("UPDATE clients SET isAssociation = ? WHERE idClient = ?");
                    $sqlUpdateClient->execute(array($datePaiement+31536000,$donneesDocument['idUser']));
                }
                if(!$factureGeneree){
                    echo " NUMERO DE FACTURE DEJA GENEREE";
                }
            }else{
                echo " Document non trouvé ou en état différent de 1";
            }
        }else{
        echo "PAS PAYER";
        }
    }
    catch (\Payplug\Exception\PayplugException $exception) {
        echo htmlentities($exception);
    }
}