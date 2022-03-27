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
    for($m=1;$m<=12;$m++){
        $sqlGraph = $bdd->prepare("SELECT SUM(totalHT) as totalHTmois FROM documents WHERE MONTH(FROM_UNIXTIME(time_transaction)) = ? AND YEAR(FROM_UNIXTIME(time_transaction)) = ? AND etat = 2 ");
        $sqlGraph->execute(array($m,$annee));
        $donneesGraph = $sqlGraph->fetch();
        $donneesGraph_100 = number_format($donneesGraph['totalHTmois'] / 100,2);
        $totalAnnuel += $donneesGraph_100;
        array_push($totaux,$donneesGraph_100);
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

$graph->title->Set("Ventes par mois en ".$annee." \n Total HT: ".$totalAnnuel);

// Display the graph
$graph->Stroke();
?>
