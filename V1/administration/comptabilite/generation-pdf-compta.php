<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
require("../../controles/fonctions/validation_donnees.php");
require("../../config.php");
require("../../bdd/connexion-bdd.php");
require("../../bdd/table_config.php");

require_once('../../FPDF/fpdf.php');

class PDF extends FPDF
{
    // En-tête
    function Header(){
        if($this->header == "COMPTABILITE"){
            // Logo
            $this->Image('../../images/design/refaitesvosjeux.png',10,3,30);
            // Police Arial gras 15
            $this->SetFont('Arial','B',15);
            // Décalage à droite
            $this->Cell(120);
            // Titre
            $this->Cell(60,10,'COMPTABILITE',1,0,'C');
            // Saut de ligne
            $this->Ln(20);
        }
        if($this->header == "CLIENTS"){
            // Logo
            $this->Image('../../images/design/refaitesvosjeux.png',10,6,30);
            // Police Arial gras 15
            $this->SetFont('Arial','B',15);
            // Décalage à droite
            $this->Cell(120);
            // Titre
            $this->Cell(60,10,'LISTE DES CLIENTS',1,0,'C');
            // Saut de ligne
            $this->Ln(20);
        }
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

    // TABLEAU DES DONNEES
    function TableDonnees($headerDonnees, $dataDonnees)
    {
        // Colors, line width and bold font
        $this->SetFillColor(0,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Largeurs des colonnes
        $w = array(20, 40, 50, 40, 40);
        // En-tête
        for($i=0;$i<count($headerDonnees);$i++)
            $this->Cell($w[$i],8,utf8_decode($headerDonnees[$i]),1,0,'C','true');
        $this->Ln();
        
        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        // Données
        foreach($dataDonnees as $rowDonnees)
        {
            $this->Cell($w[0],6,$rowDonnees['idUser'],'LR',0,'C',$fill);
            $this->Cell($w[1],6,$rowDonnees['numero_facture'],'LR',0,'C',$fill);
            $this->Cell($w[2],6,date("d-m-Y",$rowDonnees['time_transaction']).' ('.$rowDonnees['moyen_paiement'].')','LR',0,'C',$fill);
            $this->Cell($w[3],6,$rowDonnees['totalHT'],'LR',0,'C',$fill);
            $this->Cell($w[4],6,$rowDonnees['totalTTC'],'LR',0,'C',$fill);
            $this->Ln();
        }
        // Trait de terminaison
        $this->Cell(array_sum($w),0,'','T');
    }

     // TABLEAU DES CLIENTS
     function TableClients($headerClients, $dataClients)
     {
         // Colors, line width and bold font
         $this->SetFillColor(0,0,0);
         $this->SetTextColor(255);
         $this->SetDrawColor(0,0,0);
         $this->SetLineWidth(.3);
         $this->SetFont('','B');
         // Largeurs des colonnes
         $w = array(15,120,55);
         // En-tête
         for($i=0;$i<count($headerClients);$i++)
             $this->Cell($w[$i],8,utf8_decode($headerClients[$i]),1,0,'C','true');
         $this->Ln();
         
         // Color and font restoration
         $this->SetFillColor(224,235,255);
         $this->SetTextColor(0);
         $this->SetFont('');
         // Data
         $fill = false;
         // Données
         foreach($dataClients as $rowClients)
         {
             $this->Cell($w[0],6,$rowClients['idClient'],'LR',0,'C',$fill);
             $this->Cell($w[1],6,utf8_decode($rowClients['nom'])." ".utf8_decode($rowClients['prenom'])."\n ".utf8_decode($rowClients['adresse'])." ".$rowClients['cp']." ".utf8_decode($rowClients['ville'])." ".$rowClients['pays'],'LR',0,'L',$fill);
             $this->Cell($w[2],6,$rowClients['email'],'LR',0,'C',$fill);
             $this->Ln();
         }
         // Trait de terminaison
         $this->Cell(array_sum($w),0,'','T');
     }

}


// Titres des colonnes donnees
$headerDonnees = array('Client', 'Numéro', 'Date de paiement (par)', 'Total HT', 'Total TTC');
// Titres des colonnes client
$headerClients = array('Client', 'Informations', 'Contact');
// Titres des colonnes totaux
$headerTotaux = array('Total HT','TVA','Total TTC');
$tva = $donneesConfig[6]['valeur'];
//ON CHERCHE LES INFOS DU DOCUMENT
$timeDebut = mktime(0, 00, 0, $_GET['md'], $_GET['jd'], $_GET['ad']);
$timeFin = mktime(23, 59, 59, $_GET['mf'], $_GET['jf'], $_GET['af']);

$sqlRecherche = $bdd -> prepare("SELECT * FROM documents WHERE time_transaction BETWEEN ? AND ? AND numero_facture != ? ORDER BY numero_facture ASC");
$sqlRecherche-> execute(array($timeDebut,$timeFin,""));
$dataDonnees = $sqlRecherche-> fetchAll();

//recherche des clients ayant payés
$sqlRechercheClients = $bdd -> prepare("SELECT * FROM clients WHERE idClient IN (SELECT idUser FROM documents WHERE time_transaction BETWEEN ? AND ? ORDER BY numero_facture ASC)");
$sqlRechercheClients-> execute(array($timeDebut,$timeFin));
$dataDonneesClients = $sqlRechercheClients-> fetchAll();

//ON FAIT LE TOTAL DES LIGNES DU DOCUMENT
$calcul = $bdd -> prepare("SELECT SUM(totalHT) AS total FROM documents WHERE time_transaction BETWEEN ? AND ?");
$calcul-> execute(array($timeDebut,$timeFin));
$dataTotaux = $calcul -> fetch();
$totalHT = number_format($dataTotaux['total'],2);
$totalTTC = number_format($totalHT * $donneesConfig[6]['valeur'],2);
$totalTVA = number_format($totalTTC - $totalHT,2);

// Instanciation de la classe dérivée
$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->header="COMPTABILITE";
$pdf->AddPage();

$pdf->SetFont('Helvetica','',11);
$pdf->SetTextColor(0);

// Infos de la socièté sous le logo à gauche
$pdf->SetFont('Helvetica','',8);
$pdf->Text(10,35,utf8_decode('Adresse : '.$GLOBALS['adresseSociete']));
$pdf->Text(10,40,'Siret : '.$GLOBALS['siretSociete']);

// Infos de la comptabilité calées à droite
$pdf->SetFont('Helvetica','',11);
$pdf->Text(134,28,'Du '.date("d-m-Y",$timeDebut).' au '.date("d-m-Y",$timeFin));

//TOTAUX DE LA PERIODE  
$pdf->SetY(32);
$pdf->SetX(134);
$pdf->Cell(26,8,'TOTAL HT:',1,0,'L');
$pdf->SetY(40);
$pdf->SetX(134);
$pdf->Cell(26,8,'TOTAL TVA:',1,0,'L');
$pdf->SetY(48);
$pdf->SetX(134);
$pdf->Cell(26,8,'TOTAL TTC:',1,0,'L');
$pdf->SetY(32);
$pdf->SetX(160);
$pdf->Cell(26,8,$totalHT,1,0,'R');
$pdf->SetY(40);
$pdf->SetX(160);
$pdf->Cell(26,8,$totalTVA,1,0,'R');
$pdf->SetY(48);
$pdf->SetX(160);
$pdf->Cell(26,8,$totalTTC,1,0,'R');


//ligne TVA dans table de config vaut 1 = PAS DE TVA
if($donneesConfig['6']['valeur'] == 1){
    $pdf->SetFont('Helvetica','',8);
    $pdf->SetY(50);
    $pdf->SetX(10);
    $pdf->Cell(190,8,utf8_decode("TVA non applicable, article 293B du code général des impôts."),0,'C');
}

//TABLEAU DES DONNEES
$pdf->SetY(64);  //a partir de 5 cm du haut
$pdf->TableDonnees($headerDonnees,$dataDonnees);

//TABLEAU DES CLIENTS
$pdf->header="CLIENTS";
$pdf->AddPage();
// Infos de la commande calées à gauche
$pdf->SetFont('Helvetica','',11);
$pdf->Text(134,28,'Du '.date("d-m-Y",$timeDebut).' au '.date("d-m-Y",$timeFin));
//DONNEES CLIENTS 
$pdf->SetFont('Helvetica','',8);
$pdf->SetY(35);  //a partir de 5 cm du haut
$pdf->TableClients($headerClients,$dataDonneesClients);




// Nom du fichier
$nom = 'RefaitesVosJeux-Comptabilite_du_'.date("d-m-Y",$timeDebut).'_au_'.date("d-m-Y",$timeFin).'.pdf';
// Création du PDF
$pdf->Output($nom,'I');?>
?>