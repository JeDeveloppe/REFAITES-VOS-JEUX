<?php
@session_start ();
include_once("../config.php");
$titreDeLaPage = "Dons de jeux | ".$GLOBALS['titreDePage'];
$descriptionPage = "Vous pouvez faire des dons de jeux, complet ou incomplet, le service saura leur donner une seconde vie !";
include_once("../bdd/connexion-bdd.php");
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");

$sqlDons = $bdd->query("SELECT * FROM dons ORDER BY nom");
$donneesDons = $sqlDons->fetch();

$sqlBouteille = $bdd -> query("SELECT * FROM bouteille_mer");
$nbrBouteille = $sqlBouteille->rowCount();
?>
<div class="container-fluid">
    <h1 class="col-12 text-center mt-4">Les demandes</h1>
    <div class="row py-2">
        <div class="col-11 col-lg-8 mx-auto mt-3">
            <p class="mt-2">
            Le formulaire des bouteilles à la mer déborde....<br/>
            Le service à besoin de vous pour satisfaire certaines demandes...<br/>
            Si vous avez certains de ces jeux, le service vous remercie de participer à l'esprit du site en les envoyants...
            </p>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-11 mx-auto text-center">
            <div class="col-12 h3 p-0">Nombre de bouteilles déja lancées : <div id="odometerbouteilleLancee" class="odometer"></div></div>
        </div>
    </div>
    <div class="row py-2">
        <table class="table table-sm table-striped mt-4 col-11 col-md-9 col-lg-6 mx-auto text-center">
            <thead>
                <th>Image</th>
                <th>Nom du jeu</th>
                <th>Demande</th>
            </thead>
            <tbody>
                <?php
                    while($donneesDons){
                        echo '
                        <tr>
                            <td class="text-center align-middle">
                                <div class="divImgPresentationDon mt-4">
                                    <div class="zoom">
                                        <div class="zoom__top zoom__left"></div>
                                        <div class="zoom__top zoom__centre"></div>
                                        <div class="zoom__top zoom__right"></div>
                                        <div class="zoom__middle zoom__left"></div>
                                        <div class="zoom__middle zoom__centre"></div>
                                        <div class="zoom__middle zoom__right"></div>
                                        <div class="zoom__bottom zoom__left"></div>
                                        <div class="zoom__bottom zoom__centre"></div>
                                        <div class="zoom__bottom zoom__right"></div>
                                        <img class="zoom__image" src="data:image/jpeg;base64,'.$donneesDons['image'].'"/>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle">'.$donneesDons['nom'].'</td>
                            <td class="align-middle">';
                                if($donneesDons['demande'] == 2){
                                        echo '<i class="fas fa-thermometer-full text-danger fa-2x"></i>';
                                    }else{
                                        echo '<i class="fas fa-thermometer-three-quarters text-warning fa-2x"></i>';
                                    }
                                echo '
                            </td>
                        </tr>';
                        $donneesDons = $sqlDons->fetch();
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
/*
 * ODOMETRE
 */
let bouteilleLancee = <?php echo json_decode($nbrBouteille); ?>;

if(bouteilleLancee < 10){
    odometerbouteilleLancee.innerHTML = 4;
}else if(bouteilleLancee > 9 && bouteilleLancee < 100){
    odometerbouteilleLancee.innerHTML = 31;
}else if(bouteilleLancee > 99 && bouteilleLancee < 1000){
    odometerbouteilleLancee.innerHTML = 300;
}else if(bouteilleLancee > 999 && bouteilleLancee < 10000){
    odometerbouteilleLancee.innerHTML = 1983;
}else if(bouteilleLancee > 9999 && bouteilleLancee < 100000){
    odometerbouteilleLancee.innerHTML = 22220;
}
setTimeout(function(){
    odometerbouteilleLancee.innerHTML = bouteilleLancee;
}, 2500);
</script>
<script src="/js/<?php echo $GLOBAL['versionJS'];?>/odometre.js"></script>
<?php
include_once("../commun/bas_de_page.php");
?>
