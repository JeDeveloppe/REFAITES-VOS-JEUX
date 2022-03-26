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

$totauxVentes = [];
$totauxDons = [];

    for($m=1;$m<=12;$m++){
        $sqlVentes = $bdd->prepare("SELECT SUM(qte) as totalQte FROM documents_lignes_achats dl LEFT JOIN documents d ON dl.idDocument = d.idDocument WHERE MONTH(FROM_UNIXTIME(d.time_transaction)) = ? AND YEAR(FROM_UNIXTIME(d.time_transaction)) = ? AND etat = 2 ");
        $sqlVentes->execute(array($m,$annee));
        $donneesVentes = $sqlVentes->fetch();
        if($donneesVentes['totalQte'] < 1){
            array_push($totauxVentes,0);
        }else{
            array_push($totauxVentes,$donneesVentes['totalQte']);
        }

        //dons
        $sqlDons = $bdd->prepare("SELECT SUM(stock) as totalQte FROM jeux_complets WHERE MONTH(FROM_UNIXTIME(timeDon)) = ? AND YEAR(FROM_UNIXTIME(timeDon)) = ? AND actif = 0 ");
        $sqlDons->execute(array($m,$annee));
        $donneesDons = $sqlDons->fetch();
        if($donneesDons['totalQte'] < 1){
            array_push($totauxDons,0);
        }else{
            array_push($totauxDons,$donneesDons['totalQte']);
        }
    }

$data1 = $totauxVentes;
$data2 = $totauxDons;

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
$b1plot = new BarPlot($data1);
$b1plot->SetLegend("Ventes");
$b2plot = new BarPlot($data2);
$b2plot->SetLegend("Dons");


// Create the grouped bar plot
$gbbplot = new AccBarPlot(array($b1plot, $b2plot));
$gbplot = new GroupBarPlot(array($gbbplot));
// ...and add it to the graPH

$graph->Add($gbplot);
$graph->legend->SetPos(0.5,0.92,'center','bottom');


$b1plot->SetColor("white");
$b1plot->SetFillColor("#cc1111");
$b1plot->value->Show();

$graph->title->Set("Jeux d'occasion \n Ventes / Dons par mois en ".$annee);

// Display the graph
$graph->Stroke();
?>
