<?php
function moisEnTexte($numero){
    $mois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octrobre", "Novembre", "Décembre");
    $index = $numero-1;

    return $mois[$index];
}
?>