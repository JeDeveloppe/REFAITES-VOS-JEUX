<?php
@session_start (); 
require("../../controles/fonctions/adminOnline.php");

if(isset($_POST['methode']) && isset($_POST['doc']) && !isset($_SESSION['ctrl-manuel'])){  //$_SESSION['ctrl-manuel']empeche le rechargement de la page
    include_once("../../config.php");
    require("../../controles/fonctions/validation_donnees.php");

    //on recuperer la variable doc
    $doc = valid_donnees($_POST['doc']);
    $methode = valid_donnees($_POST['methode']);

        require('../../bdd/connexion-bdd.php');
        require('../../bdd/table_config.php');
        //IL FAUT RECUPERER NUMERO DU DEVIS
        $sqlDocument = $bdd -> prepare("SELECT * FROM documents WHERE idDocument = ? AND etat = ?");
        $sqlDocument-> execute(array($doc,1));   //devis obligatoire statut 1
        $countDocument = $sqlDocument-> rowCount();

        //si on trouve pas le document associe
        if($countDocument != 1){
            $_SESSION['alertMessage'] = "Document non trouvé !";
            $_SESSION['alertMessageConfig'] = "warning";
            header("Location: ".$_SERVER['HTTP_REFERER']);
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
                $num_transaction = "RefaitesVosJeuxManuel";
                $datePaiement = time();

                $sqlUpdateDoc = $bdd -> prepare("UPDATE documents SET etat = ?, numero_facture = ?, num_transaction = ?, page_controle = ?, commission_vente = ?, time_transaction = ?, moyen_paiement = ?, envoyer = ? WHERE idDocument = ?");
                $sqlUpdateDoc-> execute(array(2,$numeroFAC,$num_transaction,"manuel",0,$datePaiement,$methode,0,$donneesDocument['idDocument']));
            }

            $titreDeLaPage = "[Admin] Fin de la transaction";
            $descriptionPage = "";
            include_once("../../commun/haut_de_page.php");
            ?>
            <div class="container-fluid">
                <div class="row mt-5">
                    <div class="card p-0 col-11 col-md-8 col-lg-6 mt-4 mb-4 mx-auto">
                        <div class="card-header bg-dark text-white text-center">Confirmation</div>
                        <div class="card-body">
                            <div class="col-12 text-center align-middle"><i class="fas fa-check-square fa-2x text-success"></i> FACTURE GENEREE !<p>Reste à envoyer la commande !</p></div>
                            <div class="col-12 text-center">
                                Ne pas oubliez d'envoyer la facture en client... / Recherche de document / Facture numéro: <?php echo $numeroFAC; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        $_SESSION['ctrl-manuel'] = 1;
        include_once("../../commun/bas_de_page-admin.php");
        }//fin de document trouvé ok
    
}else{
    $_SESSION['alertMessage'] = "Protection rechargement de la page !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /admin/recherche-document/");
    exit();  
}
?>
