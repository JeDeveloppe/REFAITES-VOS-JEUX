<?php
@session_start ();
//DETAIL DE LA REPONSE DU PAIEMENT
echo "<pre>";
if(isset($_POST)){
    print_r($_POST);
}else{
    print_r($_GET);
}

echo "</pre>";
exit();

//controle de la réponse et redirection vers:   /paiement/fin-de-transaction-valide/  ou /paiement/fin-de-transaction-abandon/

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $reponse = $_POST;
    $result = json_decode($reponse['kr-answer'],true);
    


    //IL FAUT CONTROLE LE HASH ENVOYER
    if(isset($result['orderStatus']) && $result['orderStatus'] == "PAID"){
        if(isset($result['orderCycle']) && $result['orderCycle'] == "CLOSED"){

            require('../config.php');
            require('../bdd/connexion-bdd.php');
            require('../bdd/table_config.php');
            require('../controles/fonctions/validation_donnees.php');

                //IL FAUT RECUPERER NUMERO DU DEVIS
                $numDevis = $result['orderDetails']['orderId'];
                $num_transaction = $result['transactions'][0]['uuid'];
                

                $sqlDocExiste = $bdd -> prepare("SELECT * FROM documents WHERE numero_devis = ? AND etat = ?");
                $sqlDocExiste-> execute(array($numDevis,1));
                $donneesDocument = $sqlDocExiste-> fetch();
                $count = $sqlDocExiste-> rowCount();

                if($count == 1){   // si on trouve le document on met a jour avec le numero de facture
 
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
                    require_once("../controles/fonctions/incrementation.php");
                    $numeroFAC = incrementation($donneesConfig[8]['valeur'],$chiffreDocument);

                    $sqlUpdateDoc = $bdd -> prepare("UPDATE documents SET etat = ?, numero_facture = ?, time_transaction = ?, num_transaction = ?, moyen_paiement = ? WHERE idDocument = ?");
                    $sqlUpdateDoc-> execute(array(2,$numeroFAC,time(),$num_transaction,"CB", $donneesDocument['idDocument']));

                    //REDIRECTION VERS paiement/fin-de-transaction-valide/
                    $_SESSION['alertMessage'] = "MERCI !<p>Votre commande sera bientôt prête !</p>";
                    header("Location: /paiement/BANQUEPOSTALE/fin-de-transaction/");
                    exit();  
                }else{
                    $_SESSION['alertMessage'] = "Document inconnu !";
                    $_SESSION['alertMessageConfig'] = "warning";
                    header("Location: /accueil/");
                    exit();  
                }

                
            
        }
    }else{
        //REDIRECTION VERS paiement/fin-de-transaction-abandon/
        $_SESSION['alertMessage'] = "MERCI !<p>Erreur dans la transaction ou annulation de votre part !</p>";
        header("Location: /paiement/BANQUEPOSTALE/fin-de-transaction-abandon/");
        exit();  
    }
    
}
?>