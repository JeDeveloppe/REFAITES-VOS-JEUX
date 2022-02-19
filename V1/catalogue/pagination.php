<?php
require("../bdd/table_config.php");

$messagesParPage= $donneesConfig[0]['valeur']; //Nous allons afficher x messages par page.
 
//Nous allons maintenant compter le nombre de pages.
$nombreDePages=ceil($nbrJeux/$messagesParPage);
 
if(isset($_GET['page']) && is_numeric($_GET['page'])) // Si la variable $_GET['page'] existe...
{
     $pageActuelle = intval($_GET['page']);
 
     if($pageActuelle>$nombreDePages) // Si la valeur de $pageActuelle (le numéro de la page) est plus grande que $nombreDePages...
     {
          $pageActuelle = 1;
     }
}
else // Sinon
{
     if(isset($_GET['page']) && !is_numeric($_GET['page'])){
     $_SESSION['alertMessage'] = "On ne joue PAS avec les variables...";
     $_SESSION['alertMessageConfig'] = "danger";
     $pageActuelle=1; // La page actuelle est la n°1    
     require("../commun/alertMessage.php");
     }else{
          $pageActuelle=1; // La page actuelle est la n°1    
     }
}
 
$premiereEntree=($pageActuelle-1)*$messagesParPage; // On calcul la première entrée à lire



//SI C'EST LA PAGE DU CATALOGUE
if(preg_match('#catalogue#',$_SERVER['REQUEST_URI'])){

     $requeteJeux = "SELECT * FROM catalogue $requeteRecherche AND accessoire_idCategorie = 0 AND actif = 1 ORDER BY $tri $ordreTri LIMIT ".$premiereEntree.",".$messagesParPage." ";


     try
     {   
     $sqlJeux = $bdd->query($requeteJeux);
     $boites = $sqlJeux-> fetchAll();
     $count = $sqlJeux->rowCount();
     }
     catch(Exception $e){
     // en cas d'erreur :
     $_SESSION['alertMessage'] = $e->getMessage();
     $_SESSION['alertMessage-details'] = "Pagination";
     $_SESSION['alertMessageConfig'] = "warning";
     header("Location: ../erreurs/500.php");
     exit(); 
     }
}
?>