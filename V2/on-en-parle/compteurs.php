<?php
@session_start ();

//NOMBRE DE JEUX SAUVER
$sqlSauver = $bdd -> query('SELECT * FROM documents_lignes WHERE idDocument IN (SELECT idDocument FROM documents WHERE numero_facture != "")');
$nbrJeuxSauver = $sqlSauver->rowCount();
//NOMBRE DE JEUX OCCASION VENDU
$sqlOccasionVendu = $bdd -> query('SELECT * FROM documents_lignes_achats WHERE idDocument IN (SELECT idDocument FROM documents WHERE numero_facture != "")');
$nbrJeuxOccasionVendu = $sqlOccasionVendu->rowCount();
//NOMBRE DE JEUX TOTAL POUR PIECES
$sqlCatalogue = $bdd -> query("SELECT * FROM catalogue WHERE actif = 1 AND accessoire_idCategorie = 0");
$nbrJeuxTotalEnLigne = $sqlCatalogue->rowCount();
//NOMBRE DE JEUX COMPLET TOTAL AU CATALOGUE
$sqlCatalogue = $bdd -> query("SELECT SUM(stock) AS totalJeuxCompletDisponible FROM jeux_complets WHERE actif = 1");
$donneesJeuxComplet = $sqlCatalogue->fetch();
$nbrJeuxCompletEnLigne = $donneesJeuxComplet['totalJeuxCompletDisponible'];
?>

<div class="row justify-content-center">
    <div class="col-12 p-0 text-center mb-2">
        <span data-html="true" data-toggle="tooltip" data-placement="top" title="Nombre de jeux incomplets en ligne" id="odometerJeuxEnLigne" class="odometer"></span>
        <span data-html="true" data-toggle="tooltip" data-placement="top" title="Nombre de jeux complets en ligne" id="odometerOccasionCatalogue" class="odometer mx-3"></span>
        <span data-html="true" data-toggle="tooltip" data-placement="top" title="Nombre de jeux complétés depuis la création du service" id="odometer" class="odometer"></span>
    </div>
</div>


<script>
/*
 * ODOMETRE
 */
let jeuxEnLigne = <?php echo json_decode($nbrJeuxTotalEnLigne); ?>;
let jeuxSauverAvantSite = 120;
let jeuxSauverParLeSite = <?php echo json_encode($nbrJeuxSauver); ?>;
let jeuxOccasionVendu = <?php echo json_encode($nbrJeuxOccasionVendu); ?>;

let totalJeuxSauver = Number(jeuxSauverAvantSite) + Number(jeuxSauverParLeSite) + Number(jeuxOccasionVendu);


let jeuxCompletEnLigne = <?php echo json_encode($nbrJeuxCompletEnLigne); ?>;

if(jeuxEnLigne < 10){
    odometerJeuxEnLigne.innerHTML = 4;
}else if(jeuxEnLigne > 9 && jeuxEnLigne < 100){
    odometerJeuxEnLigne.innerHTML = 31;
}else if(jeuxEnLigne > 99 && jeuxEnLigne < 1000){
    odometerJeuxEnLigne.innerHTML = 300;
}else if(jeuxEnLigne > 999 && jeuxEnLigne < 10000){
    odometerJeuxEnLigne.innerHTML = 1983;
}else if(jeuxEnLigne > 9999 && jeuxEnLigne < 100000){
    odometerJeuxEnLigne.innerHTML = 22220;
}

if(totalJeuxSauver < 10){
    odometer.innerHTML = 4;
}else if(totalJeuxSauver > 9 && totalJeuxSauver < 100){
    odometer.innerHTML = 31;
}else if(totalJeuxSauver > 99 && totalJeuxSauver < 1000){
    odometer.innerHTML = 711;
}else if(totalJeuxSauver > 999 && totalJeuxSauver < 10000){
    odometer.innerHTML = 1983;
}else if(totalJeuxSauver > 9999 && totalJeuxSauver < 100000){
    odometer.innerHTML = 22220;
}

if(jeuxCompletEnLigne < 10){
    odometerOccasionCatalogue.innerHTML = 4;
}else if(jeuxCompletEnLigne > 9 && jeuxCompletEnLigne < 100){
    odometerOccasionCatalogue.innerHTML = 31;
}else if(jeuxCompletEnLigne > 99 && jeuxCompletEnLigne < 1000){
    odometerOccasionCatalogue.innerHTML = 711;
}else if(jeuxCompletEnLigne > 999 && jeuxCompletEnLigne < 10000){
    odometerOccasionCatalogue.innerHTML = 1983;
}else if(jeuxCompletEnLigne > 9999 && jeuxCompletEnLigne < 100000){
    odometerOccasionCatalogue.innerHTML = 22220;
}
setTimeout(function(){
    odometerJeuxEnLigne.innerHTML = jeuxEnLigne;
    odometer.innerHTML = totalJeuxSauver;
    odometerOccasionCatalogue.innerHTML = jeuxCompletEnLigne;
}, 2500);
</script>
<script src="/js/<?php echo $GLOBAL['versionJS'];?>/odometre.js"></script>