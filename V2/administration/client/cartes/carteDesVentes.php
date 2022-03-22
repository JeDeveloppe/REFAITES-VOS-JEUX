<?php
@session_start ();
include_once("../../../config.php");

include_once("../../../bdd/connexion-bdd.php");


include_once("../../../commun/haut_de_page.php");
include_once("../../../commun/alertMessage.php");
?>
<div class="container-fluid">
  <div class="row mt-5 mb-4">
    <div class="col-11 col-md-9 col-lg-6 mx-auto text-center">
      <form action="" method="POST">
        <select name="pays">
          <option value="">Choisir le pays...</option>
          <option value="france">FRANCE</option>
          <option value="belgique">BELGIQUE</option>
        </select>
        <button class="btn btn-info" type="submit">Voir</button>
      </form>
    </div>
  </div>
  <div class="row mt-5">
    <div class="col-11 col-md-9 col-lg-6 mx-auto">
      <div id="map"></div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12 text-center small">
      🙏 <a href="https://simplemaps.com/" target="_blank">Simplemap.com</a> ❤️
    </div>
  </div>
</div>

<?php
include_once("../../../commun/bas_de_page-admin.php");

if(isset($_POST['pays']) && !empty($_POST['pays'])){

  if($_POST['pays'] == 'france'){
    $columnPays = "FR";
    $table = "villes_france_free";
  }else{
    $columnPays = "BE";
    $table = "villes_belgique_free";
  }

  $villes = [];

  $documentsSql = $bdd->query("SELECT cpFacturation, villeFacturation FROM clients INNER JOIN documents WHERE documents.idUser = clients.idClient AND documents.etat = 2 ");
  $documents = $documentsSql->fetchAll();

  

  foreach($documents as $document){
    $cp = $document['cpFacturation'];
    $ville = $document['villeFacturation'];

    $querySql = "SELECT ville_nom,lng,lat FROM $table WHERE ville_code_postal = ? AND ville_nom = ?";

    $sqlVilleFree = $bdd->prepare($querySql);
    $sqlVilleFree->execute(array($cp,$ville));
    $donneesVilleFree = $sqlVilleFree->fetch();

    if($donneesVilleFree['lat'] != null){
      array_push($villes,
      [
        "lat" => $donneesVilleFree['lat'],
        "lng" => $donneesVilleFree['lng'],
        "name" => $donneesVilleFree['ville_nom'],
        "size" => "35"
      ]);
    }

  }

  foreach ($villes as $keys => $value ) {
    $locations->{$keys} = $value;
  }

  $jsonStructure = json_encode($locations); 

  echo '
  <script>
    var locations = '.$jsonStructure.';

  </script>
  <script type="text/javascript" src="/administration/client/cartes/'.$_POST['pays'].'/mapdata.js"></script>		
  <script  type="text/javascript" src="/administration/client/cartes/'.$_POST['pays'].'/countrymap.js"></script>';

  exit();

}
?>