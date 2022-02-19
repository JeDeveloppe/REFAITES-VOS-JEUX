<?php
@session_start ();

// On vérifie que la méthode POST est utilisée
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    require_once("../../../controles/fonctions/validation_donnees.php");
    require_once('../../../config.php');
    require_once('../../../bdd/connexion-bdd.php');
    require_once("../../../bdd/table_config.php");
    $tva = $donneesConfig[6]['valeur'];
    $port = valid_donnees($_POST['port']);
    $adhesion = valid_donnees($_POST['association']);
    if(isset($_POST['expeditionOption'])){
        $expeditionOption = valid_donnees($_POST['expeditionOption']);
    }

    if($port != ""){
        if($port == "retrait_caen1"){
            $valueExpeditionOption = "non";
        }else{
            $valueExpeditionOption = $expeditionOption;
        }
    } 


    // On vérifie si le champ "recaptcha-response" contient une valeur
    // if(empty($_POST['recaptcha-response'])){
    //     $_SESSION['alertMessage'] = "Captcha Google vide... Veuillez ré-essayer !";
    //     $_SESSION['alertMessageConfig'] = "warning";
    //     header('Location: '.$_SERVER['HTTP_REFERER']);
    //     exit();
    // }else{
        require("../../../config.php");

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
        // if(empty($response) || is_null($response)){
        //     $_SESSION['alertMessage'] = "Pas de réponse Captcha Google... ";
        //     $_SESSION['alertMessageConfig'] = "warning";
        //     header('Location: '.$_SERVER['HTTP_REFERER']);
        //     exit();
        // }else{
            // $data = json_decode($response);
            // if($data->success){

                //A CE NIVEAU TOUT EST OK ON PREPARE LA BDD POUR CREER UN DEVIS ET ON ENVOI LE MAIL A L' UTILISATEUR
                require("../../../config.php");
                require("../../../bdd/connexion-bdd.php");
                require("../../../bdd/table_config.php");

                $sqlListeMessages = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND qte > ?");
                $sqlListeMessages-> execute(array($_SESSION['sessionId'],0));
                $donneesListeMessages = $sqlListeMessages->fetch();

                $sqlListeMessagesTab = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND qte > ?");
                $sqlListeMessagesTab-> execute(array($_SESSION['sessionId'],0));
                $donneesListeMessagesTab = $sqlListeMessagesTab->fetchAll();

                $totalHT = 0;
                while($donneesListeMessages){ 
                       //information du jeu complet
                        $requeteJeuxComplet = "SELECT * FROM jeux_complets WHERE idJeuxComplet = ".$donneesListeMessages['idJeu'];
                        $sqlJeuxComplet = $bdd -> query($requeteJeuxComplet);
                        $donneesJeuxComplet = $sqlJeuxComplet-> fetch();

                    $totalHT += $donneesJeuxComplet['prixHT'] * $donneesListeMessages['qte'];
                    $donneesListeMessages = $sqlListeMessages->fetch();
                }
            
                $totalHTavecAdhesion = $totalHT + $adhesion;
                $totalTTC = ($totalHT+ $adhesion) * $tva;
                $adhesionRVJTTC = $adhesion * $tva;
                $TVA = $totalTTC - $totalHTavecAdhesion;


                //A VOIR...
                // $sqlSaveClient = $bdd -> prepare("UPDATE users SET port = ?, optionExpedition = ? WHERE validKey = ?");
                // $sqlSaveClient-> execute(array($port,$valueExpeditionOption,$_SESSION['sessionId']));

                //****************$ CREATION DU DEVIS **************

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
                        $verifValidKey = $bdd -> query("SELECT * FROM documents WHERE validKey = '$validKey' ");
                        $donneesValidKey = $verifValidKey -> fetch();
                    }
                    while(is_array($donneesValidKey));


                    $sqlCreationDevis = $bdd-> prepare("INSERT INTO documents (idUser,validKey, prix_preparation,expedition,prix_expedition,commentaire,totalHT,totalTVA,totalTTC,time) VALUES (:client, :key, :prixP, :expe, :prixE, :com, :ht, :tva, :ttc, :time)");
                    $sqlCreationDevis->execute(array(
                        "client" => $_SESSION['userId'],
                        "key" => $validKey,
                        "prixP" => number_format($adhesion/100,2,".",""),
                        "expe" => $port,
                        "prixE" => "0.00",
                        "com" => "ACHAT RVJC",
                        "ht" => number_format($totalHTavecAdhesion/100,2,"."," "),
                        "tva" => number_format(($TVA/100),2,".",""),
                        "ttc" => number_format(($totalTTC/100),2,".",""),
                        "time" => time()));

                    //on recupere le dernier enregistrement
                    $devisCree = $bdd->lastInsertId();


                    //on incremente le numero
                    require_once("../../../controles/fonctions/incrementation.php");
                    $numero = incrementation($donneesConfig[7]['valeur'],$chiffreDocument);

                    //fin de validation du document
                    $fin_validation = time () + 86400;   //+4 temps entre enregistrement en envoi...

                    //on met a jour le numero du devis
                    $sqlUpdateNumeroDevis = $bdd->prepare("UPDATE documents SET numero_devis = ?, annee = ?, end_validation = ? WHERE idDocument = ?");
                    $sqlUpdateNumeroDevis-> execute(array($numero,$anneeCivil,$fin_validation,$devisCree));

                    //on supprime la demande d'origine
                    $sqlListeMessagesDelete = $bdd -> prepare("DELETE FROM listeMessages WHERE idUser = ? AND qte > ?");
                    $sqlListeMessagesDelete-> execute(array($_SESSION['sessionId'],0));


                    //pour chaque ligne on vérifie les champs et on mets dans la table documents_lignes
                      
                    foreach($donneesListeMessagesTab as $jeu){
                        $sqlCoquilleJeu = $bdd->prepare("SELECT * FROM catalogue WHERE idCatalogue = (SELECT idCatalogue FROM  jeux_complets WHERE idJeuxComplet = ? )");
                        $sqlCoquilleJeu->execute(array($jeu['idJeu']));
                        $donneesCoquille = $sqlCoquilleJeu->fetch();

                        $sqlJeuCompletSearch = $bdd->prepare("SELECT * FROM jeux_complets WHERE idJeuxComplet = ? ");
                        $sqlJeuCompletSearch->execute(array($jeu['idJeu']));
                        $donneesJeuCompletSearch = $sqlJeuCompletSearch->fetch();

                        $sqlInsertLignesDocument = $bdd -> prepare("INSERT INTO documents_lignes (idDocument,idJeu,question,reponse, prix) VALUES (?,?,?,?,?)");
                        $sqlInsertLignesDocument-> execute(array($devisCree,$donneesCoquille['idCatalogue'],$donneesCoquille['nom'],$donneesJeuCompletSearch['etatBoite']."|".$donneesJeuCompletSearch['etatMateriel']."|".$donneesJeuCompletSearch['regleJeu'],number_format($jeu['tarif']/100,2,".","")));
                    }

                //affichage dans la domande en fonction des options choisi
                if($port == "retrait_caen1"){
                    $tdRetraitLivraison = "Retrait à cette adresse:<p>".$donneesConfig[9]['valeur']."</p>";
                }else{
                    if($expeditionOption == "mondialrelay"){
                        $tdRetraitLivraison = "Envoi à mon domicile.<br/><b>Pour un colis: envoi avec Mondial Relay</b>";
                    }else{
                        $tdRetraitLivraison = "Envoi à mon domicile.<br/><b>Pour un colis: envoi avec Colissimo</b>";
                    }
                }

                
                //DEBUT DU TRAITEMENT DU MAIL
                $sqlUpdateTimeSendMail = $bdd-> prepare("UPDATE documents SET time_mail_devis = ? WHERE numero_devis = ?");
                $sqlUpdateTimeSendMail-> execute(array(time(),$devisCree));
                

                $sqlDevis = $bdd->prepare("SELECT * FROM documents WHERE numero_devis =  ? ");
                $sqlDevis->execute(array($numero));
                $donneesDevis = $sqlDevis->fetch();

                $sqlLignesDocument = $bdd->prepare("SELECT * FROM documents_lignes WHERE idDocument = ?");
                $sqlLignesDocument->execute(array($donneesDevis['idDocument']));
                $donneesLignesDocument = $sqlLignesDocument->fetchAll();

                $sqlClient = $bdd -> prepare("SELECT * FROM users WHERE validKey = ?");
                $sqlClient-> execute(array($_SESSION['sessionId']));
                $donneesClient = $sqlClient->fetch();

                switch($donneesDevis['expedition']){
                    case 'mondialRelay':
                        $texteExpedition = "Expédition par Mondial Relay";
                    break;
                    case 'colissimo':
                        $texteExpedition = "Expédition par Colissimo";
                    break;
                    case 'retrait_caen1':
                        $texteExpedition = "Retrait à la Coop 5 pour 100 Caen";
                    break;
                    case 'poste':
                        $texteExpedition = "Expédition par La Poste";
                    break;
                }

                //CONTENUE DU MAIL
                $contentDevis = '
                <!-- LINE -->
                <!-- Set line color -->
                <tr>
                    <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;" class="line">
                        <p>DEVIS '.$numero.' (Valable jusqu\'au '.date("d.m.Y à G:i",$donneesDevis['end_validation']).')</p>
                        <hr color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
                    </td>
                </tr>';


                $contentDevis .= '
                <!-- PARAGRAPH -->
                <!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 560px; font-size: 12px; font-weight: 400; line-height: 100%;
                        padding-top: 25px; padding-bottom: 25px;
                        color: #000000;
                        font-family: sans-serif;" class="paragraph">
                            <table border="0" cellpadding="0" cellspacing="0" align="center"
                            bgcolor="#FFFFFF"
                            width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                            max-width: 560px; margin-top:5px" class="container">
                            <th valign="top" style="padding-top: 10px">Adresse de facturation:</th>
                            <tr>
                            <td align="left" style="padding-top: 10px; padding-left: 50%;">'.$donneesClient['nom'].' '.$donneesClient['prenom'].'<br/>'.$donneesClient['adresse'].'<br/>'.$donneesClient['cp'].' '.$donneesClient['ville'].'</td>
                            </tr>
                            </table>
                    </td>
                </tr>';

                $contentDevis .= '
                <!-- PARAGRAPH -->
                <!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 500px; font-size: 12px; font-weight: 400; line-height: 100%;
                        padding-top: 5px; padding-bottom: 5px;
                        color: #000000;
                        font-family: sans-serif;" class="paragraph">
                            <table border="1rem solid" cellpadding="0" cellspacing="0" align="center"
                            width="500" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                            max-width: 500px; margin-top:5px" class="container">
                            <th valign="middle" style="padding-top: 10px; padding-bottom: 10px; width: 80%; bgcolor=#AFFFFF ">Souhait d\'achat</th>
                            <th align="center" valign="top" style="padding-top: 10px; padding-bottom: 10px;">Prix HT</th>';

                foreach($donneesLignesDocument as $ligne){
                    $sqlJeu = $bdd-> query("SELECT * FROM catalogue WHERE idCatalogue = ".$ligne['idJeu']);
                    $donneesJeu = $sqlJeu->fetch();
                    $jeuxComplets = explode("|",$ligne['reponse']);

                    $contentDevis .='     
                    <tr>
                    <td align="left" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 80%; font-size: 14px;
                        padding-top: 10px; padding-bottom: 10px;
                        color: #000000;
                        font-family: sans-serif;">
                            <p>Le jeu '.$ligne['question'].':</p>                            
                            <p>État de la boite: '.$jeuxComplets[0].'</p>
                            <p>État du matériel: '.$jeuxComplets[1].'</p>
                            <p>Règle du jeu: '.$jeuxComplets[2].'</p>                        
                    </td>
                    <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 20%; font-size: 14px;
                    padding-top: 10px; padding-bottom: 10px;
                    color: #000000;
                    font-family: sans-serif;">
                            '.$ligne['prix'].'
                    </td>
                    </tr>'; 
                }

                $contentDevis .= '
                    <tr>
                        <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 2%; width: 40%; font-size: 14px; font-weight: bold;
                            padding-top: 10px; padding-bottom: 10px;
                            color: #000000;
                            font-family: sans-serif;" class="header">
                                Adhésion:
                        </td>
                        <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px;
                            padding-top: 10px; padding-bottom: 10px;
                            color: #000000;
                            font-family: sans-serif;" class="header">
                                    '.$donneesDevis['prix_preparation'].'
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 2%; width: 40%; font-size: 14px; font-weight: bold;
                            padding-top: 10px; padding-bottom: 10px;
                            color: #000000;
                            font-family: sans-serif;" class="header">
                                '.$texteExpedition.':
                        </td>
                        <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px;
                                padding-top: 10px; padding-bottom: 10px;
                                color: #000000;
                                font-family: sans-serif;" class="header">
                                '.$donneesDevis['prix_expedition'].'
                        </td>
                    </tr>
                </table></td></tr>';


                if($donneesDevis['totalTVA'] == ""){
                    $texteTVA = "0.00";
                }else{
                    $texteTVA = $donneesDevis['totalTVA'];
                }
                // PARTIE TOTAUX
                $contentDevis .= '
                <!-- PARAGRAPH -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 260px; font-size: 12px; font-weight: 400; line-height: 100%;
                        padding-top: 10px; padding-bottom: 10px;
                        color: #000000;
                        font-family: sans-serif;" class="paragraph">
                            <table border="1" cellpadding="0" cellspacing="0" align="center"
                            bgcolor="#FFFFFF"
                            width="260" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                            max-width: 260px;" class="container">
                            <tr>
                            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 53%; font-size: 14px;
                            padding-top: 10px; padding-bottom: 10px;
                            color: #000000;
                            font-family: sans-serif;" class="header">TOTAL HT:
                            </td>
                            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px;
                            padding-top: 10px; padding-bottom: 10px;
                            color: #000000;
                            font-family: sans-serif;" class="header">
                                    '.$donneesDevis['totalHT'].'
                            </td>
                            </tr>
                            <tr>
                            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px;
                            padding-top: 10px; padding-bottom: 10px;
                            color: #000000;
                            font-family: sans-serif;" class="header">TVA:
                            </td>
                            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px;
                            padding-top: 10px; padding-bottom: 10px;
                            color: #000000;
                            font-family: sans-serif;" class="header">
                                    '.$texteTVA.'
                            </td>
                            </tr>
                            <tr>
                            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px; font-weight:bold;
                            padding-top: 10px; padding-bottom: 10px;
                            color: #000000;
                            font-family: sans-serif;" class="header">TOTAL TTC:
                            </td>
                            <td align="right" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 40%; font-size: 14px; font-weight:bold;
                            padding-top: 10px; padding-bottom: 10px;
                            color: #000000;
                            font-family: sans-serif;" class="header">
                                    '.$donneesDevis['totalTTC'].'
                            </td>
                            </tr>
                            </table>
                    </td>';



                // PARTIE TVA si 0 ou pas
                if($donneesConfig[6]['valeur'] == 1){
                $contentDevis .= '
                    <!-- PARAGRAPH -->
                    <tr>
                        <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 560px; font-size: 12px; font-weight: 400; line-height: 100%;
                            padding-top: 10px; padding-bottom: 10px;
                            color: #000000;
                            font-family: sans-serif;" class="paragraph">
                                <table border="0" cellpadding="0" cellspacing="0" align="center"
                                bgcolor="#FFFFFF"
                                width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                                max-width: 560px;" class="container">
                                <tr>
                                <td align="left" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 53%; font-size: 14px;
                                padding-top: 10px; padding-bottom: 10px;
                                color: #000000;
                                font-family: sans-serif;" class="header">TVA non applicable, article 293B du code général des impôts. 
                                </td>
                                </tr>
                                </table>
                        </td>
                    </tr>';
                }

                // PARTIE DES BOUTONS
                $contentDevis .= '
                <!-- PARAGRAPH -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 500px; font-size: 12px; font-weight: 400; line-height: 100%;
                        padding-top: 10px; padding-bottom: 10px;
                        color: #000000;
                        font-family: sans-serif;" class="paragraph">
                            <table border="0" cellpadding="0" cellspacing="0" align="center"
                            bgcolor="#FFFFFF"
                            width="500" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                            max-width: 500px; margin-top:5px" class="container">
                            <tr>
                            <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 50%;
                                padding-top: 25px;
                                padding-bottom: 5px;" class="button">
                                <a href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/accept.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'" style="text-decoration: underline;" alt="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/accept.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'">
                                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 240px; min-width: 120px; border-collapse: collapse; border-spacing: 0; padding: 0;">
                                    <tr>
                                        <td align="center" valign="middle" style="padding: 12px 24px; margin: 0; text-decoration: underline; border-collapse: collapse; border-spacing: 0; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; -khtml-border-radius: 4px;"
                                        bgcolor="#28a745"><a target="_blank" style="text-decoration: underline;
                                        color: #FFFFFF; font-family: sans-serif; font-size: 17px; font-weight: 400; line-height: 100%;"
                                        href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/accept.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'">
                                            Accepter et payer le devis<br/>
                                        </a>
                                        </td>
                                    </tr>
                                    </table>
                                </a>
                            </td>
                            <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 50%;
                                padding-top: 25px;
                                padding-bottom: 5px;" class="button"><a
                                href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/refuse.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'" style="text-decoration: underline;">
                                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="max-width: 240px; min-width: 120px; border-collapse: collapse; border-spacing: 0; padding: 0;"><tr><td align="center" valign="middle" style="padding: 12px 24px; margin: 0; text-decoration: underline; border-collapse: collapse; border-spacing: 0; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; -khtml-border-radius: 4px;"
                                        bgcolor="#dc3545"><a target="_blank" style="text-decoration: underline;
                                        color: #FFFFFF; font-family: sans-serif; font-size: 17px; font-weight: 400; line-height: 100%;"
                                        href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/refuse.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'">
                                            Annuler le devis
                                        </a>
                                </td></tr></table></a>
                            </td>
                            </tr>
                            </table>
                    </td>
                </tr>';

                $contentDevis .= '
                <!-- PARAGRAPH -->
                <!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 500px; font-size: 12px; line-height: 100%;
                        padding-top: 5px; padding-bottom: 25px;
                        color: #000000;
                        font-family: sans-serif;" class="paragraph">
                            <table border="0" cellpadding="0" cellspacing="0" align="center"
                            bgcolor="#FFFFFF"
                            width="500" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                            max-width: 500px; margin-top:5px" class="container">
                            <tr>
                                <td align="left" style="padding: 10px;">
                                    <p>Si les boutons n\'apparaîssent pas:</p>
                                    <p><a href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/accept.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'">Cliquez sur ce lien si vous souhaitez accepter et payer le devis.</a></p>
                                    <p><a href="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/refuse.php?doc='.$donneesDevis['validKey'].'&user='.$donneesDevis['idUser'].'">Cliquez sur ce lien si vous souhaitez annuler.</a></p>
                                </td>
                            </tr>
                            </table>
                    </td>
                </tr>';

                $contentDevis .= '
                <!-- PARAGRAPH -->
                <!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 500px; font-size: 12px; line-height: 100%;
                        padding-top: 5px; padding-bottom: 25px;
                        color: #000000;
                        font-family: sans-serif;" class="paragraph">
                            <table border="0" cellpadding="0" cellspacing="0" align="center"
                            bgcolor="#FFFFFF"
                            width="500" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                            max-width: 500px; margin-top:5px" class="container">
                            <tr>
                            <td align="left" style="padding: 10px;"><p>INFORMATION:</p>Le moyen le plus simple reste le paiement par carte bancaire, mais si vous souhaitez un autre moyen de paiement, il vous suffit de répondre à ce mail et nous trouverons une solution !</td>
                            </tr>
                            </table>
                    </td>
                </tr>';

                $contentDevis .= '
                <!-- PARAGRAPH -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 560px; font-size: 12px; font-weight: 400; line-height: 100%;
                        padding-top: 10px; padding-bottom: 10px;
                        color: #000000;
                        font-family: sans-serif;" class="paragraph">
                            <table border="0" cellpadding="0" cellspacing="0" align="center"
                            bgcolor="#FFFFFF"
                            width="560" style="border-collapse: collapse; border-spacing: 0; padding: 0; width: inherit;
                            max-width: 560px;" class="container">
                            <tr>
                            <td align="center" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 53%; font-size: 14px;
                            padding-top: 10px; padding-bottom: 10px;
                            color: #000000;
                            font-family: sans-serif;" class="header">
                            <a target="_blank" style="text-decoration: none;"
                                href="'.$GLOBALS['urlService'].'"><img border="0" vspace="0" hspace="0"
                                src="'.$GLOBALS['domaine'].'/PAIEMENT/'.$GLOBAL['servicePaiement'].'/logo-'.$GLOBAL['servicePaiement'].'.png"
                                width="300" height="auto"
                                alt="logo du service de paiement" title="Payement sécurisé" style="
                                color: #000000;
                                font-size: 10px; margin: 0; padding: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: block;" /></a>
                            </td>
                            </tr>
                            </table>
                    </td>
                </tr>';

                //TRAITEMENT DU MAIL ET REDIRECTION
                require_once("../../../mails/mail_envoiDevis.php");
            
            // }
        // }
    // }
}else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /accueil/");  
}
?>