<?php
@session_start ();
$titreDeLaPage = "[ADMIN] - Statistiques Annuelle";
$descriptionPage = "";
require("../../../../controles/fonctions/adminOnline.php");

require_once('../../../../config.php');
require_once('../../../../bdd/connexion-bdd.php');
require_once ('../../../jpgraph/src/jpgraph.php');
require_once ('../../../jpgraph/src/jpgraph_pie.php');
require_once ('../../../jpgraph/src/jpgraph_pie3d.php');

//on verifie les variables
if(!isset($_GET['annee']) || strlen($_GET['annee']) != 4){
    $_SESSION['alertMessage'] = "Donnée manquante...!";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}else{
    $annee = $_GET['annee'];
}

//total des preparations
$totalPreparations = 0;
    for($m=1;$m<=12;$m++){
        $sqlGraph = $bdd->prepare("SELECT SUM(prix_preparation) as total FROM documents WHERE MONTH(FROM_UNIXTIME(time_transaction)) = ? AND YEAR(FROM_UNIXTIME(time_transaction)) = ? AND etat = 2 ");
        $sqlGraph->execute(array($m,$annee));
        $donneesGraph = $sqlGraph->fetch();
        $donneesGraph_100 = number_format($donneesGraph['total'] / 100,2);
        $totalPreparations += $donneesGraph_100;
    }
//total des occasions
$totalOccasions = 0;
    for($m=1;$m<=12;$m++){
        $sqlGraph = $bdd->prepare("SELECT SUM(totalOccasions) as total FROM documents WHERE MONTH(FROM_UNIXTIME(time_transaction)) = ? AND YEAR(FROM_UNIXTIME(time_transaction)) = ? AND etat = 2 ");
        $sqlGraph->execute(array($m,$annee));
        $donneesGraph = $sqlGraph->fetch();
        $donneesGraph_100 = number_format($donneesGraph['total'] / 100,2);
        $totalOccasions += $donneesGraph_100;
    }
//total des expeditions
$totalExpeditions = 0;
    for($m=1;$m<=12;$m++){
        $sqlGraph = $bdd->prepare("SELECT SUM(prix_expedition) as total FROM documents WHERE MONTH(FROM_UNIXTIME(time_transaction)) = ? AND YEAR(FROM_UNIXTIME(time_transaction)) = ? AND etat = 2 ");
        $sqlGraph->execute(array($m,$annee));
        $donneesGraph = $sqlGraph->fetch();
        $donneesGraph_100 = number_format($donneesGraph['total'] / 100,2);
        $totalExpeditions += $donneesGraph_100;
    }
//calcul des pieces
$totalAnnuel = 0;
    for($m=1;$m<=12;$m++){
        $sqlGraph = $bdd->prepare("SELECT SUM(totalHT) as total FROM documents WHERE MONTH(FROM_UNIXTIME(time_transaction)) = ? AND YEAR(FROM_UNIXTIME(time_transaction)) = ? AND etat = 2 ");
        $sqlGraph->execute(array($m,$annee));
        $donneesGraph = $sqlGraph->fetch();
        $donneesGraph_100 = number_format($donneesGraph['total'] / 100,2);
        $totalAnnuel += $donneesGraph_100;
    }
$totalPieces = $totalAnnuel - $totalExpeditions - $totalOccasions - $totalPreparations;

// Some data
$data = array($totalPreparations,$totalOccasions,$totalExpeditions,$totalPieces);

// Create the Pie Graph. 
$graph = new PieGraph(800,570);

$theme_class= new VividTheme;
$graph->SetTheme($theme_class);

// Set A title for the plot
$graph->title->Set("Répartition des ventes de ".$annee);

// Create
$p1 = new PiePlot3D($data);
$p1->SetLegends(array('Préparation / adhésion ('.$totalPreparations.' HT)','Jeux d\'occasion ('.$totalOccasions.' HT)','Port ('.$totalExpeditions.' HT)','Pièces détachées ('.$totalPieces.' HT)'));
$p1->SetLabelMargin(20);
$p1->SetHeight(10);
$graph->Add($p1);
$graph->legend->SetPos(0.01,0.04,'left','top');


$p1->ShowBorder();
$p1->SetColor('black');
$p1->ExplodeSlice(1);
$graph->Stroke();
?>
