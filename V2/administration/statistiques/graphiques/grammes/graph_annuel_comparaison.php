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
if(!isset($_GET['annee']) || strlen($_GET['annee']) != 4 ){
    $_SESSION['alertMessage'] = "Donnée manquante...!";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}else{
    $anneeN = $_GET['annee'];
    $anneePassee = $_GET['annee'] -1;
}


$totaux1y = [];
    for($m=1;$m<=12;$m++){
        $sqlJeuxOccasionVendus = $bdd->prepare("SELECT SUM(c.poidBoite)
        FROM catalogue c LEFT JOIN documents_lignes_achats dd ON dd.idCatalogue = c.idCatalogue
        LEFT JOIN documents d ON d.idDocument = dd.idDocument
        WHERE d.etat = 2 AND MONTH(FROM_UNIXTIME(d.time_transaction)) = ? AND YEAR(FROM_UNIXTIME(d.time_transaction)) = ?");
        $sqlJeuxOccasionVendus->execute(array($m,$anneePassee));
        $donneesDuMois = $sqlJeuxOccasionVendus->fetch();
    
        //si pas de resultat on dit 0 et on pousse dans la tableau total de jpgraph
        if($donneesDuMois['SUM(c.poidBoite)'] < 1){
            array_push($totaux1y,0);
        }else{
            array_push($totaux1y,$donneesDuMois['SUM(c.poidBoite)']);
        }
    }
$data1y=$totaux1y;

$totaux2y = [];
    for($m=1;$m<=12;$m++){
        $sqlJeuxOccasionVendus = $bdd->prepare("SELECT SUM(c.poidBoite)
        FROM catalogue c LEFT JOIN documents_lignes_achats dd ON dd.idCatalogue = c.idCatalogue
        LEFT JOIN documents d ON d.idDocument = dd.idDocument
        WHERE d.etat = 2 AND MONTH(FROM_UNIXTIME(d.time_transaction)) = ? AND YEAR(FROM_UNIXTIME(d.time_transaction)) = ?");
        $sqlJeuxOccasionVendus->execute(array($m,$anneeN));
        $donneesDuMois = $sqlJeuxOccasionVendus->fetch();

        //si pas de resultat on dit 0 et on pousse dans la tableau total de jpgraph
        if($donneesDuMois['SUM(c.poidBoite)'] < 1){
            array_push($totaux2y,0);
        }else{
            array_push($totaux2y,$donneesDuMois['SUM(c.poidBoite)']);
        }
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

$graph->title->Set("Grammes par mois ".$anneePassee." - ".$anneeN);


// Display the graph
$graph->Stroke();
?>
