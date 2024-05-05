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
            $sqlDocument = $bdd -> prepare("SELECT * FROM documents WHERE validKey = ? AND etat = ?");
            $sqlDocument-> execute(array($validKey,1));
            $countDocument = $sqlDocument-> rowCount();
            //si on trouve le document associe
            if($countDocument == 1){

                $donneesDocument = $sqlDocument-> fetch();
                $numDevis = $donneesDocument['idDocument'];

                //SI Y A PAS ENCORE DE NUMERO DE FACTURE C'EST QUE UTILISATEUR A QUITTER APRES LE PAIEMENT SANS RETOUR SUR LE SITE
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

                    $sqlUpdateDoc = $bdd->prepare("UPDATE documents SET etat = ?, numero_facture = ?, num_transaction = ?, page_controle = ?, commission_vente = ?, time_transaction = ?, moyen_paiement = ?, envoyer = ? WHERE idDocument = ?");
                    $sqlUpdateDoc->execute(array(2,$numeroFAC,$num_transaction,"page_notif",$commissionVente,$datePaiement,"CB",0,$donneesDocument['idDocument']));
                    $factureGeneree =  " OK FACTURE GENEREE";

                    //mise a jour des jeux vendu
                    $sqlChercheLignesAchat = $bdd->prepare("SELECT * FROM documents_lignes_achats WHERE idDocument = ?");
                    $sqlChercheLignesAchat->execute((array($donneesDocument['idDocument'])));
                    $jeux = $sqlChercheLignesAchat->fetchAll();

                    foreach($jeux as $jeu){
                        $sqlUpdateJeuxComplet = $bdd->prepare('UPDATE jeux_complets SET vente = ?,timeVente = ?, actif = 0 WHERE idJeuxComplet = ?');
                        $sqlUpdateJeuxComplet->execute(array('|CB',$datePaiement,$jeu['idJeuComplet']));
                    }

                    $sqlUpdateClientAssociation = $bdd->prepare("UPDATE clients SET isAssociation = ? WHERE idClient = ?");
                    $sqlUpdateClientAssociation->execute(array($datePaiement+31536000,$donneesDocument['idUser']));
               

                    if($donneesDocument['expedition'] == "retrait_caen1"){
                        $sqlClient = $bdd->prepare("SELECT email FROM clients WHERE idClient = ?");
                        $sqlClient->execute(array($donneesDocument['idUser']));
                        $donneesClient = $sqlClient->fetch();

                        //CONTENUE DU MAIL
                        $contentMail = '
                        <!-- LINE -->
                        <!-- Set line color -->
                        <tr>
                            <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;" class="line">
                                <p>Confirmation de paiement.</p>
                                <hr color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
                            </td>
                        </tr>';


                        $contentMail .= '
                        <!-- PARAGRAPH -->
                        <!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
                        <tr>
                            <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 560px; font-size: 14px; font-weight: 400; line-height: 100%;
                                padding-top: 25px; padding-bottom: 25px;
                                color: #000000;
                                font-family: sans-serif;" class="paragraph">
                                <table border="0" cellpadding="0" cellspacing="0" align="center"
                                bgcolor="#FFFFFF"
                                width="500" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                                max-width: 500px; margin-top:5px" class="container">
                                <tr>
                                <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 50%;
                                    padding-top: 5px;
                                    padding-bottom: 5px;" class="button">
                                    Bonjour,<p>Nous vous remerçions pour votre paiement.</p>
                                    <p>Votre commande sera déposée à la Coop 5 pour 100 sous 7 jours.</p>
                                    <p>Un email vous sera envoyé lorsqu\'elle sera déposée.</p>
                                    <p>Bonne journée</p>
                                </td>
                                </tr>
                                </table>
                            </td>
                        </tr>';

                        require_once('../../mails/mail_envoiConfirmationPaiementRetrait.php');
                    }
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