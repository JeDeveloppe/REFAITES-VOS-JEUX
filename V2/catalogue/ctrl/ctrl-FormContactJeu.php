<?php
@session_start ();

// On vérifie que la méthode POST est utilisée
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    require("../../config.php");
    require('../../bdd/connexion-bdd.php');
    require('../../bdd/table_config.php');

        $tailleImage = $donneesConfig[2]['valeur'];
        $widthImage = $donneesConfig[3]['valeur'];
        $heightImage = $donneesConfig[4]['valeur'];

        require("../../controles/fonctions/validation_donnees.php");
 
        $content = valid_donnees($_POST["content"]);
        $idDuJeu = valid_donnees($_POST["idDuJeu"]);

        //ON VERIFIE LES CHAMPS REMPLI
        if(!empty($content) || !strlen($content) < 15 || !strlen($content) > 300 || !empty($idDuJeu)){
            //ON TESTE L'IMAGE

            //si on upload au moins une photo
            if(isset($_FILES["photo"])){
                    //on compte le nombre de photo
                    $countfiles = count($_FILES['photo']['name']);
                    //si on a charger plus que 2 photos retour en arriere
                    if($countfiles > 2){
                        $_SESSION['alertMessage'] = "Pas plus que 2 images par demande merci !";
                        $_SESSION['alertMessageConfig'] = "warning";
                        header("Location: ".$_SERVER['HTTP_REFERER']);
                        exit();  
                    }

                    for($i=0;$i<$countfiles;$i++){
                        if($_FILES["photo"]["size"][$i] > 0){
                            if($_FILES["photo"]["error"][$i] == 0){
                                $allowed = array("jpg" => "image/jpg", "JPG" => "image/jpg", "JPEG" => "image/jpeg", "jpeg" => "image/jpeg", "GIF" => "image/gif", "gif" => "image/gif", "png" => "image/png", "PNG" => "image/png");
                                $filename[$i] = time()."-".$_FILES["photo"]["name"][$i];
                                $filetype[$i] = $_FILES["photo"]["type"][$i];
                                $filesize[$i] = $_FILES["photo"]["size"][$i];
                            
                                // Verify file extension
                                $ext[$i] = pathinfo($filename[$i], PATHINFO_EXTENSION);
                                if(!array_key_exists($ext[$i], $allowed)){
                                    $_SESSION['alertMessage'] = "Format d'image incorrect pour l'image ".[$i]." !";
                                    $_SESSION['alertMessageConfig'] = "warning";
                                    header("Location: ".$_SERVER['HTTP_REFERER']);
                                    exit();  
                                }
            
                                // Verify file size - 5MB maximum
                                $maxsize = $tailleImage * 1024 * 1024;
            
                                if($filesize[$i] > $maxsize){
                                    $_SESSION['alertMessage'] = "Image trop grande, maximum ".$tailleImage."MB (ou ".$maxsize." octets)!";
                                    $_SESSION['alertMessageConfig'] = "warning";
                                    header("Location: ".$_SERVER['HTTP_REFERER']);
                                    exit();
                                }
            
            
                                // Verify MYME type of the file
                                if(in_array($filetype[$i], $allowed)){
                                    $image[$i] = $_FILES['photo']['tmp_name'][$i]; 
                                    $imgContent[$i] = file_get_contents($image[$i]); 
                                    $imgBase64[$i] = base64_encode($imgContent[$i]);
            
                                    //optention des dimensions de l'image
                                    list($width, $height) = getimagesize($image[$i]);
                                    //dimension mini pour affichage correct en extra large voir css
                                    $minWidth = $widthImage;
                                    $minHeight = $heightImage;
                                    if($width < $minWidth || $height < $heightImage){
                                        $_SESSION['alertMessage'] = "Image n° ".[$i]." trop petite, taille mini (".$widthImage."px X ".$heightImage."px)";
                                        $_SESSION['alertMessageConfig'] = "warning";
                                        header("Location: ".$_SERVER['HTTP_REFERER']);
                                        exit();
            
                                    }
                                }
                            }
                            
                        }else{
                            $imgBase64 = "";
                        }
                    }
            }


           

                $_SESSION['content'] = $content;
            }else{
                $_SESSION['alertMessage'] = "Un champs n' était pas correct, veuillez rééssayer !";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit();
            }

        
        // On vérifie si le champ "recaptcha-response" contient une valeur
        if(empty($_POST['recaptcha-response'])){
            $_SESSION['alertMessage'] = "Captcha Google vide...";
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

                    //ON VERIFIE SI IL EXISTE DEJA UNE CLE DE PANIER SI NON ON LA CREE
                    if(!isset($_SESSION['panierKey'])){
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
                            $validKey = random_strings(25);
                            $verifValidKey = $bdd->query("SELECT panierKey FROM listeMessages WHERE panierKey = '$validKey' ");
                            $donneesValidKey = $verifValidKey->rowCount();
                        }
                        while($donneesValidKey = 0);
                        $_SESSION['panierKey'] = $validKey;
                    }

                    //ICI TOUT EST BON
                    require_once("../../config.php");
                    require_once("../../bdd/connexion-bdd.php");
                    $sqlListeMessages = $bdd -> prepare("INSERT INTO listeMessages (idUser, idJeu, message, time, statut, panierKey) VALUES (:user, :jeu, :message, :creation, :statut, :panierKey)");
                    $sqlListeMessages-> execute(array("user" => $_SESSION['sessionId'], "jeu" => $idDuJeu, "message" => $content, "creation" => time(), "statut" => 0, "panierKey" => $_SESSION['panierKey']));
                    $lastId = $bdd-> lastInsertId();
                    //on recupere le dernier enregistrement

                    //on saisi dans la base chaque images
                    for($i=0;$i<$countfiles;$i++){
                        if($_FILES["photo"]["size"][$i] > 0){
                            $filetype[$i] = $_FILES["photo"]["type"][$i];
                            $image[$i] = $_FILES['photo']['tmp_name'][$i]; 
                            $imgContent[$i] = file_get_contents($image[$i]); 
                            $imgBase64[$i] = base64_encode($imgContent[$i]);
                            $sqlInsertImageListeMessage = $bdd-> prepare("INSERT INTO listeMessages_images (idListeMessages,image,image_type) VALUES (?,?,?)");
                            $sqlInsertImageListeMessage-> execute(array($lastId,$imgBase64[$i],$filetype[$i]));
                        }
                    }


                    if(!headers_sent($filename, $linenum)) {
                        $_SESSION['alertMessage'] = "Demande dans le panier!<br/>N'oubliez pas de le valider!";
                        $_SESSION['alertMessageConfig'] = "success";
                        unset($_SESSION['content']);
                        header("Location: /catalogue-pieces-detachees/");
                        exit();
                    }else{
                        echo 'Erreur fichier '.$filename.' à la ligne '.$linenum.'<br/>
                        Il est donc impossible de vous rediriger automatiquement, aussi veuillez
                        cliquer <a href="http://www.refaitesvosjeux.fr">ici</a>';
                        exit();
                    }
                }
            }
        }
}//fin de methode POST
else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /");  
}
?>