<?php 
@session_start ();
include_once("./config.php");
// utilisateur non loggé
$titreDeLaPage = "Catalogue des jeux de pièces détachées disponibles | ".$GLOBALS['titreDePage'];
$descriptionPage = "Catalogue des jeux dont le service dispose de pièces en stock. Retrouvez tous nos jeux disponibles pour compléter les vôtres.";
include_once("./bdd/connexion-bdd.php");
include_once("./commun/haut_de_page.php");
?>
<div class="container">
      
    <style type="text/css">
      /* Set the size of the div element that contains the map */
      #map {
        height: 400px;
        /* The height is 400 pixels */
        width: 400px;
        /* The width is the width of the web page */
      }
    </style>
    <script>
      // Initialize and add the map
      function initMap() {
        // The location of Uluru
        const uluru = { lat: -25.344, lng: 131.036 };
        // The map, centered at Uluru
        const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 4,
          center: uluru,
        });
        // The marker, positioned at Uluru
        const marker = new google.maps.Marker({
          position: uluru,
          map: map,
        });
      }
    </script>

    <!--The div element for the map -->
    <div id="map"></div>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZSfWgkkxjh1XGd01jlpWUaVz5o6ElazU&callback=initMap&libraries=&v=weekly"
      async
    ></script>








</div>

<?php
require("./commun/bas_de_page.php");
?>
