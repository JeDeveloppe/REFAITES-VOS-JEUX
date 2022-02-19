<?php
@session_start ();
//DETAIL DE LA REPONSE DU PAIEMENT
// echo "<pre>";
// var_dump($_POST);
// echo "</pre>";
// exit();

if($_SERVER["REQUEST_METHOD"] == "POST"){

    require_once("../controles/fonctions/validation_donnees.php");
    $pid = valid_donnees($_GET['pid']);
    $result = valid_donnees($_GET['result']);
    $numDevis = valid_donnees($_GET['orderId']);

    if(empty($pid) || empty($result) || empty($numDevis)){
        $_SESSION['alertMessage'] = "Donnée manquante !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: /accueil/");
        exit();  
    }

    if($result != "SUCCESSED"){
        //REDIRECTION VERS paiement/fin-de-transaction-abandon/
        $_SESSION['alertMessage'] = "ERREUR<p>Problème dans le paiement !</p>";
        header("Location: /paiement/fin-de-transaction-abandon/");
        exit(); 
        $_SESSION['alertMessage'] = "Transaction abandonnée !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: /accueil/");
        exit();       
    }else{
        require('../config.php');
        require('../bdd/connexion-bdd.php');
        require('../bdd/table_config.php');

        $sqlDocExiste = $bdd -> prepare("SELECT * FROM documents WHERE numero_devis = ? AND etat = ? AND transaction = ?");
                $sqlDocExiste-> execute(array($numDevis,1,$pid));
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

                    $sqlUpdateDoc = $bdd -> prepare("UPDATE documents SET etat = ?, numero_facture = ?, time_transaction = ?, moyen_paiement = ?, envoyer = ? WHERE idDocument = ?");
                    $sqlUpdateDoc-> execute(array(2,$numeroFAC,time(),"CB",0, $donneesDocument['idDocument']));

                    //REDIRECTION VERS paiement/fin-de-transaction-valide/
                    $_SESSION['alertMessage'] = "MERCI !<p>Votre commande sera bientôt prête !</p>";
                    $_SESSION['boutonFacture'] = $donneesDocument['idDocument'];
                    header("Location: /paiement/fin-de-transaction-valide/");
                    exit();  
                }else{
                    $_SESSION['alertMessage'] = "Document inconnu !";
                    $_SESSION['alertMessageConfig'] = "warning";
                    header("Location: /accueil/");
                    exit();  
                }

    }
}else{
    $_SESSION['alertMessage'] = "Mauvaise requete reçue !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /accueil/");
    exit();  
}
?>