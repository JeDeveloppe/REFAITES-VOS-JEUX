<?php
@session_start ();

// On vérifie que la méthode POST est utilisée
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    require_once("../../controles/fonctions/validation_donnees.php");
    
    if(isset($_POST['port'])){
        $port = valid_donnees($_POST['port']);
    }else{
        $port = "retrait_caen1";
    }
    
    if(isset($_POST['expeditionOption'])){
        $expeditionOption = valid_donnees($_POST['expeditionOption']);
    }else{
        $expeditionOption = "non";
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

                //on met a jour options envoi ou retrait
                $sqlEnvoiRetrait = $bdd->prepare("UPDATE clients SET port = ?, optionExpedition = ? WHERE idUser = ?");
                $sqlEnvoiRetrait->execute(array($port,$expeditionOption,$_SESSION['sessionId']));

                $sqlListeMessages = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND qte IS NULL AND statut = ?");
                $sqlListeMessages-> execute(array($_SESSION['sessionId'],0));
                $donneesListeMessages = $sqlListeMessages-> fetchAll();

                //ON RECUPERE LES ACHATS
                $sqlListeMessagesAchat = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND qte > 0 AND statut = ?");
                $sqlListeMessagesAchat-> execute(array($_SESSION['sessionId'],0));
                $donneesListeMessagesAchat = $sqlListeMessagesAchat-> fetchAll();

                $sqlClient = $bdd-> prepare("SELECT * FROM clients WHERE idUser = ?");
                $sqlClient-> execute(array($_SESSION['sessionId']));
                $donneesClient = $sqlClient->fetch();
                $emailMembre = $donneesClient['email'];
      

                //pour affichage dans accueil admin et surtout suppression des messages au statut 0 -> 5 jours (voir verifConnexion)
                $sqlListeMessagesUpdateDevis = $bdd -> prepare("UPDATE listeMessages SET statut = 1 WHERE idUser = ?");
                $sqlListeMessagesUpdateDevis-> execute(array($_SESSION['sessionId']));


                //affichage dans la domande en fonction des options choisi
                if($port == "retrait_caen1"){
                    $tdRetraitLivraison = "Retrait à cette adresse:<p>".$donneesConfig[9]['valeur']."</p>";
                }else{
                    $tdRetraitLivraison = "Envoi à cette adresse:<p>".$donneesClient['nomLivraison']." ".$donneesClient['prenomLivraison']."<br/>".$donneesClient['adresseLivraison']."<br/>".$donneesClient['cpLivraison']." ".$donneesClient['villeLivraison']."<br/>".$donneesClient['paysLivraison']."</p>";  
                }

                $contentDemandeDevis ='
                <!-- LINE -->
                <!-- Set line color -->
                <tr>
                    <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;" class="line">
                        <p>DETAILS DE VOTRE DEMANDE du '.date("d-m-Y",time()).'</p>
                        <hr color="#E0E0E0" align="center" width="100%" size="1" noshade style="margin: 0; padding: 0;" />
                    </td>
                </tr>';

                $contentDemandeDevis .='
                <!-- LIST -->
                <tr>
                    <td align="center" valign="middle" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%;" class="list-item"><table align="center" border="0" cellspacing="0" cellpadding="0" style="width: inherit; margin: 0; padding: 0; border-collapse: collapse; border-spacing: 0;">';

               
                //lignes des achats
                if(count($donneesListeMessagesAchat) > 0){
                    foreach($donneesListeMessagesAchat as $ligneAchat){
                        //on recupere tout de la boite de jeu
                        $sqlJeuxComplet = $bdd->query("SELECT * FROM jeux_complets WHERE idJeuxComplet = ".$ligneAchat['idJeu']);
                        $donneesJeuxComplet = $sqlJeuxComplet->fetch();
                        if($donneesJeuxComplet['isNeuf'] == true){
                            $detailsJeuComplet =  'COMME NEUF';
                        }else{
                            $detailsJeuComplet =  'État de la boite: '.$donneesJeuxComplet['etatBoite'].'<br/>État du matériel: '.$donneesJeuxComplet['etatMateriel'].'<br/>Règle du jeu: '.$donneesJeuxComplet['regleJeu']; 
                        }
                        //on recupere tout du catalogue
                        $sqlJeux = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = ".$donneesJeuxComplet['idCatalogue']);
                        $donneesJeux = $sqlJeux -> fetch();

                        $contentDemandeDevis .='     
                        <!-- LIST ITEM -->
                        <tr>

                            <!-- LIST ITEM IMAGE -->
                            <!-- Image text color should be opposite to background color. Set your url, image src, alt and title. Alt text should fit the image size. Real image size should be x2 -->
                            <td align="left" valign="middle" style="border-collapse: collapse; border-spacing: 0;
                                padding-top: 5px;
                                padding-right: 20px;">
                                <img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: block;
                                color: #000000;" src="data:image/jpeg;base64,'.$donneesJeux['imageBlob'].'"
                                alt="'.$donneesJeux['nom'].' - '.$donneesJeux['editeur'].'" title="'.$donneesJeux['nom'].'"
                                width="100" height="100">
                            </td>

                            <!-- LIST ITEM TEXT -->
                            <!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
                            <td align="left" valign="top" style="font-size: 14px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
                                padding-top: 5px;
                                color: #000000;
                                font-family: sans-serif;" class="paragraph">
                                    <b style="color: #333333;">'.$detailsJeuComplet.'</b>
                            </td>

                        </tr>
                        <tr>
                    </tr>'; 
                    }
                }


                foreach($donneesListeMessages as $ligne){
                    //on recupere tout de la boite de jeu
                    $sqlJeux = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = ".$ligne['idJeu']);
                    $donneesJeux = $sqlJeux -> fetch();

                    //on recupere les images d'exemple s'il y en a
                    $sqlImageExemple = $bdd -> query("SELECT * FROM listeMessages_images WHERE idListeMessages = ".$ligne['idListeMessages']);
                    $countImageExemple = $sqlImageExemple->rowCount();

                        if($countImageExemple < 1){
                            $textImageFournie = "Image d'exemple non fournie.";
                        }elseif($countImageExemple == 1){
                            $textImageFournie = "2 images fournies.";
                        }else{
                            $textImageFournie = "1 image fournie.";
                        }
                        $tdImageFournie = '<td colspan="2" align="center" valign="top" style="font-size: 14px; font-weight: 400; line-height: 160%; border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;
                        padding-top: 5px;
                        color: #dc3545;
                        font-family: sans-serif;" class="paragraph">'.$textImageFournie.'</td>';
            
                    $contentDemandeDevis .='     
                        <!-- LIST ITEM -->
                        <tr>

                            <!-- LIST ITEM IMAGE -->
                            <!-- Image text color should be opposite to background color. Set your url, image src, alt and title. Alt text should fit the image size. Real image size should be x2 -->
                            <td align="left" valign="middle" style="border-collapse: collapse; border-spacing: 0;
                                padding-top: 5px;
                                padding-right: 20px;"><img
                            border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: block;
                                color: #000000;" src="data:image/jpeg;base64,'.$donneesJeux['imageBlob'].'"
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
                            <td align="center" style="padding-top: 10px">'.$donneesClient['nomFacturation'].' '.$donneesClient['prenomFacturation'].'<br/>'.$donneesClient['adresseFacturation'].'<br/>'.$donneesClient['cpFacturation'].' '.$donneesClient['villeFacturation'].'<br/>'.$donneesClient['paysFacturation'].'</td>
                            <td align="center" style="padding-top: 10px">'.$tdRetraitLivraison.'</td>
                            </tr>
                            </table>
                    </td>
                </tr>';

                $contentDemandeDevis .='
                <!-- PARAGRAPH -->
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