<?php
@session_start ();
include_once("../../../config.php");
include("../../../controles/fonctions/cleanUrl.php");

$descriptionPage = "Cette page répertorie tous les partenaires du service. Il s’agit de personnes, d’initiatives ou d’entreprises qui s’inscrivent dans la même démarche autour du jeu, du développement durable, du réemploi et de la réduction des déchets. Déposez vos jeux complets ou incomplets partout en Belgique !";
include_once("../../../bdd/connexion-bdd.php");

if(preg_match('#/don-de-jeux/#',$_SERVER['REQUEST_URI'])){
  $sqlPartenaires = $bdd->prepare("SELECT * FROM partenaires WHERE pays = ? AND don =? ORDER BY nom");
  $sqlPartenaires->execute(array("BE",1));
  $h1 = "Donner ses jeux en Belgique &#127463;&#127466;";
  $titreDeLaPage = "Donner ses jeux en Belgique - ".$GLOBALS['titreDePage'];
  $linkReverse = '/don-de-jeux/partenaires/france/';
}else{
  $sqlPartenaires = $bdd->prepare("SELECT * FROM partenaires WHERE pays = ? ORDER BY nom");
  $sqlPartenaires->execute(array("BE"));
  $h1 = "Nos partenaires Belges &#127463;&#127466;";
  $titreDeLaPage = "Nos partenaires en Belgique - ".$GLOBALS['titreDePage'];
  $descriptionPage = "A définir";
  $linkReverse = '/carte-des-partenaires/france/';
}

include_once("../../../commun/haut_de_page.php");
include_once("../../../commun/alertMessage.php");
?>
<div class="container-fluid">
  <div class="row mt-5 mb-4">
    <div class="col-12 text-center"><h1><?php echo $h1; ?></h1></div>
    <div class="col-11 col-md-8 mx-auto lead text-muted my-2">
        <?php echo $descriptionPage; ?>
    </div>
    <div class="col-8 mx-auto text-right mt-3">
      <a class="text-decoration-none" href="<?php echo $linkReverse; ?>">Voir nos partenaires <span class="wind">&#127467;&#127479;</span></a>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-11 col-md-9 col-lg-6 mx-auto">
      <div id="map"></div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12 text-center small">
      🙏 <a href="https://simplemaps.com/" target="_blank">Simplemap.com</a> ❤️
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-11 col-md-9 col-lg-6 mx-auto text-center p-0">
      <a class="text-decoration-none h5" href="/contact/">Vous avez envie de figurer sur cette carte ?<br/>Prenez contact avec nous !</a>
    </div>
  </div>
</div>

<?php
include_once("../../../commun/bas_de_page.php");

//on cree un tableau vite
$depotArray = [];

$donneesPartenaires = $sqlPartenaires->fetchAll();

//on boucle sur chaque partenaire
foreach($donneesPartenaires as $partenaire){
  $sqlVilleFranceFree = $bdd->prepare("SELECT ville_nom,province,lng,lat FROM villes_belgique_free WHERE ville_id = ?");
  $sqlVilleFranceFree->execute(array($partenaire['id_villes_free']));
  $donneesVilleFranceFree = $sqlVilleFranceFree->fetch();
  
  //affichage port reception jeux ou pas 
  if($partenaire['complet'] == 1 || $partenaire['detachee'] == 1){
    $detailsVente= "<span class=\"badge badge-success m-0\" style=\"\">Dépot sur place possible.</span><br/>";
  }else{
    $detailsVente = "";
  }

    array_push($depotArray,
    [
      "lat" => $donneesVilleFranceFree['lat'],
      "lng" => $donneesVilleFranceFree['lng'],
      "name" => $partenaire['nom'].' à '.$donneesVilleFranceFree['ville_nom'].' ('.$donneesVilleFranceFree['province'].')',
      "description" => '<p style="margin-top:10px; width:100%; text-align:center;"><img style="width:50px;" src="data:image/jpeg;base64,'.$partenaire['image'].'"/></p><p>'.$partenaire['description'].'</p><p><b>Le service collecte:</b><br/>'.$partenaire['collecte'].'</p><p>'.$detailsVente.'</p><p style="width:100%; text-align:center">'.$partenaire['url'].'</p>',
      "url" => $partenaire['url']
    ]);
}


foreach ($depotArray as $keys => $value ) {
  $locations->{$keys} = $value;
}
$jsonStructure = json_encode($locations); 

?>


<script>
  var locations = <?php echo $jsonStructure; ?>;
  console.log(locations)
</script>
<script type="text/javascript" src="/cartes/partenaires/belgique/mapdata.js"></script>		
<script  type="text/javascript" src="/cartes/partenaires/belgique/countrymap.js"></script>