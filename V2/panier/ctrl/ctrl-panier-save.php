<?php
@session_start ();

// On vérifie que la méthode POST est utilisée
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    require_once("../../controles/fonctions/validation_donnees.php");
            
    print_r($_POST);
    exit();
    
    $prenom = valid_donnees($_POST['prenom']);
    $nom = valid_donnees($_POST['nom']);
    $adresse = valid_donnees($_POST['adresse']);
    $cp = valid_donnees($_POST['cp']);
    $ville = valid_donnees($_POST['ville']);
    $pays = valid_donnees($_POST['pays']);
    $email = valid_donnees($_POST['email']);
    $telephone = valid_donnees($_POST['telephone']);
    $port = valid_donnees($_POST['port']);
    if(isset($_POST['expeditionOption'])){
        $expeditionOption = valid_donnees($_POST['expeditionOption']);
    }
    //REMETTRE SI PAS DE CAPTCHA GOOGLE
    //$reponse = valid_donnees($_POST['reponse']);

    //si un champs n'est pas vide on met en session pour un eventuel retour arrière
    if($prenom != ""){
        $_SESSION['prenom'] = ucfirst(strtolower($prenom));
    }
    if($nom != ""){
        $_SESSION['nom'] = mb_strtoupper($nom);
    }
    if($adresse != ""){
        $_SESSION['adresse'] = $adresse;
    }
    if($cp != ""){
        $_SESSION['cp'] = strtoupper($cp);
    }
    if($ville != ""){
        $_SESSION['ville'] = mb_strtoupper($ville);
    }
    if($pays != ""){
        $_SESSION['pays'] = mb_strtoupper($pays);
    }
    if($email != ""){
        $_SESSION['email'] = $email;
    }
    if($telephone != ""){
        $_SESSION['telephone'] = $telephone;
    }
    if($port != ""){
        $_SESSION['port'] = $port;
        if($_SESSION['port'] == "retrait_caen1"){
            $_SESSION['expeditionOption'] = "non";
        }else{
            $_SESSION['expeditionOption'] = $expeditionOption;
        }
    } 

    //si une session est vide on revient en arrière
    if($_SESSION['prenom'] == "" ||
        $_SESSION['nom'] == "" ||
        $_SESSION['adresse'] == "" ||
        $_SESSION['cp'] == "" ||
        $_SESSION['ville'] == "" ||
        $_SESSION['pays'] == "" ||
        $_SESSION['email'] == "" ||
        $_SESSION['telephone'] == "" ||
        $_SESSION['port'] == ""){

        $_SESSION['alertMessage'] = "Il manque un champs de saisie !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    //on controle chaque champs si on a le format attendu
    //prenom,nom,ville,pays inférieur à 50
    if(strlen($_SESSION['prenom']) > 50 || strlen($_SESSION['nom']) > 50 || strlen($_SESSION['ville']) > 30 || strlen($_SESSION['pays']) > 2){
        if(strlen($_SESSION['prenom']) > 50 ){
            unset($_SESSION['prenom']);
        }
        if(strlen($_SESSION['nom']) > 50 ){
            unset($_SESSION['nom']);
        }
        if(strlen($_SESSION['ville']) > 30 ){
            unset($_SESSION['ville']);
        }
        if(strlen($_SESSION['pays']) > 2 ){
            unset($_SESSION['pays']);
        }
        $_SESSION['alertMessage'] = "Une saisie est trop longue (max 50 caractères)";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();   
    }

    //adresse supérieur à 80
    if(strlen($_SESSION['adresse']) > 80){
        if(strlen($_SESSION['adresse']) > 80 ){
            unset($_SESSION['adresse']);
        }
        $_SESSION['alertMessage'] = "Adresse trop longue (max 80 caractères)";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();   
    }

    //CODES POSTAUX EN FONCTION DES PAYS

    //Allemagne,France, Espagne, Suisse, Italie, Monaco, DOM-TOM / 5 chiffres
    $codepostaux5chiffres = array("DE","FR","CH","ES","IT","MC","YT","GF","GP","MQ","RE");
    if(in_array($_SESSION['pays'], $codepostaux5chiffres)){
        if(!preg_match('#^[0-9]{5}$#', $_SESSION['cp'])){
            unset($_SESSION['cp']);
            $_SESSION['alertMessage'] = "Saisie du code postale incorrect !";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();   
        } 
    }
    //Belgique, Luxembourg et Suisse / 4 chiffres
    $codepostaux4chiffres = array("BE","LU");
    if(in_array($_SESSION['pays'], $codepostaux4chiffres)){
        if(!preg_match('#^[0-9]{4}$#', $_SESSION['cp'])){
            unset($_SESSION['cp']);
            $_SESSION['alertMessage'] = "Saisie du code postale incorrect pour ".$pays."!";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();   
        } 
    }
    //Andorre : format AD-3 chiffres
    if($_SESSION['pays'] == "AD"){
        if(!preg_match('#^AD([0-9]{3})$#', $_SESSION['cp'])){
            unset($_SESSION['cp']);
            $_SESSION['alertMessage'] = "Saisie du code postale incorrect pour AD !";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();   
        } 
    }
    //Royaume-Uni
    if($_SESSION['pays'] == "GB"){
        if(!preg_match('#^[A-Z]{1,2}[0-9][A-Z0-9]? ?[0-9][A-Z]{2}$#', $_SESSION['cp'])){
            unset($_SESSION['cp']);
            $_SESSION['alertMessage'] = "Saisie du code postale incorrect !";
            $_SESSION['alertMessageConfig'] = "danger";
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();   
        } 
    }


    if(!preg_match('#^[0-9]{8,14}$#', $_SESSION['telephone'])){
        unset($_SESSION['telephone']);
        $_SESSION['alertMessage'] = "Saisie téléphone incorrect !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }

    //on controle adresse mail au bon format
    if(!filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL)){
        unset($_SESSION['email']);
        $_SESSION['alertMessage'] = "Mauvais format: adresse email !";
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


                $sqlListeMessages = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ?");
                $sqlListeMessages-> execute(array($_SESSION['sessionId']));
                $donneesListeMessages = $sqlListeMessages-> fetchAll();

                $sqlSaveClient = $bdd -> prepare("INSERT INTO clients (idUser,nom,prenom,adresse,cp,ville,pays,telephone,email,port,optionExpedition) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                $sqlSaveClient-> execute(array($_SESSION['sessionId'],$_SESSION['nom'],$_SESSION['prenom'],$_SESSION['adresse'],$_SESSION['cp'],$_SESSION['ville'],$_SESSION['pays'],$_SESSION['telephone'],$_SESSION['email'],$_SESSION['port'],$_SESSION['expeditionOption']));

                //pour affichage dans accueil admin et surtout suppression des messages au statut 0 -> 5 jours (voir verifConnexion)
                $sqlListeMessagesUpdateDevis = $bdd -> prepare("UPDATE listeMessages SET statut = 1 WHERE idUser = ?");
                $sqlListeMessagesUpdateDevis-> execute(array($_SESSION['sessionId']));


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