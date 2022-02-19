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
        $ligneDocument = 1;
        foreach($dataDonnees as $rowDonnees)
        {
            $this->Cell($w[0],8,$ligneDocument,'LR',0,'C',$fill);
            $this->Cell($w[1],8,$rowDonnees['numero_facture'],'LR',0,'C',$fill);
            $this->Cell($w[2],8,date("d-m-Y",$rowDonnees['time_transaction']).' ('.$rowDonnees['moyen_paiement'].')','LR',0,'C',$fill);
            $this->Cell($w[3],8,number_format($rowDonnees['totalHT'] / 100,"2",",",""),'LR',0,'C',$fill);
            $this->Cell($w[4],8,number_format($rowDonnees['totalTTC'] / 100,"2",",",""),'LR',0,'C',$fill);
            $this->Ln();
            $ligneDocument++;
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
         $w = array(30,140,20);
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
         $ligneDocument = 1;
         foreach($dataClients as $rowClients)
         {
            $detailClientFacturation = explode('<br/>',$rowClients['adresse_facturation']);
            if($detailClientFacturation[3] == "FR"){
                $flag = "&#127467;&#127479;"; //france
            }else{
                $flag = "&#127463;&#127466;"; //belgium flag
            }
             $this->Cell($w[0],8,$ligneDocument,'LR',0,'C',$fill);
             $this->Cell($w[1],8,utf8_decode($detailClientFacturation[0])." ".utf8_decode($detailClientFacturation[1])." ".utf8_decode($detailClientFacturation[2]),'LR',0,'L',$fill);
             $this->Cell($w[2],8,$detailClientFacturation[3],'LR',0,'C',$fill);
             $this->Ln();
             $ligneDocument++;
         }
         // Trait de terminaison
         $this->Cell(array_sum($w),0,'','T');
     }

}


// Titres des colonnes donnees
$headerDonnees = array('Doc / client', 'Numéro', 'Date de paiement (par)', 'Total HT', 'Total TTC');
// Titres des colonnes client
$headerClients = array('Doc / client', 'Adresse de facturation', 'Pays');
// Titres des colonnes totaux
$headerTotaux = array('Total HT','TVA','Total TTC');
$tva = $donneesConfig[6]['valeur'];
//ON CHERCHE LES INFOS DU DOCUMENT
$timeDebut = mktime(0, 00, 0, $_GET['md'], $_GET['jd'], $_GET['ad']);
$timeFin = mktime(23, 59, 59, $_GET['mf'], $_GET['jf'], $_GET['af']);



$sqlRecherche = $bdd -> prepare("SELECT * FROM documents WHERE time_transaction BETWEEN ? AND ? AND etat = ? ORDER BY numero_facture ASC");
$sqlRecherche-> execute(array($timeDebut,$timeFin,2));
$dataDonnees = $sqlRecherche-> fetchAll();

//ON FAIT LE TOTAL DES LIGNES DU DOCUMENT
$calcul = $bdd -> prepare("SELECT SUM(totalHT) AS total FROM documents WHERE time_transaction BETWEEN ? AND ?");
$calcul-> execute(array($timeDebut,$timeFin));
$dataTotaux = $calcul -> fetch();
$totalHT = $dataTotaux['total'];
$totalTTC = $totalHT * $donneesConfig[6]['valeur'];
$totalTVA = $totalTTC - $totalHT;

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
$pdf->Cell(26,8,number_format($totalHT / 100,2,",",""),1,0,'R');
$pdf->SetY(40);
$pdf->SetX(160);
$pdf->Cell(26,8,number_format($totalTVA / 100,2,",",""),1,0,'R');
$pdf->SetY(48);
$pdf->SetX(160);
$pdf->Cell(26,8,number_format($totalTTC / 100,2,",",""),1,0,'R');


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
$pdf->TableClients($headerClients,$dataDonnees);




// Nom du fichier
$nom = 'RefaitesVosJeux-Comptabilite_du_'.date("d-m-Y",$timeDebut).'_au_'.date("d-m-Y",$timeFin).'.pdf';
// Création du PDF
$pdf->Output($nom,'I');?>
?>