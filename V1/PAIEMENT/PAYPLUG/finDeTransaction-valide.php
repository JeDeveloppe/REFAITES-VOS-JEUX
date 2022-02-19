<?php
@session_start (); 
if(isset($_SESSION['payment_id']) && $_GET['doc']){
    include_once("../../config.php");
    require("../../controles/fonctions/validation_donnees.php");

    //on recuperer la variable validKey
    $validKey = valid_donnees($_GET['doc']);

    $secretkey = $GLOBAL['secretPaiement'];
        require_once('./payplug_php/lib/init.php');
        Payplug\Payplug::init(array(
            'secretKey' => $secretkey,
            'apiVersion' => $GLOBALS['api-version'],
        ));
        
    $payment = \Payplug\Payment::retrieve($_SESSION['payment_id']);

    $etatPaiement = $payment->is_paid;
    $datePaiement = $payment->paid_at;
    $num_transaction = $payment->id;
    $urlPaiement = $payment->hosted_payment->payment_url;

    //si la transaction est bien payéé donc OK
    if($etatPaiement == 1){
        require('../../bdd/connexion-bdd.php');
        require('../../bdd/table_config.php');
        //IL FAUT RECUPERER NUMERO DU DEVIS
        $sqlDocument = $bdd -> prepare("SELECT * FROM documents WHERE validKey = ? AND etat = ?");
        $sqlDocument-> execute(array($validKey,1));
        $countDocument = $sqlDocument-> rowCount();

        //si on trouve pas le document associe
        if($countDocument != 1){
            $_SESSION['alertMessage'] = "Document non trouvé !";
            $_SESSION['alertMessageConfig'] = "warning";
            header("Location: /");
            exit();
        }else{

            $donneesDocument = $sqlDocument-> fetch();
            $numDevis = $donneesDocument['idDocument'];

            //Si pas encore de numero de transaction dans la base (PAS de Notification de PAYPLUG)
            if($donneesDocument['numero_facture'] == ""){
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
                $sqlUpdateDoc-> execute(array(2,$numeroFAC,$num_transaction,"page_valide",$commissionVente,$datePaiement,"CB",0,$donneesDocument['idDocument']));
            }

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
        unset($_SESSION['payment_id']);
        include_once("../../commun/bas_de_page.php");
        }//fin de document trouvé ok
    }else{ //fin de transaction OK
        header("Location: /PAIEMENT/PAYPLUG/finDeTransaction-abandon.php");
        exit();  
    }
}else{
    $_SESSION['alertMessage'] = "Transaction terminée !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /");
    exit();  
}
?>
