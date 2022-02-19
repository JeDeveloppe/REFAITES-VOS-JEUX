<?php
@session_start ();
//require("../../controles/fonctions/adminOnline.php");
require("../../controles/fonctions/validation_donnees.php");
require("../../config.php");

if(isset($_POST['document'])){ //si c'est une demande client à la confirmation de paiement
    $document = valid_donnees($_POST['document']);
}else{
    $document = valid_donnees($_GET['document']);
}


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
function entete_table_produits($position_entete_produits){
    global $pdf;
    $pdf->SetDrawColor(183); // Couleur du fond
    $pdf->SetFillColor(221); // Couleur des filets
    $pdf->SetTextColor(0); // Couleur du texte
    $pdf->SetY($position_entete_produits);
    $pdf->SetX(8);
    $pdf->Cell(176,8,utf8_decode('Désignation'),1,0,'L',1);
    $pdf->SetX(176); // 104 + 10
    $pdf->Cell(24,8,'Net HT',1,0,'C',1);
    $pdf->Ln(); // Retour à la ligne
}

require_once("../../config.php");
require_once("../../bdd/connexion-bdd.php");
require_once("../../bdd/table_config.php");

//ON CHERCHE LES INFOS DU DOCUMENT
$sqlDocument = $bdd -> prepare("SELECT * FROM documents WHERE idDocument = ?");
$sqlDocument-> execute(array($document));
$donneesDocument = $sqlDocument-> fetch();

if($donneesDocument['expedition'] == "mondialRelay"){
    $detailExpedition = "Expédition par Mondial Relay ";
}elseif($donneesDocument['expedition'] == "colissimo"){
    $detailExpedition = "Expédition par Colissimo ";
}elseif($donneesDocument['expedition'] == "poste"){
    $detailExpedition = "Expédition par LA Poste ";
}else{
    $detailExpedition = "Retrait sur Caen ";
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
$pdf->Text(130,38,utf8_decode($donneesClient['prenom']).' '.utf8_decode($donneesClient['nom']));
$pdf->Text(130,43,utf8_decode($donneesClient['adresse']));
$pdf->Text(130,48,$donneesClient['cp'].' '.utf8_decode($donneesClient['ville']));
$pdf->Text(130,53,utf8_decode($donneesClient['pays']));

//EN TETE DU TABLEAU
$position_entete_produits = 105;
entete_table_produits($position_entete_produits);

//LIGNE FOURNITURES
$position_detail = $position_entete_produits + 8; // Position à 9mm de l'entête
$pdf->SetY($position_detail);
$pdf->SetX(8);
$pdf->MultiCell(168,8,"Fourniture(s)",1,'C');
$pdf->SetY($position_detail);
$pdf->SetX(176);
$pdf->MultiCell(24,8,number_format($donneesCalcul['total'],2),1,'R');

//LIGNE FORFAIT DE BASE
$position_detail = $position_entete_produits + 16; // Position à 9mm de l'entête
$pdf->SetY($position_detail);
$pdf->SetX(8);
$pdf->MultiCell(168,8,"Forfait de base ",1,'R');
$pdf->SetY($position_detail);
$pdf->SetX(176);
$pdf->MultiCell(24,8,number_format($donneesDocument['prix_preparation'],2),1,'R');

//LIGNE FORFAIT ENVOI
$position_detail = $position_entete_produits + 24; // Position à 9mm de l'entête
$pdf->SetY($position_detail);
$pdf->SetX(8);
$pdf->MultiCell(168,8,utf8_decode($detailExpedition),1,'R');
$pdf->SetY($position_detail);
$pdf->SetX(176);
$pdf->MultiCell(24,8,number_format($donneesDocument['prix_expedition'],2),1,'R');



//tableau des totaux
$tableauTotauxY = $position_entete_produits + 50;
$pdf->SetY($tableauTotauxY);
$pdf->SetX(148);
$pdf->MultiCell(28,8,"Total HT:",1,'L');
$pdf->SetY($tableauTotauxY);
$pdf->SetX(176);
$pdf->MultiCell(24,8,$donneesDocument['totalHT'],1,'R');
$pdf->SetY($tableauTotauxY + 8);
$pdf->SetX(148);
$pdf->MultiCell(28,8,"TVA:",1,'L');
$pdf->SetY($tableauTotauxY + 8);
$pdf->SetX(176);
$pdf->MultiCell(24,8,$donneesDocument['totalTVA'],1,'R');
$pdf->SetY($tableauTotauxY + 16);
$pdf->SetX(148);
$pdf->MultiCell(28,8,"Total TTC:",1,'L');
$pdf->SetY($tableauTotauxY + 16);
$pdf->SetX(176);
$pdf->MultiCell(24,8,$donneesDocument['totalTTC'],1,'R');

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

//CONTENUE DU MAIL
$contentFacture = '
<!-- PARAGRAPH -->
<!-- Set text color and font family ("sans-serif" or "Georgia, serif"). Duplicate all text styles in links, including line-height -->
<tr>
    <td align="left" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 560px; font-size: 12px; font-weight: 400; line-height: 100%;
        padding: 25px;
        color: #000000;
        font-family: sans-serif;" class="paragraph">
            Bonjour,
            <p>Veuillez trouver ci-joint la facture correspondant à votre commande sur Refaites vos jeux.</p>
            <p>Merci de votre confiance et à bientôt ! </p>     
    </td>
</tr>';

//ON ENVOI PAR MAIL
require("../../mails/mail_envoiFacture.php");
?>