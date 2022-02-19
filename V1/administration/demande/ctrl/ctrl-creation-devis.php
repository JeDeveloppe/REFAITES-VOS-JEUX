<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");
include('../../../config.php');
include('../../../bdd/connexion-bdd.php');
include('../../../bdd/table_config.php');
require('../../../controles/fonctions/validation_donnees.php');

$client = valid_donnees($_POST['client']);
$ttc = valid_donnees($_POST['totalTTC']);
$ht = valid_donnees($_POST['totalHT']);
$tva = valid_donnees($_POST['totalTVA']);
$prixExpedition = valid_donnees_int($_POST['prixExpedition']);
$prixPreparation = valid_donnees_int($_POST['prixPreparation']);
$nbrL = valid_donnees($_POST['nbr_lignes']);
$commentaire = valid_donnees($_POST['commentaire']);
$jeu = $_POST['jeu'];
$reponses = $_POST['reponse'];
$prixLignes = $_POST['prixLigne'];
$questions = $_POST['messageClient'];
$envoi = valid_donnees($_POST['envoi']);


// echo '<pre>';
// print_r($_POST);
// echo '</pre>';
// exit();

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
    "client" => $client,
    "key" => $validKey,
    "prixP" => $prixPreparation,
    "expe" => $envoi,
    "prixE" => $prixExpedition,
    "com" => $commentaire,
    "ht" => $ht,
    "tva" => $tva,
    "ttc" => $ttc,
    "time" => time()));

//on recupere le dernier enregistrement
$devisCree = $bdd->lastInsertId();


//on incremente le numero
require_once("../../../controles/fonctions/incrementation.php");
$numero = incrementation($donneesConfig[7]['valeur'],$chiffreDocument);

//fin de validation du document
$fin_validation = time () + $donneesConfig[11]['valeur'] + 4;   //+4 temps entre enregistrement en envoi...

//on met a jour le numero du devis
$sqlUpdateNumeroDevis = $bdd->prepare("UPDATE documents SET numero_devis = ?, annee = ?, end_validation = ? WHERE idDocument = ?");
$sqlUpdateNumeroDevis-> execute(array($numero,$anneeCivil,$fin_validation,$devisCree));

//on supprime la demande d'origine
$sqlClient = $bdd -> query("SELECT * FROM clients WHERE idClient = ".$client);
$donneesClient = $sqlClient -> fetch();

//on met a jour la table document au passage avec les adresses
$adresses = $donneesClient['nom'].' '.$donneesClient['prenom'].'<br/>'.$donneesClient['adresse'].'<br/>'.$donneesClient['cp'].' '.$donneesClient['ville'].'<br/>'.$donneesClient['pays'];
$sqlUpdateDocumentAdresses = $bdd->prepare("UPDATE documents SET adresse_facturation = ?, adresse_livraison = ? WHERE idDocument = ?");
$sqlUpdateDocumentAdresses->execute(array($adresses,$adresses,$devisCree));


$sqlListeMessage = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ?");
$sqlListeMessage-> execute(array($donneesClient['idUser']));
$donneesListeMessage = $sqlListeMessage -> fetch();

$sqlListeMessagesImagesUpdateDevis = $bdd -> prepare("DELETE FROM listeMessages_images WHERE idListeMessages = ?");
$sqlListeMessagesImagesUpdateDevis-> execute(array($donneesListeMessage['idListeMessages']));

$sqlListeMessagesUpdateDevis = $bdd -> prepare("DELETE FROM listeMessages WHERE idUser = ?");
$sqlListeMessagesUpdateDevis-> execute(array($donneesClient['idUser']));



//pour chaque ligne on vérifie les champs et on mets dans la table documents_lignes

for ($i=0; $i < $nbrL; $i++){
    $sqlInsertLignesDocument = $bdd -> prepare("INSERT INTO documents_lignes (idDocument,idJeu,question,reponse, prix) VALUES (?,?,?,?,?)");
    $sqlInsertLignesDocument-> execute(array($devisCree,$jeu[$i],$questions[$i],$reponses[$i],$prixLignes[$i]));
}

//puis on redirige pour modifier ou envoyer en mail...
$_SESSION['alertMessage'] = "Devis créé et sauvegardé !";
$_SESSION['alertMessageConfig'] = "success";
header("Location: /admin/devis/edition/".$numero);

exit();
?>