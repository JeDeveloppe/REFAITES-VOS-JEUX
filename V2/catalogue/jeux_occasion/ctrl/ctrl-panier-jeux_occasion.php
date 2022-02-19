<?php
@session_start ();

// On vérifie que la méthode POST est utilisée
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    //ICI TOUT EST BON
    require_once("../../../config.php");
    require_once("../../../bdd/connexion-bdd.php");
    require("../../../controles/fonctions/validation_donnees.php");

    $jeu = valid_donnees($_POST['rvjc']);
    $qte = 1;

    $stockJC = $bdd->prepare("SELECT * FROM jeux_complets WHERE idJeuxComplet = ?");
    $stockJC->execute(array($jeu));
    $donneesJC = $stockJC->fetch();

    $sqlListeMessagesVerif= $bdd->prepare("SELECT * FROM listeMessages WHERE idJeu = ? AND qte > 0 AND idUser = ?");
    $sqlListeMessagesVerif->execute(array($jeu, $_SESSION['sessionId']));
    $donneesVerif = $sqlListeMessagesVerif->fetch();
    $nbrVerif = $sqlListeMessagesVerif->rowCount();

    if($nbrVerif == 1){
        $updateQte = $donneesVerif['qte'] + $qte;
        $sqlListeMessages = $bdd->prepare("UPDATE listeMessages SET qte = ? WHERE idJeu = ? AND idUser = ?");
        $sqlListeMessages->execute(array($updateQte,$jeu,$_SESSION['sessionId']));
    }else{
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

        $sqlListeMessages = $bdd->prepare("INSERT INTO listeMessages (idUser, idJeu, qte, message, time, statut, tarif, panierKey) VALUES (:user, :jeu, :qte, :message, :creation, :statut, :tarif, :panierKey)");
        $sqlListeMessages->execute(array("user" => $_SESSION['sessionId'], "jeu" => $jeu, "qte" => $qte,"message" => "Le jeu complet dans l'état proposé !", "creation" => time(), "statut" => 0, "tarif" => $donneesJC['prixHT'], "panierKey" => $_SESSION['panierKey']));
    }


    $nouveauStock = $donneesJC['stock'] - $qte;

    $sqlUpdateJeuComplet = $bdd->prepare("UPDATE jeux_complets SET stock = ? WHERE idJeuxComplet = ?");
    $sqlUpdateJeuComplet->execute(array($nouveauStock,$jeu));

    $_SESSION['alertMessage'] = "Jeu mis dans le panier !";
    $_SESSION['alertMessageConfig'] = "success";
    header('Location: '.$_SERVER['HTTP_REFERER']); 
    exit();

}//fin de methode POST
else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header('Location: '.$_SERVER['HTTP_REFERER']); 
    exit();
}
?>