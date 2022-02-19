<?php
@session_start (); 

if(isset($_GET['pid'])){
    include_once("../../config.php");

    //IDENTIFICATION PAYGREEN ET VALEURS UTILES
    define('ID_UNIQUE_PAYGREEN',"35e1d4ad009c2601e9589a766dfc0e90");
    define('PRIVATE_KEY_PAYGREEN',"d8b0-4df7-b6a7-30eafbb5618e");

    
     //url de l'appel vers api
     $url = "https://paygreen.fr/api/".ID_UNIQUE_PAYGREEN."/payins/transaction/".$_GET['pid'];

     $authorization = "Authorization: Bearer ".PRIVATE_KEY_PAYGREEN; 
     $ch = curl_init($url);
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
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
            
            $etatPaiement = $objet['data']['result']['status'];
            $num_transaction = $objet['data']['id'];
            $datePaiement = time();

            //si la transaction est bien payéé donc OK
            if($etatPaiement == "SUCCESSED"){
                require('../../bdd/connexion-bdd.php');
                require('../../bdd/table_config.php');
                //IL FAUT RECUPERER NUMERO DU DEVIS
                $sqlDocument = $bdd -> prepare("SELECT * FROM documents WHERE transaction = ? AND etat = ?");
                $sqlDocument-> execute(array($num_transaction,1));
                $donneesDocument = $sqlDocument-> fetch();
                $numDevis = $donneesDocument['idDocument'];

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

                $sqlUpdateDoc = $bdd -> prepare("UPDATE documents SET etat = ?, numero_facture = ?, time_transaction = ?, moyen_paiement = ?, envoyer = ? WHERE idDocument = ?");
                $sqlUpdateDoc-> execute(array(2,$numeroFAC,$datePaiement,"CB",0,$donneesDocument['idDocument']));


                $titreDeLaPage = "Fin de la transaction | ".$GLOBALS['titreDePage'];
                $descriptionPage = "";
                include_once("../../commun/haut_de_page.php");
                ?>
                <div class="container-fluid">
                    <div class="row mt-5">
                        <div class="card p-0 col-11 col-md-8 col-lg-6 mt-4 mb-4 mx-auto">
                            <div class="card-header bg-dark text-white text-center">Confirmation</div>
                            <div class="card-body">
                                <div class="col-12 text-center align-middle"><i class="fas fa-check-square fa-2x text-success"></i> MERCI !<p>Votre commande sera bientôt prête !</p></div>
                                <div class="col-12 text-center">
                                    <a href="/accueil/" class="btn btn-primary">Retour à l'accueil !</a>

                                    <form method="post" action="/administration/facture/generation-pdf-envoi.php" class="mt-4">
                                    <input type="hidden" name="document" value="<?php echo $donneesDocument['idDocument']; ?>">
                                    <button class="btn btn-primary">Je souhaite une facture !</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            include_once("../../commun/bas_de_page.php");
            }else{ //fin de transaction OK
                header("Location: /PAIEMENT/PAYGREEN/finDeTransaction-abandon.php");
                exit();  
            }
        }
        else{
            $_SESSION['alertMessage'] = "Pas de réponse de l'API PAYGREEN !<br/> Veuillez réessayer !";
            $_SESSION['alertMessageConfig'] = "warning";
            header("Location: /");
            exit(); 
        }  
}else{
    $_SESSION['alertMessage'] = "Transaction terminée !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /");
    exit();  
}
?>
