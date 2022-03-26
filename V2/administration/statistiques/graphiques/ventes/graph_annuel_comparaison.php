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
if(!isset($_GET['anneeN']) || strlen($_GET['anneeN']) != 4 ){
    $_SESSION['alertMessage'] = "Donnée manquante...!";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}else{
    $anneeN = $_GET['anneeN'];
    $anneePassee = $_GET['anneeN'] -1;
}


$totaux1y = [];
    for($m=1;$m<=12;$m++){
        $sqlGraph = $bdd->prepare("SELECT SUM(totalHT) as totalHTmois FROM documents WHERE MONTH(FROM_UNIXTIME(time_transaction)) = ? AND YEAR(FROM_UNIXTIME(time_transaction)) = ? AND etat = 2 ");
        $sqlGraph->execute(array($m,$anneePassee));
        $donneesGraph = $sqlGraph->fetch();
        $donneesGraph_100 = $donneesGraph['totalHTmois'] / 100;
        array_push($totaux1y,$donneesGraph_100);
    }
$data1y=$totaux1y;

$totaux2y = [];
    for($m=1;$m<=12;$m++){
        $sqlGraph2 = $bdd->prepare("SELECT SUM(totalHT) as totalHTmois FROM documents WHERE MONTH(FROM_UNIXTIME(time_transaction)) = ? AND YEAR(FROM_UNIXTIME(time_transaction)) = ? AND etat = 2 ");
        $sqlGraph2->execute(array($m,$anneeN));
        $donneesGraph2 = $sqlGraph2->fetch();
        $donneesGraph2_100 = $donneesGraph2['totalHTmois'] / 100;
        array_push($totaux2y,$donneesGraph2_100);
    }
$data2y=$totaux2y;

// Create the graph. These two calls are always required
$graph = new Graph(1050,600,'auto');
$graph->SetScale("textlin");

//choix du theme
$theme_class=new AquaTheme;
$graph->SetTheme($theme_class);

//axe des Y
//$graph->yaxis->SetTickPositions(array(0,30,60,90,120,150,180,210,240,270,300), array(15,45,75,105,135,165,195,225));
$graph->yaxis->SetTextTickInterval(1,2);
$graph->SetBox(false);

$graph->ygrid->SetFill(false);
$graph->xaxis->SetTickLabels(array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre'));
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

// Create the bar plots
$b1plot = new BarPlot($data1y);
$b1plot->SetLegend($anneePassee);
$b2plot = new BarPlot($data2y);
$b2plot->SetLegend($anneeN);

// Create the grouped bar plot
$gbplot = new GroupBarPlot(array($b1plot,$b2plot));
// ...and add it to the graPH
$graph->Add($gbplot);
$graph->legend->SetPos(0.5,0.92,'center','bottom');


$b1plot->SetColor("white");
$b1plot->SetFillColor("#cc1111");
$b1plot->value->Show();

$b2plot->SetColor("white");
$b2plot->SetFillColor("#11cccc");
$b2plot->value->Show();

$graph->title->Set("Ventes par mois (HT) ".$anneePassee." / ".$anneeN);


// Display the graph
$graph->Stroke();
?>
