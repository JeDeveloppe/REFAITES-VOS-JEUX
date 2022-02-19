<?php
  if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
  $url = "https"; 
  }else{
  $url = "http"; 
  }
  
// Ajoutez // à l'URL.
$url .= "://"; 
  
// Ajoutez l'hôte (nom de domaine, ip) à l'URL.
$url .= $_SERVER['HTTP_HOST']; 
  
// Ajouter l'emplacement de la ressource demandée à l'URL
$url .= $_SERVER['REQUEST_URI']; 
   

$pattern = implode("|",$GLOBALS['url_exclues_sitemap']);

//si on est en production
if(preg_match('#www#',$_SERVER['HTTP_HOST'])){
  //on exclu certaines url voir $GLOBALS  admin|fbclid
  if(!preg_match('#'.$pattern.'#', $_SERVER['REQUEST_URI'])){
    $sqlSitemapRecherche = $bdd->prepare("SELECT * FROM sitemaps WHERE url = ?");
    $sqlSitemapRecherche->execute(array($url));
    $donneesSitemapRecherche = $sqlSitemapRecherche->fetch();
    $countSitemapRecherche = $sqlSitemapRecherche->rowCount();
 
    //si c'est un retour de bouteille à la mer


    //si url non presente dans la bdd
    if($countSitemapRecherche == 0){
      if(isset($_GET['jeu'])){
        $idJeu = valid_donnees($_GET['jeu']);
      }else{
        $idJeu = 0;
      }
      $sqlSave = $bdd->prepare("INSERT INTO sitemaps SET url = ?, time = ?, vues = ?, idJeu= ?, actif = ?");
      $sqlSave->execute(array($url,time(),0,$idJeu,1));
    }else{
      $nouvelleValeur = $donneesSitemapRecherche['vues'] + 1;
      $sqlUpdate = $bdd->prepare("UPDATE sitemaps SET vues = ? WHERE url = ?");
      $sqlUpdate->execute(array($nouvelleValeur,$url));
    }
  }
}
?>  