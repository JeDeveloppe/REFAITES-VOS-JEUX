<?php
@session_start ();
$titreDeLaPage = "[ADMIN] - Statistiques Annuelle";
$descriptionPage = "";
require("../../../../controles/fonctions/adminOnline.php");

require_once('../../../../config.php');
require_once('../../../../bdd/connexion-bdd.php');
require_once ('../../../jpgraph/src/jpgraph.php');
require_once ('../../../jpgraph/src/jpgraph_bar.php');

//on verifie les variables
if(!isset($_GET['annee']) || strlen($_GET['annee']) != 4){
    $_SESSION['alertMessage'] = "Donnée manquante...!";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}else{
    $annee = $_GET['annee'];
}

$totaux = [];
$totalAnnuel = 0;

//on boucle par mois
for($m=1;$m<=12;$m++){
    $sqlJeuxOccasionVendus = $bdd->prepare("SELECT SUM(c.poidBoite)
    FROM catalogue c LEFT JOIN documents_lignes_achats dd ON dd.idCatalogue = c.idCatalogue
    LEFT JOIN documents d ON d.idDocument = dd.idDocument
    WHERE d.etat = 2 AND MONTH(FROM_UNIXTIME(d.time_transaction)) = ? AND YEAR(FROM_UNIXTIME(d.time_transaction)) = ?");
    $sqlJeuxOccasionVendus->execute(array($m,$annee));
    $donneesDuMois = $sqlJeuxOccasionVendus->fetch();

    //si pas de resultat on dit 0 et on pousse dans la tableau total de jpgraph
    if($donneesDuMois['SUM(c.poidBoite)'] < 1){
        array_push($totaux,0);
    }else{
        array_push($totaux,$donneesDuMois['SUM(c.poidBoite)']);
    }
    //calcul total du poid (= sommes des mois)
    $totalAnnuel += $donneesDuMois['SUM(c.poidBoite)'];
}

$data1y=$totaux;

// Create the graph. These two calls are always required
$graph = new Graph(1050,600,'auto');
$graph->SetScale("textlin");

$theme_class = new VividTheme;
$graph->SetTheme($theme_class);

// $graph->yaxis->SetTickPositions(array(0,30,60,90,120,150,180,210,240,270,300), array(15,45,75,105,135,165,195,225));
$graph->yaxis->SetTextTickInterval(1,2);
$graph->SetBox(false);

$graph->ygrid->SetFill(false);
$graph->xaxis->SetTickLabels(array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre'));
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

// Create the bar plots
$b1plot = new BarPlot($data1y);
$b1plot->SetLegend($annee);


// Create the grouped bar plot
$gbplot = new GroupBarPlot(array($b1plot));
// ...and add it to the graPH
$graph->Add($gbplot);
$graph->legend->SetPos(0.5,0.92,'center','bottom');


$b1plot->SetColor("white");
$b1plot->SetFillColor("#cc1111");
$b1plot->value->Show();

$graph->title->Set("Ventes par mois en ".$annee." \n Total grammes: ".$totalAnnuel);

// Display the graph
$graph->Stroke();
?>
