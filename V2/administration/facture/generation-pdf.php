<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    require_once("../../controles/fonctions/validation_donnees.php");
    require_once("../../config.php");
    require_once("../../bdd/connexion-bdd.php");
    require_once("../../bdd/table_config.php");
    $tva = $donneesConfig[6]['valeur'];

    if(!isset($_GET['document'])){
        $_SESSION['alertMessage'] = "Donnée manquante !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: /"); 
        exit(); 
    }

    $document = valid_donnees($_GET['document']);
    //ON CHERCHE LES INFOS DU DOCUMENT
    $sqlDocument = $bdd -> prepare("SELECT * FROM documents WHERE idDocument = ?");
    $sqlDocument-> execute(array($document));
    $donneesDocument = $sqlDocument-> fetch();
    $nbrDoc = $sqlDocument->rowCount();
    $adresseFacturationClient = explode("<br/>",$donneesDocument['adresse_facturation']);

    if($nbrDoc != 1){
        $_SESSION['alertMessage'] = "Document inconnu !";
        $_SESSION['alertMessageConfig'] = "danger";
        header("Location: /"); 
        exit(); 
    }

    //on cherche les achats de jeu complet
    $sqlDetailsDocumentsAchats = $bdd-> prepare("SELECT * FROM documents_lignes_achats WHERE idDocument = ?");
    $sqlDetailsDocumentsAchats->execute(array($document));
    $donneesDetailsDocumentAchats = $sqlDetailsDocumentsAchats->fetchAll();

    require_once('../../FPDF/fpdf.php');

    class PDF extends FPDF
    {
        // En-tête
        function Header(){
            // Logo
            $this->Image('../../images/design/refaitesvosjeux.png',10,6,30);
            // Police Arial gras 15
            $this->SetFont('Arial','B',15);
            // Décalage à droite
            $this->Cell(150);
            // Titre
            $this->Cell(30,10,'FACTURE',1,0,'C');
            // Saut de ligne
            $this->Ln(20);
        }

        // Pied de page
        function Footer(){
            // Positionnement à 1,5 cm du bas
            $this->SetY(-15);
            // Police Arial italique 8
            $this->SetFont('Arial','I',8);
            // Numéro de page
            $this->Cell(0,10,'Page '.$this->PageNo().'/1',0,0,'C');
        }


    }

    //en-tete du tableau
    function entete_table_accessoires($position_entete_produits){
        global $pdf;
        $pdf->SetDrawColor(183); // Couleur du fond
        $pdf->SetFillColor(221); // Couleur des filets
        $pdf->SetTextColor(0); // Couleur du texte
        $pdf->SetY($position_entete_produits);
        $pdf->SetX(8);
        $pdf->Cell(176,8,utf8_decode('Désignation'),1,0,'C',1);
        $pdf->SetX(176); // 104 + 10
        $pdf->Cell(24,8,'Net HT',1,0,'C',1);
        $pdf->Ln(); // Retour à la ligne
    }



    if($donneesDocument['expedition'] == "mondialRelay"){
        $detailExpedition = "Expédition par Mondial Relay:";
    }elseif($donneesDocument['expedition'] == "colissimo"){
        $detailExpedition = "Expédition par Colissimo:";
    }elseif($donneesDocument['expedition'] == "poste"){
        $detailExpedition = "Expédition par La Poste:";
    }else{
        $detailExpedition = "Retrait sur Caen: ";
    }

    //ON CHERCHE LES INFOS DU CLIENT
    $sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = ?");
    $sqlClient-> execute(array($donneesDocument['idUser']));
    $donneesClient = $sqlClient-> fetch();
  

    //ON FAIT LE TOTAL DES LIGNES DU DOCUMENT
    $calcul = $bdd -> prepare("SELECT SUM(prix) AS total FROM documents_lignes WHERE idDocument = ?");
    $calcul-> execute(array($donneesDocument['idDocument']));
    $donneesCalcul = $calcul -> fetch();

    // Instanciation de la classe dérivée
    $pdf = new PDF('P','mm','A4');
    $pdf->AddPage();
    $pdf->SetFont('Helvetica','',11);
    $pdf->SetTextColor(0);

    // Infos de la socièté sous le logo à gauche
    $pdf->SetFont('Helvetica','',8);
    $pdf->Text(10,35,utf8_decode('Adresse : '.$GLOBALS['adresseSociete']));
    $pdf->Text(10,40,'Siret : '.$GLOBALS['siretSociete']);

    $pdf->SetFont('Helvetica','',11);
    // Infos de la commande calées à gauche
    $pdf->Text(8,78,utf8_decode('N° de facture : '.$donneesDocument['numero_facture']));
    $pdf->Text(8,84,'Date : '.date("d-m-Y",$donneesDocument['time_transaction']));
    $pdf->Text(8,90,utf8_decode('Mode de règlement : '.$donneesDocument['moyen_paiement']));

    // Infos du client calées à droite
    $pdf->Text(130,38,utf8_decode($adresseFacturationClient[0]));
    $pdf->Text(130,43,utf8_decode($adresseFacturationClient[1]));
    $pdf->Text(130,48,utf8_decode($adresseFacturationClient[2]));
    $pdf->Text(130,53,utf8_decode($adresseFacturationClient[3]));
   

    //EN TETE DU TABLEAU
    $position_entete_produits = 105;
    entete_table_accessoires($position_entete_produits);


    if(count($donneesDetailsDocumentAchats) > 0){
        $positionLigneAchat = 8;
        foreach($donneesDetailsDocumentAchats as $ligneAchat){
            $sqlJeuCatalogue = $bdd->prepare("SELECT nom,editeur FROM catalogue WHERE idCatalogue = ?");
            $sqlJeuCatalogue->execute(array($ligneAchat['idCatalogue']));
            $donneesCatalogue = $sqlJeuCatalogue->fetch();

            $pdf->SetY($position_entete_produits + $positionLigneAchat);
            $pdf->SetX(8);
            $pdf->MultiCell(168,8,utf8_decode("Jeu d'occasion: ".$donneesCatalogue['nom'].' - '.$donneesCatalogue['editeur']),1,'C');
            $pdf->SetY($position_entete_produits + $positionLigneAchat);
            $pdf->SetX(176);
            $pdf->MultiCell(24,8,number_format($ligneAchat['prix'] * $tva / 100,2),1,'R');
            $positionLigneAchat += 8;
        }
    }else{
        $positionLigneAchat = 8;
    }

    $position_detail = $position_entete_produits + $positionLigneAchat; // Position à 9mm de l'entête

    //LIGNE FOURNITURES
    if($donneesCalcul['total'] > 0){
        $pdf->SetY($position_detail);
        $pdf->SetX(8);
        $pdf->MultiCell(168,8,utf8_decode("Fourniture(s) de pièce(s)"),1,'C');
        $pdf->SetY($position_detail);
        $pdf->SetX(176);
        $pdf->MultiCell(24,8,number_format($donneesCalcul['total'] * $tva / 100,2),1,'R');
    }else{
        $position_detail -= 8;
    }

    //LIGNE FORFAIT DE BASE
    $pdf->SetY($position_detail + 8);
    $pdf->SetX(8);
    $pdf->MultiCell(168,8,utf8_decode("Forfait de base / Adhésion RVJ:"),1,'R');
    $pdf->SetY($position_detail + 8);
    $pdf->SetX(176);
    $pdf->MultiCell(24,8,number_format($donneesDocument['prix_preparation'] * $tva / 100,2),1,'R');

    //LIGNE FORFAIT ENVOI
    $pdf->SetY($position_detail + 16);
    $pdf->SetX(8);
    $pdf->MultiCell(168,8,utf8_decode($detailExpedition),1,'R');
    $pdf->SetY($position_detail + 16);
    $pdf->SetX(176);
    $pdf->MultiCell(24,8,number_format($donneesDocument['prix_expedition']* $tva / 100,2),1,'R');



    //tableau des totaux
    $tableauTotauxY = $position_detail + 50;
    $pdf->SetY($tableauTotauxY);
    $pdf->SetX(148);
    $pdf->MultiCell(28,8,"Total HT:",1,'L');
    $pdf->SetY($tableauTotauxY);
    $pdf->SetX(176);
    $pdf->MultiCell(24,8,number_format($donneesDocument['totalHT'] / 100,"2",".",""),1,'R');
    $pdf->SetY($tableauTotauxY + 8);
    $pdf->SetX(148);
    $pdf->MultiCell(28,8,"TVA:",1,'L');
    $pdf->SetY($tableauTotauxY + 8);
    $pdf->SetX(176);
    $pdf->MultiCell(24,8,number_format($donneesDocument['totalTVA'] / 100,"2",".",""),1,'R');
    $pdf->SetY($tableauTotauxY + 16);
    $pdf->SetX(148);
    $pdf->MultiCell(28,8,"Total TTC:",1,'L');
    $pdf->SetY($tableauTotauxY + 16);
    $pdf->SetX(176);
    $pdf->MultiCell(24,8,number_format($donneesDocument['totalTTC'] / 100,"2",".",""),1,'R');

    //LIGNE REMERCIEMENT
    $pdf->SetFont('Helvetica','',12);
    $pdf->SetY(250);
    $pdf->SetX(10);
    $pdf->MultiCell(190,8,utf8_decode("MERCI POUR VOTRE COMMANDE."),0,'C');

    //ligne TVA dans table de config vaut 1 = PAS DE TVA
    if($donneesConfig['6']['valeur'] == 1){
    $pdf->SetFont('Helvetica','',8);
    $pdf->SetY(262);
    $pdf->SetX(10);
    $pdf->MultiCell(190,8,utf8_decode("TVA non applicable, article 293B du code général des impôts."),0,'C');
    }

    // Nom du fichier
    $nom = 'RefaitesVosJeux-'.$donneesDocument['numero_facture'].'.pdf';
    // Création du PDF
    $pdf->Output($nom,'I');

}else{
    $_SESSION['alertMessage'] = "Requête interdite...";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /"); 
    exit(); 
}
?>