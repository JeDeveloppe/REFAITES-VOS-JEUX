<?php
@session_start ();
$titreDeLaPage = "[ADMIN] - Graphiques";
$descriptionPage = "";
require("../../../controles/fonctions/adminOnline.php");
include_once('../../../config.php');
include_once('../../../bdd/connexion-bdd.php');
include_once("../../../bdd/table_config.php");
include_once("../../../commun/haut_de_page.php");
include_once("../../../commun/alertMessage.php");
?>

<div class="container-fluid mb-5">
    <div class="row">
        <div class="col-6 mx-auto">
            <div class="col-12 h3 text-center mt-5">Graphiques:</div>
            <div class="input-group col-11 mx-auto mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">Année:</span>
                </div>
                <input type="text" class="form-control text-center" placeholder="ex: 2021" id="anneeGraphique">
            </div>
            <div class="col-12 mt-4 text-center d-flex flex-column">
                <div>Les ventes:</div>
                <a class=" link btn btn-info disabled mb-2" href="/admin/statistiques/ventes/" data-original-href="/admin/statistiques/ventes/" target="_blank">LES VENTES de l'année N</a>
                <a class=" link btn btn-info disabled mb-2" href="/admin/statistiques/ventes/repartition/" data-original-href="/admin/statistiques/ventes/repartition/" target="_blank">REPARTITION de l'année N</a>
                <a class=" link btn btn-info disabled mb-2" href="/admin/statistiques/ventes/comparaison/" data-original-href="/admin/statistiques/ventes/comparaison/" target="_blank">COMPARAISON Année N-1 et N</a>
            </div>
            <div class="col-12 mt-2 text-center d-flex flex-column">
                <div>Les boites: (ventes et dons)</div>
                <a class=" link btn btn-info disabled mb-2" href="/admin/statistiques/boites/" data-original-href="/admin/statistiques/boites/" target="_blank">Évolution de l'année N</a>
            </div>
            <div class="col-12 mt-2 text-center d-flex flex-column">
                <div>Les grammes: (total)</div>
                <a class=" link btn btn-info disabled mb-2" href="/admin/statistiques/grammes/" data-original-href="/admin/statistiques/grammes/" target="_blank">Évolution de l'année N</a>
                <a class=" link btn btn-info disabled mb-2" href="/admin/statistiques/grammes/comparaison/" data-original-href="/admin/statistiques/grammes/comparaison/" target="_blank">COMPARAISON Année N-1 et N</a>
            </div>
            <div class="col-12 mt-2 text-center d-flex flex-column">
                <div>Les grammes: (juste DEEE)</div>
                <a class=" link btn btn-info disabled mb-2" href="/admin/statistiques/grammes/deee/" data-original-href="/admin/statistiques/grammes/deee/" target="_blank">Évolution de l'année N</a>
                <a class=" link btn btn-info disabled mb-2" href="/admin/statistiques/grammes/comparaison/deee/" data-original-href="/admin/statistiques/grammes/comparaison/deee/" target="_blank">COMPARAISON Année N-1 et N</a>
            </div>
            <div class="col-12 mt-2 text-center d-flex flex-column">
                <div>Les adhérents:</div>
                <a class=" link btn btn-info disabled mb-2" href="/admin/statistiques/adherents/" data-original-href="/admin/statistiques/adherents/" target="_blank">Évolution de l'année N</a>
            </div>

        </div>
    </div>
</div>

<?php include_once("../../../commun/bas_de_page-admin.php");?>

<script>
    let anneeGraphique = document.getElementById('anneeGraphique');
let liens = document.getElementsByClassName('link');
 
let isModifiedLinks = false;
  
anneeGraphique.addEventListener('keyup', () => {
  if(
    anneeGraphique.value.length == 4 &&
    anneeGraphique.value >= 2020 &&
    anneeGraphique.value.match(/^\d{4}$/)
  ){
    // l'utilisateur à entré une année avec un format valide
     
    if(!isModifiedLinks) {
        // mais les liens ont déja était modifier
      // il faut leurs rendre leurs valeur d'origine avant
      // de les remodifier pour éviter d'avoir une valeur comme:
      // https://.../2021/2022/ dans les liens
       
      // le script devrait être capable de parcourir
      // les liens et d'effectuer l'opération inverse de la modification
      // des liens
       
      // parcour les liens pour leurs rendre leurs valeur d'origine
       
      for(const link of liens) {
        // rend la valeur d'origine au liens
        link.href = link.getAttribute("data-original-href");
      }
       
      // maintenant que les liens ont une valeur "non-modifier"
      // ont peut leurs appliquer la nouvelle "année" choisie par
      // l'utilisateur
      for(const link of liens){
        link.classList.remove('disabled');
        console.log(link.href += anneeGraphique.value+"/");
      }
    } else {
        // les liens n'ont pas été modifier
      // la boucle apporte la modifications sur les liens
      for(const link of liens){
        link.classList.remove('disabled');
        console.log(link.href += anneeGraphique.value+"/");
      }
      // indique que maintenant les liens sont modifier
      isModifiedLinks = true;
    }
  }
});
</script>