<?php
@session_start ();

// On vérifie que la méthode POST est utilisée
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    require_once("../../controles/fonctions/validation_donnees.php");
    require_once("../../config.php");
    require_once('../../bdd/connexion-bdd.php');
    require_once("../../bdd/table_config.php");

    //on cherche tout du client
    $sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idUser = ?");
    $sqlClient-> execute(array($_SESSION['sessionId']));
    $donneesClient = $sqlClient->fetch();

    if($donneesClient['isAssociation'] > time()){
        $prixPreparation = 0;
    }else{
        $prixPreparation = $donneesConfig[28]['valeur']*100;
    }

    //AUTOMATIQUEMENT RETRAIT PAS DE CHOIX POSSIBLE
    $port = "retrait_caen1";
    $expedition = "non";

    //on met a jour options envoi ou retrait
    $sqlEnvoiRetrait = $bdd->prepare("UPDATE clients SET port = ?, optionExpedition = ? WHERE idUser = ?");
    $sqlEnvoiRetrait->execute(array($port,$expedition,$_SESSION['sessionId']));

 
    //on verifie que toute l'adresse de facturation est saisie
    if($donneesClient['adresseFacturation'] == NULL || $donneesClient['cpFacturation'] == NULL || $donneesClient['villeFacturation'] == NULL || $donneesClient['nomFacturation'] == NULL || $donneesClient['prenomFacturation'] == NULL){
        $_SESSION['alertMessage'] = "Adresse de facturation non complète !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }
 
    // On vérifie si le champ "recaptcha-response" contient une valeur
    if(empty($_POST['recaptcha-response'])){
        $_SESSION['alertMessage'] = "Captcha Google vide... Veuillez ré-essayer !";
        $_SESSION['alertMessageConfig'] = "warning";
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();
    }else{
        require("../../config.php");

        // On prépare l'URL
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$GLOBAL['cleSecreteGoogleCaptcha']."&response={$_POST['recaptcha-response']}";

        // On vérifie si curl est installé
        if(function_exists('curl_version')){
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
        }else{
            // On utilisera file_get_contents
            $response = file_get_contents($url);
        }

        // On vérifie qu'on a une réponse
        if(empty($response) || is_null($response)){
            $_SESSION['alertMessage'] = "Pas de réponse Captcha Google... ";
            $_SESSION['alertMessageConfig'] = "warning";
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit();
        }else{
            $data = json_decode($response);
            if($data->success){

                //A CE NIVEAU TOUT EST OK ON PREPARE LA BDD POUR CREER UN DEVIS ET ON ENVOI LE MAIL A L' UTILISATEUR
                require("../../config.php");
                require("../../bdd/connexion-bdd.php");
                require("../../bdd/table_config.php");

                //on recupere tout du panier (que des achats apriori)
                $sqlListeMessages = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND qte > 0");
                $sqlListeMessages-> execute(array($_SESSION['sessionId']));
                $donneesListeMessages = $sqlListeMessages-> fetchAll();

                //affichage dans la domande en fonction des options choisi
                $tdRetraitLivraison = "Retrait à cette adresse:<p>".$donneesConfig[9]['valeur']."</p>";

                //ICI ON CREE LE DEVIS
                //on recupere l'année en cours au moment de l'enregistrement
                $anneeCivil = date("Y", time());

                //on cherche le dernier enregistrement
                $sqlDernierEnregistrement = $bdd -> prepare("SELECT * FROM documents WHERE annee = ? AND numero_devis LIKE ? ORDER BY numero_devis DESC LIMIT 1");
                $sqlDernierEnregistrement-> execute(array($anneeCivil,$donneesConfig[7]['valeur']."%"));
                $donneesLastRow = $sqlDernierEnregistrement-> fetch();
                $nbRow = $sqlDernierEnregistrement-> rowCount();

                if($nbRow == 0){  //pas encore d'enregistrement
                    $chiffreDocument = 1;
                }else{
                    $lastAnneeEnCours = $donneesLastRow['annee'];
                        if($lastAnneeEnCours == $anneeCivil){
                            $rest = substr($donneesLastRow['numero_devis'], -4);
                            $chiffreDocument = $rest + 1;
                        }else{
                            $chiffreDocument = 1;
                        }
                }

                //validKey aléatoire
                function random_strings($length_of_string) 
                { 
                    // String of all alphanumeric character 
                    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@'; 

                    // Shufle the $str_result and returns substring 
                    // of specified length 
                    return substr(str_shuffle($str_result),0, $length_of_string); 
                } 

                //on creer une clefUnique tant que cela n'existe pas dans la base
                do{
                    $validKey = random_strings(64);
                    $verifValidKey = $bdd-> query("SELECT validKey FROM documents WHERE validKey = '$validKey' ");
                    $donneesValidKey = $verifValidKey -> rowCount();
                }
                while($donneesValidKey = 0);


                $totalAchats = $_SESSION['totalAchats'] * 100;

                //nouveau champs organisme
                if(empty($donneesClient['organismeFacturation']) || $donneesClient['organismeFacturation'] == null){
                    $organismeFacturation = "";
                }else{
                    $organismeFacturation = $donneesClient['organismeFacturation'].'<br/>';
                }


                $sqlCreationDevis = $bdd-> prepare("INSERT INTO documents (idUser,validKey, prix_preparation,expedition,prix_expedition,totalOccasions,commentaire,totalHT,totalTVA,totalTTC,time,envoyer,demande_achat,adresse_facturation,adresse_livraison) VALUES (:client, :key, :prixP, :expe, :prixE, :totalOccasions, :com, :ht, :tva, :ttc, :time, :envoyer, :dem_ach, :adr_fac, :adr_liv)");
                $sqlCreationDevis->execute(array(
                    "client" => $donneesClient['idClient'],
                    "key" => $validKey,
                    "prixP" => $prixPreparation,
                    "expe" => $port,
                    "prixE" => 0,
                    "envoyer" => 0,
                    "dem_ach" => "A",
                    "totalOccasions" => $totalAchats,
                    "com" => "MERCI POUR VOTRE COMMANDE",
                    "adr_fac" => $organismeFacturation.$donneesClient['nomFacturation'].' '.$donneesClient['prenomFacturation'].'<br/>'.$donneesClient['adresseFacturation'].'<br/>'.$donneesClient['cpFacturation'].' '.$donneesClient['villeFacturation'].'<br/>'.$donneesClient['paysFacturation'],
                    "adr_liv" => $donneesConfig[9]['valeur'],
                    "ht" => $_SESSION['ht'] * 100,
                    "tva" => $_SESSION['tva'] * 100,
                    "ttc" => $_SESSION['ttc'] * 100,
                    "time" => time()));

                //on recupere le dernier enregistrement
                $devisCree = $bdd->lastInsertId();


                //on incremente le numero
                require_once("../../controles/fonctions/incrementation.php");
                $numero = incrementation($donneesConfig[7]['valeur'],$chiffreDocument);

                //fin de validation du document
                $fin_validation = time () + $donneesConfig[11]['valeur'] + 4;   //+4 temps entre enregistrement en envoi...

                //on met a jour le numero du devis
                $sqlUpdateNumeroDevis = $bdd->prepare("UPDATE documents SET numero_devis = ?, annee = ?, end_validation = ? WHERE idDocument = ?");
                $sqlUpdateNumeroDevis-> execute(array($numero,$anneeCivil,$fin_validation,$devisCree));

                //on supprime la demande d'origine    
                $sqlListeMessagesUpdateDevis = $bdd -> prepare("DELETE FROM listeMessages WHERE idUser = ? AND qte > 0");
                $sqlListeMessagesUpdateDevis-> execute(array($_SESSION['sessionId']));



                //pour chaque ligne on vérifie les champs et on mets dans la table documents_lignes_achats

                foreach($donneesListeMessages as $ligneAchat){
                    $sqlToutDujeuComplet = $bdd->prepare("SELECT * FROM jeux_complets WHERE idJeuxComplet = ?");
                    $sqlToutDujeuComplet->execute(array($ligneAchat['idJeu']));
                    $donneesToutDuJeuComplet = $sqlToutDujeuComplet->fetch();
                    if($donneesToutDuJeuComplet['isNeuf'] == true){
                        $detailsJeuComplet =  'COMME NEUF';
                    }else{
                        $detailsJeuComplet =  'État de la boite: '.$donneesToutDuJeuComplet['etatBoite'].'<br/>État du matériel: '.$donneesToutDuJeuComplet['etatMateriel'].'<br/>Règle du jeu: '.$donneesToutDuJeuComplet['regleJeu']; 
                    }

                    $sqlInsertLignesDocumentAchat = $bdd -> prepare("INSERT INTO documents_lignes_achats (idDocument,idJeuComplet,idCatalogue,detailsComplet,qte,prix) VALUES (?,?,?,?,?,?)");
                    $sqlInsertLignesDocumentAchat-> execute(array($devisCree,$ligneAchat['idJeu'],$donneesToutDuJeuComplet['idCatalogue'],$detailsJeuComplet,$ligneAchat['qte'],$ligneAchat['tarif']));
                }

                //ICI REDIRECTION VERS PAGE DE PAIEMENT
                $urlPaiement = $GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/accept.php?doc='.$validKey.'&user='.$donneesClient['idClient'];
                header("Location: ".$urlPaiement);
                exit();  

   

                $contentDemandeDevis ='
                <!-- LINE -->
                <!-- Set line color -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;" class="line">
                        <p>DETAILS DE VOTRE COMMANDE du '.date("d-m-Y",time()).'</p>
                        <hr color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
                    </td>
                </tr>';

                $contentDemandeDevis .='
                <!-- LIST -->
                <tr>
                    <td align="center" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%;" class="list-item"><table align="center" border="0" cellspacing="0" cellpadding="0" style="width: inherit; margin: 0; padding: 0; border-collapse: collapse; border-spacing: 0;">';

                foreach($donneesListeMessages as $ligne){
                    //on recupere tout de la boite de jeu
                    $sqlJeux = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = ".$ligne['idJeu']);
                    $donneesJeux = $sqlJeux -> fetch();
                    //on cherche l'image du jeu
                    $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$ligne['idJeu']);
                    $donneesImage = $sqlImage->fetch();

                    //on recupere les images d'exemple s'il y en a
                    $sqlImageExemple = $bdd -> query("SELECT * FROM listeMessages_images WHERE idListeMessages = ".$ligne['idListeMessages']);
                    $countImageExemple = $sqlImageExemple->rowCount();

                    if($countImageExemple < 1){
                        $tdImageFournie = '<td colspan="2" align="center" valign="top" style="font-size: 14px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
                        padding-top: 5px;
                        color: #dc3545;
                        font-family: sans-serif;" class="paragraph"> Image d\'exemple non fournie.</td>';
                    }elseif($countImageExemple == 1){
                        $tdImageFournie = '<td colspan="2" align="center" valign="top" style="font-size: 14px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
                        padding-top: 5px;
                        color: #28a745;
                        font-family: sans-serif;" class="paragraph">2 images fournies.</td>';
                    }else{
                        $tdImageFournie = '<td colspan="2" align="center" valign="top" style="font-size: 14px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
                        padding-top: 5px;
                        color: #28a745;
                        font-family: sans-serif;" class="paragraph">1 image fournie.</td>';
                    }
            
                    $contentDemandeDevis .='     
                        <!-- LIST ITEM -->
                        <tr>

                            <!-- LIST ITEM IMAGE -->
                            <!-- Image text color should be opposite to background color. Set your url, image src, alt and title. Alt text should fit the image size. Real image size should be x2 -->
                            <td align="left" valign="middle" style="border-collapse: collapse; border-spacing: 0;
                                padding-top: 5px;
                                padding-right: 20px;"><img
                            border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: block;
                                color: #000000;" src="data:image/jpeg;base64,'.$donneesImage['image'].'"
                                alt="'.$donneesJeux['nom'].' - '.$donneesJeux['editeur'].'" title="'.$donneesJeux['nom'].'"
                                width="100" height="100"></td>

                            <!-- LIST ITEM TEXT -->
                            <!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
                            <td align="left" valign="top" style="font-size: 14px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
                                padding-top: 5px;
                                color: #000000;
                                font-family: sans-serif;" class="paragraph">
                                    <b style="color: #333333;">'.$donneesJeux['nom'].'<br/>'.$donneesJeux['editeur'].'<br/>'.$donneesJeux['annee'].'</b><br/>
                                    '.$ligne['message'].'
                            </td>

                        </tr>'.$tdImageFournie.'<tr>
                    </tr>'; 
                }

                $contentDemandeDevis .='</table></td>
                </tr>
                
                <!-- LINE -->
                <!-- Set line color -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;
                        padding-top: 25px; padding-bottom: 2px;" class="line"><hr
                        color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
                    </td>
                </tr>';
                
                $contentDemandeDevis .='
                <!-- PARAGRAPH -->
                <!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 560px; font-size: 12px; font-weight: 400; line-height: 160%;
                        padding-top: 25px; padding-bottom: 25px;
                        color: #000000;
                        font-family: sans-serif;" class="paragraph">
                            <table border="0" cellpadding="0" cellspacing="0" align="center"
                            bgcolor="#FFFFFF"
                            width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                            max-width: 560px; margin-top:5px" class="container">
                            <th valign="top" style="padding-top: 10px">Contact:</th>
                            <th valign="top"; style="padding-top: 10px">Envoi ou retrait:</th>
                            <tr>
                            <td align="center" style="padding-top: 10px">'.$_SESSION['nom'].' '.$_SESSION['prenom'].'<br/>'.$_SESSION['adresse'].'<br/>'.$_SESSION['cp'].' '.$_SESSION['ville'].'<br/>'.$_SESSION['telephone'].'<br/>'.$_SESSION['email'].'</td>
                            <td align="center" style="padding-top: 10px">'.$tdRetraitLivraison.'</td>
                            </tr>
                            </table>
                    </td>
                </tr>';

                $contentDemandeDevis .='
                <!-- PARAGRAPH -->
                <!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; font-size: 17px; font-weight: 400; line-height: 160%;
                        padding-top: 25px; padding-bottom: 25px;
                        color: #000000;
                        font-family: sans-serif;" class="paragraph">
                            Votre demande a bien été prise en compte.<br/>Elle sera traitée dans les meilleurs délais. 
                    </td>
                </tr>';

                //TRAITEMENT DU MAIL ET REDIRECTION
                require_once("../../mails/mail_confirmationDemandeDevis.php");
            
            }
        }
    }
}else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /");
    exit();  
}
?>