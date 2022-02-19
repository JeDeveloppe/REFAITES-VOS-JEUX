<?php
@session_start ();

    $mail = valid_donnees($_POST["mail"]);
    $passwordUser = valid_donnees($_POST["passwordUser"]);

    function valid_donnees($donnees){
        $donnees = trim($donnees);
        $donnees = stripslashes($donnees);
        $donnees = htmlspecialchars($donnees);
        return $donnees;
    }

    if (empty($mail)
        || !filter_var($mail, FILTER_VALIDATE_EMAIL)
        || empty($passwordUser)){

        $_SESSION['alertMessage'] = "Un champs n' était pas rempli, veuillez rééssayer !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: /connexion-inscription/");
    }
    else{ //tout est bon on verifie si l'adresse mail existe

        require_once('../../config.php');
        require_once('../../bdd/connexion-bdd.php');

        $req = $bdd->prepare('SELECT * FROM clients WHERE email = ?');
        $req->execute(array($mail));
        $donnees = $req->fetch();
        $nbr = $req->rowCount();

        if($nbr == 1){ // si adresse mail existe on peut comparer les mots de passe saisie et celui de la base de donnee


            $passwordFromBdd = $donnees['password'];
            
            if(password_verify($passwordUser, $passwordFromBdd)){ // les mots de passes sont identiques on se connecte
                //on met a jour la bdd de la derniere visite
                $updateTimeVisite = $bdd->prepare('UPDATE clients SET lastVisite = :time WHERE email = :email');
                $updateTimeVisite->execute(array('time' => time(), 'email' => $mail));

                if($donnees['pseudo'] == NULL){
                    $bonjourNom = $donnees['prenomFacturation'];
                }else{
                    $bonjourNom = $donnees['pseudo'];
                }

                $_SESSION['pseudo'] = $bonjourNom;
                $_SESSION['levelUser'] = $donnees['userLevel'];
                $_SESSION['sessionId'] = $donnees['idUser'];
                $_SESSION['idClient'] = $donnees['idClient'];

                if($donnees['isAssociation'] < time()){
                    $_SESSION['isAssociation'] = "NO";
                }else{
                    $_SESSION['isAssociation'] = "YES";
                }

                $_SESSION['alertMessage'] = "Bienvenue ".$bonjourNom;
                $_SESSION['alertMessageConfig'] = "success";

                //on fait le tri dans la liste des messages a la connexion
                // message de plus de 5 jours toujours à 0
                $time = time() - 432000 ; //5 jours en arriere
                $sqlDeleteVielleDemande = $bdd -> prepare("DELETE FROM listeMessages WHERE statut = ? AND time < ? AND qte IS NULL");
                $sqlDeleteVielleDemande-> execute(array(0,$time));
                // jeuxComplet de plus de 2 jours toujours à 0
                $timeEnd = time() - 86400;
                $allAchatsWaiting = $bdd->prepare("SELECT * FROM listeMessages WHERE time < ? AND qte > ?");
                $allAchatsWaiting->execute(array($timeEnd, 0));   //on garde maximum 24hrs en "mémoire"
                $donneesAllAchatsWaiting = $allAchatsWaiting->fetchAll();

                foreach($donneesAllAchatsWaiting as $ligne){
                    $sqlUpdateStockJC = $bdd->prepare("SELECT * FROM jeux_complets WHERE idJeuxComplet = ?");
                    $sqlUpdateStockJC->execute(array($ligne['idJeu']));
                    $donneesJC = $sqlUpdateStockJC->fetch();

                    $nouveauStock = $donneesJC['stock'] + $ligne['qte'];

                    $sqlUpdateStockJCAfterCalc = $bdd->prepare("UPDATE jeux_complets SET stock = ? WHERE idJeuxComplet = ?");
                    $sqlUpdateStockJCAfterCalc->execute(array($nouveauStock,$ligne['idJeu']));

                    $delete = $bdd->prepare("DELETE FROM listeMessages WHERE idListeMessages = ?");
                    $delete->execute(array($ligne['idListeMessages']));

                }

                if($_SESSION['levelUser'] > 1){
                    //si superieur a simple client
                    header("Location: /admin/accueil/");
                    exit(); 
                }else{
                    //sinon on redirige vers l'accueil
                    header("Location: /");
                    exit(); 
                }

            }else{  
                $_SESSION['alertMessage'] = "Mot de passe incorrect !";
                $_SESSION['alertMessageConfig'] = "danger";
                header("Location: /connexion/");
                exit();
            }

        }
        else{ // adresse mail n'existe pas

            // redirection acceuil du site
            $_SESSION['alertMessage'] = "Adresse email inconnue ou niveau requis trop faible !";
            $_SESSION['alertMessageConfig'] = "danger";
            $req->closeCursor(); 
            header("Location: /connexion/");
        }
    }
?>