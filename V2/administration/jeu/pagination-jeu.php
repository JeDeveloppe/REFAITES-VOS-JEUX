<?php
// require("../bdd/table_config.php");

 //Nous allons afficher x messages par page.
 
//Nous allons maintenant compter le nombre de pages.
$nombreDePages = ceil($rows/$messagesParPage);

 
if(isset($_GET['page'])) // Si la variable $_GET['page'] existe...
{
     $pageActuelle = intval($_GET['page']);
 
     if($pageActuelle>$nombreDePages) // Si la valeur de $pageActuelle (le numéro de la page) est plus grande que $nombreDePages...
     {
          $pageActuelle = 1;
     }
}
else // Sinon
{
     $pageActuelle = 1; // La page actuelle est la n°1    
}
 
$premiereEntree=($pageActuelle-1)*$messagesParPage; // On calcul la première entrée à lire



//recherche des jeux par page
$requetePagination = "$querySql LIMIT ".$premiereEntree.",".$messagesParPage." ";

$sqlRecherchePagination = $bdd ->query($requetePagination);
$donneesJeux = $sqlRecherchePagination->fetchAll();
$count = $sqlRecherchePagination->rowCount(); 
?>