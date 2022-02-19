<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");
include('../../../config.php');
include('../../../bdd/connexion-bdd.php');
include('../../../bdd/table_config.php');
require('../../../controles/fonctions/validation_donnees.php');
$tvaSite = $donneesConfig[6]['valeur'];

$client = valid_donnees($_POST['client']);
$panier = valid_donnees($_POST['panier']);
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

if(isset($_POST['totalPrixOccasion'])){
    $totalPrixOccasion = valid_donnees($_POST['totalPrixOccasion']);
}else{
    $totalPrixOccasion = 0;
}

$sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = :client");
$sqlClient-> execute(array("client" => $client));
$donneesClient = $sqlClient->fetch();


//nouveau champs organisme
if(empty($donneesClient['organismeFacturation']) || $donneesClient['organismeFacturation'] == null){
    $organismeFacturation = "";
}else{
    $organismeFacturation = $donneesClient['organismeFacturation'].'<br/>';
}
if(empty($donneesClient['organismeLivraison']) || $donneesClient['organismeLivraison'] == null){
    $organismeLivraison = "";
}else{
    $organismeLivraison = $donneesClient['organismeLivraison'].'<br/>';
}

//adresses directementdans le document
//adr livraison
if($envoi == "retrait_caen1"){
    $adresse_livraison = $donneesConfig[9]['valeur'];
}else{
    $adresse_livraison = $organismeLivraison.$donneesClient['nomLivraison'].' '.$donneesClient['prenomLivraison'].'<br/>'.$donneesClient['adresseLivraison'].'<br/>'.$donneesClient['cpLivraison'].' '.$donneesClient['villeLivraison'].'<br/>'.$donneesClient['paysLivraison'];
}
//adr facturation
$adresse_facturation = $organismeFacturation.$donneesClient['nomFacturation'].' '.$donneesClient['prenomFacturation'].'<br/>'.$donneesClient['adresseFacturation'].'<br/>'.$donneesClient['cpFacturation'].' '.$donneesClient['villeFacturation'].'<br/>'.$donneesClient['paysFacturation'];

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
    $verifValidKey = $bdd->query("SELECT validKey FROM documents WHERE validKey = '$validKey' ");
    $donneesValidKey = $verifValidKey -> rowCount();
}
while($donneesValidKey = 0);


$sqlCreationDevis = $bdd-> prepare("INSERT INTO documents (idUser,validKey, prix_preparation,expedition,prix_expedition,totalOccasions,commentaire,totalHT,totalTVA,totalTTC,time, adresse_facturation,adresse_livraison) VALUES (:client, :key, :prixP, :expe, :prixE, :prixOccasion, :com, :ht, :tva, :ttc, :time, :adrFac, :adrLiv)");
$sqlCreationDevis->execute(array(
    "client" => $client,
    "key" => $validKey,
    "prixP" => $prixPreparation / $tvaSite * 100,
    "expe" => $envoi,
    "prixE" => $prixExpedition / $tvaSite * 100,
    "prixOccasion" => $totalPrixOccasion,
    "com" => $commentaire,
    "ht" => $ht * 100,
    "tva" => $tva * 100,
    "ttc" => $ttc * 100,
    "time" => time(),
    "adrFac" => $adresse_facturation,
    "adrLiv" => $adresse_livraison));

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
$sqlClient = $bdd->query("SELECT * FROM clients WHERE idClient = ".$client);
$donneesClient = $sqlClient -> fetch();

//on recupere deja les achats
$sqlListeMessagesAchats = $bdd ->prepare("SELECT * FROM listeMessages WHERE idUser = ? AND statut = ? AND qte > 0 AND panierKey = ?");
$sqlListeMessagesAchats->execute(array($donneesClient['idUser'],1,$panier));
$donneesListeMessagesAchats = $sqlListeMessagesAchats-> fetchAll();

//on recupere pour la suppression
$sqlListeMessage = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND statut = ? AND qte IS NULL AND panierKey = ?");
$sqlListeMessage-> execute(array($donneesClient['idUser'],1,$panier));
$donneesListeMessage = $sqlListeMessage -> fetchAll();

foreach($donneesListeMessage as $imageToDelete){
    $sqlListeMessagesImagesUpdateDevis = $bdd->prepare("DELETE FROM listeMessages_images WHERE idListeMessages = ?");
    $sqlListeMessagesImagesUpdateDevis-> execute(array($imageToDelete['idListeMessages']));
}

$sqlListeMessagesUpdateDevis = $bdd->prepare("DELETE FROM listeMessages WHERE idUser = ? AND statut = ? AND panierKey = ?");
$sqlListeMessagesUpdateDevis-> execute(array($donneesClient['idUser'],1,$panier));



//pour chaque ligne on vérifie les champs et on mets dans la table documents_lignes
foreach($donneesListeMessagesAchats as $ligne){

    $sqlToutDujeuComplet = $bdd->prepare("SELECT * FROM jeux_complets WHERE idJeuxComplet = ?");
    $sqlToutDujeuComplet->execute(array($ligne['idJeu']));
    $donneesToutDuJeuComplet = $sqlToutDujeuComplet->fetch();
    if($donneesToutDuJeuComplet['isNeuf'] == true){
        $detailsJeuComplet =  'COMME NEUF';
    }else{
        $detailsJeuComplet =  'État de la boite: '.$donneesToutDuJeuComplet['etatBoite'].'<br/>État du matériel: '.$donneesToutDuJeuComplet['etatMateriel'].'<br/>Règle du jeu: '.$donneesToutDuJeuComplet['regleJeu']; 
    }

    $sqlInsertLignesDocumentAchat = $bdd -> prepare("INSERT INTO documents_lignes_achats (idDocument,idJeuComplet,idCatalogue,detailsComplet,qte,prix) VALUES (?,?,?,?,?,?)");
    $sqlInsertLignesDocumentAchat-> execute(array($devisCree,$ligne['idJeu'],$donneesToutDuJeuComplet['idCatalogue'],$detailsJeuComplet,$ligne['qte'],$ligne['tarif']));
    
}

//on met les reponses pour les pieces détachées
for ($i=0; $i < $nbrL; $i++){
    $sqlInsertLignesDocument = $bdd -> prepare("INSERT INTO documents_lignes (idDocument,idJeu,question,reponse, prix) VALUES (?,?,?,?,?)");
    $sqlInsertLignesDocument-> execute(array($devisCree,$jeu[$i],$questions[$i],$reponses[$i],$prixLignes[$i] / $tvaSite * 100));
}

//puis on redirige pour modifier ou envoyer en mail...
$_SESSION['alertMessage'] = "Devis créé et sauvegardé !";
$_SESSION['alertMessageConfig'] = "success";
header("Location: /admin/devis/edition/".$numero);

exit();
?>