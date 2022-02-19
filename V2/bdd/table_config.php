<?php
@session_start ();
$sqlConfig = $bdd->query("SELECT * FROM configAdmin");
$donneesConfig = $sqlConfig-> fetchAll();
$nbrLigneConfig = $sqlConfig-> rowCount();
?>