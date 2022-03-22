<?php
@session_start ();
include_once("../../../config.php");
include("../../../controles/fonctions/cleanUrl.php");

include_once("../../../bdd/connexion-bdd.php");

if(preg_match('#/don-de-jeux/#',$_SERVER['REQUEST_URI'])){
  $descriptionPage = "Cette page répertorie tous les partenaires français du service qui collectent et valorisent les jeux d’occasion. En fonction des partenaires, vous pouvez donner vos jouets, vos peluches, vos puzzles ainsi que vos jeux de société complets ou incomplets. Une belle occasion de faire un geste écocitoyen en prolongeant la vie de ces objets !";
  $sqlPartenaires = $bdd->prepare("SELECT * FROM partenaires WHERE pays = ? AND don = ? AND isActif = 1 ORDER BY nom");
  $sqlPartenaires->execute(array("FR",1));
  $h1 = "Donner ses jeux en France &#127467;&#127479;";
  $titreDeLaPage = "Donner ses jeux en France - ".$GLOBALS['titreDePage'];
  $linkReverse = '/don-de-jeux/partenaires/belgique/';
  $texteReverse = "Donner ses jeux en ";
}else{
  $descriptionPage = "Cette page répertorie tous les partenaires français du service. Il s’agit de personnes, d’organismes ou d’entreprises qui s’inscrivent dans la même démarche autour du jeu, du développement durable, du réemploi et de la réduction des déchets. Auprès de ces partenaires vous pouvez acheter, louer ou donner des jeux d’occasion !";
  $sqlPartenaires = $bdd->prepare("SELECT * FROM partenaires WHERE pays = ? AND isActif = 1 ORDER BY nom");
  $sqlPartenaires->execute(array("FR"));
  $h1 = "Nos partenaires Français &#127467;&#127479;";
  $titreDeLaPage = "Nos partenaires en France - ".$GLOBALS['titreDePage'];
  $texteReverse = "Nos partenaires ";
  $linkReverse = '/carte-des-partenaires/belgique/';
}
include_once("../../../commun/haut_de_page.php");
include_once("../../../commun/alertMessage.php");
?>
<div class="container-fluid">
  <div class="row mt-5 mb-4">
    <div class="col-12 text-center"><h1><?php echo $h1; ?></h1></div>
    <div class="col-11 col-md-8 mx-auto lead my-2">
        <?php echo $descriptionPage; ?>
    </div>
    <div class="col-8 mx-auto text-right mt-3">
      <a class="text-decoration-none" href="<?php echo $linkReverse; ?>"><?php echo $texteReverse; ?><span class="wind">&#127463;&#127466;</span></a>
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
  <div class="row mt-5">
    <div class="col-11 col-md-9 col-lg-6 mx-auto text-center p-0">
      <a class="h5" href="/contact/">Vous avez envie de figurer sur cette carte ?<br/>Prenez contact avec nous !</a>
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
  $sqlVilleFranceFree = $bdd->prepare("SELECT ville_nom,ville_departement,lng,lat FROM villes_france_free WHERE ville_id = ?");
  $sqlVilleFranceFree->execute(array($partenaire['id_villes_free']));
  $donneesVilleFranceFree = $sqlVilleFranceFree->fetch();

  //affichage details des ventes
  if($partenaire['complet'] == 1 || $partenaire['detachee'] == 1){
    $detailsVente= "<b>Le service vend:</b><br/>".$partenaire['vend'];
  }else{
    $detailsVente = "";
  }

  if($partenaire['idPartenaire'] == 10 || $partenaire['idPartenaire'] == 14){
    
    if($partenaire['idPartenaire'] == 14){
      $size = 45; //35 commun
    }else{
      $size = 55; //55 pour logo caen
    }
    array_push($depotArray,
    [
      "lat" => $donneesVilleFranceFree['lat'],
      "lng" => $donneesVilleFranceFree['lng'],
      "name" => $partenaire['nom'].' à '.$donneesVilleFranceFree['ville_nom'].' ('.$donneesVilleFranceFree['ville_departement'].')',
      "description" => '<p style="margin-top:10px; width:100%; text-align:center;"><img style="width:100px;" src="data:image/jpeg;base64,'.$partenaire['image'].'"/></p><p>'.$partenaire['description'].'</p><p><b>Le service collecte:</b><br/>'.$partenaire['collecte'].'</p><p>'.$detailsVente.'</p>',
      "url" => $partenaire['url'],
      "type" => "image",
      "image_url" => "https://www.refaitesvosjeux.fr/images/design/logoDepots.png",
      "size" => $size,
    ]);
  }else{
    if($partenaire['ecommerce'] == 1){
      array_push($depotArray,
      [
        "lat" => $donneesVilleFranceFree['lat'],
        "lng" => $donneesVilleFranceFree['lng'],
        "name" => $partenaire['nom'].' à '.$donneesVilleFranceFree['ville_nom'].' ('.$donneesVilleFranceFree['ville_departement'].')',
        "description" => '<p style="margin-top:10px; width:100%; text-align:center;"><img style="width:100px;" src="data:image/jpeg;base64,'.$partenaire['image'].'"/></p><p>'.$partenaire['description'].'</p><p><b>Le service collecte:</b><br/>'.$partenaire['collecte'].'</p><p>'.$detailsVente.'</p><p>Site E-commerce disponible.</p>',
        "url" => $partenaire['url'],
        "size" => "35"
      ]);
    }else{
      array_push($depotArray,
      [
        "lat" => $donneesVilleFranceFree['lat'],
        "lng" => $donneesVilleFranceFree['lng'],
        "name" => $partenaire['nom'].' à '.$donneesVilleFranceFree['ville_nom'].' ('.$donneesVilleFranceFree['ville_departement'].')',
        "description" => '<p style="margin-top:10px; width:100%; text-align:center;"><img style="width:100px;" src="data:image/jpeg;base64,'.$partenaire['image'].'"/></p><p>'.$partenaire['description'].'</p><p><b>Le service collecte:</b><br/>'.$partenaire['collecte'].'</p><p>'.$detailsVente.'</p>',
        "url" => $partenaire['url']
      ]);
    }
  }
}


foreach ($depotArray as $keys => $value ) {
  $locations->{$keys} = $value;
}
$jsonStructure = json_encode($locations); 

?>


<script>
  var locations = <?php echo $jsonStructure; ?>;
</script>
<script type="text/javascript" src="/cartes/partenaires/france/mapdata.js"></script>		
<script  type="text/javascript" src="/cartes/partenaires/france/countrymap.js"></script>