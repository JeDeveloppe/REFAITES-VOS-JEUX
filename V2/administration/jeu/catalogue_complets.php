<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
require('../../bdd/table_config.php');

$tva = $donneesConfig[6]['valeur'];

$titreDeLaPage = "[ADMIN] - Les jeux complets";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");

include_once("../../commun/alertMessage.php");

//menu alphabet
$menuAlphabet = "A";
?>

<div class="container-fluid py-4 bg-secondary">
    <div class="col-12 h2 text-center"> LES JEUX COMPLETS </div>

        <div class="row my-5">
            <div class="col-6 mx-auto">
                <form class="d-flex justify-content-center" method="post" action="/administration/jeu/ctrl/ctrl-recherche-reference-complet.php">
                    <input class="col-6 form-control mr-2 align-self-center" type="search" name="recherche" placeholder="Rechercher une référence" aria-label="Rechercher" required>
                    <button class="btn btn-outline-success mt-0" type="submit">Chercher</button>
                    <a href="/admin/jeu/catalogue/complet/" class="btn btn-outline-danger ml-2">Éffacer la recherche</a>
                </form>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 p-0">
                <nav aria-label="Pagination menu alphabet">
                    <ul class="pagination d-flex flex-wrap justify-content-around">
                    <?php
                        //pas alphabetique
                        $searchAlphabetLettre = $bdd-> prepare("SELECT * FROM catalogue WHERE nom REGEXP '^[0-9]+' AND isComplet = ? ORDER BY nom");
                        $searchAlphabetLettre-> execute(array(1));
                        $countAlphabetLettre = $searchAlphabetLettre-> rowCount();
                        echo '<li class="page-item"><a class="page-link" href="/admin/jeu/catalogue/complet/">#<br/><span class="small">('.$countAlphabetLettre.')</span></a></li>';
                        
                        for($x=1;$x<27;$x++)
                        {
                        $searchAlphabetLettre = $bdd-> prepare("SELECT * FROM catalogue WHERE nom LIKE ? AND isComplet = 1 ORDER BY nom");
                        $searchAlphabetLettre-> execute(array($menuAlphabet.'%'));
                        $countAlphabetLettre = $searchAlphabetLettre-> rowCount();
    
                        echo '<li class="page-item"><a class="page-link" href="/admin/jeu/catalogue/complet/'.$menuAlphabet.'/">'.$menuAlphabet++.'<br/><span class="small">('.$countAlphabetLettre.')</span></a></li>';
                        }
                        
                    ?>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-11 mx-auto text-start bg-light card">
                <?php
                    if(isset($_GET['lettre']) && ctype_alpha($_GET['lettre']) && strlen($_GET['lettre']) == 1){
                        $lettre = $_GET['lettre'];
                        $query = "SELECT * FROM catalogue WHERE nom LIKE ? AND isComplet = 1 ORDER BY nom";
                        $arrayQuery = $lettre.'%';
                    }else{
                        $lettre = "#";
                        $query = "SELECT * FROM catalogue WHERE nom REGEXP '^[0-9]+' AND isComplet = ? ORDER BY nom";
                        $arrayQuery = 1;
                    }
                    // else{
                    //     $lettre = "A";
                    //     $query = "SELECT * FROM catalogue WHERE nom LIKE ? AND isComplet = 1 ORDER BY nom";
                    //     $arrayQuery = 'A%';
                    // }
                    echo strtoupper($lettre);
                ?>
            </div>
        </div>
        <?php
            $searchAlphabet = $bdd->prepare($query);
            $searchAlphabet->execute(array($arrayQuery));
            $donneesAlphabet = $searchAlphabet-> fetchAll();
            $countAlphabet = $searchAlphabet-> rowCount();

            foreach($donneesAlphabet as $donnees){
                echo '
                <div class="row my-5">
                    <div class="card col-11 mx-auto p-0">
                        <div class="card-header d-flex justify-content-around align-items-center">
                            <div class="col" id="'.$donnees['idCatalogue'].'">'.$donnees['nom'].'</div>
                            <div class="col">'.$donnees['editeur'].'</div>
                            <div class="col">'.$donnees['annee'].'</div>
                            <div class="col"><a href="/administration/jeu/edition.php?etat=offline&tri=&jeu='.$donnees['idCatalogue'].'" class="btn btn-info">Retour "coquille"</a></div>
                        </div>
                        <div class="card-body p-0">
                            <div class="col-12 p-0 mb-4 d-flex flex-wrap">
                                <div class="col-4 align-self-center text-center p-0">
                                    <div class="divImgCatalogueAdmin">';
                                            echo '<img src="data:image/jpeg;base64,'.$donnees['imageBlob'].'"/>';
                                        echo '
                                    </div>
                                </div>
                                <div class="col-8 p-0 mt-4">
                                    <form method="post" action="/administration/jeu/ctrl/ctrl-new-complet.php" class="d-flex flex-wrap">
                        
                                        <div class="form-group col-12">
                                            Prix TTC de référence: <span class="prixHtReference">'.number_format($donnees['prix_HT'] * $tva/ 100,2,",","").' €</span>
                                        </div> 
                                        <div class="col-12 mb-2">
                                            <div class="form-check col-11 mx-auto">
                                                <input class="form-check-input" type="checkbox" value="" id="checkNeuf">
                                                <label class="form-check-label pl-0" for="flexCheckDefault">
                                                    Comme neuf (cocher la case)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 d-none mb-2" id="divPrixNeuf">
                                            <label for="prixCommeNeuf">Prix comme neuf (€ TTC):</label>
                                            <input class="text-center col-3" type="text" name="prixCommeNeuf" pattern="([0-9]{1,2}).([0-9]{2})" placeholder="10.00">
                                        </div>  
                                        <div class="col-12 d-flex p-0" id="selects">
                                            <div class="form-group col-4">
                                                <label for="etatBoite">État de la boite:</label>
                                                <select name="etatBoite" id="selectEtatBoite" required>
                                                    <option value="">...</option>
                                                    <option value="COMME NEUF">COMME NEUF</option>
                                                    <option value="BON ÉTAT">BON ÉTAT</option>
                                                    <option value="ÉTAT MOYEN">ÉTAT MOYEN</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-4">
                                                <label for="etatMateriel">État du matériel:</label>
                                                <select name="etatMateriel" id="selectEtatMateriel" required>
                                                    <option value="">...</option>
                                                    <option value="COMME NEUF">COMME NEUF</option>
                                                    <option value="BON ÉTAT">BON ÉTAT</option>
                                                    <option value="ÉTAT MOYEN">ÉTAT MOYEN</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-4">
                                                <label for="regleJeu">Règle du jeu:</label>
                                                <select name="regleJeu" id="selectEtatRegle" required>
                                                    <option value="">...</option>
                                                    <option value="ORIGINALE">ORIGINALE</option>
                                                    <option value="IMPRIMÉE">IMPRIMÉE</option>
                                                    <option value="SANS">SANS</option>
                                                    <option value="SUR LA BOITE">SUR LA BOITE</option>
                                                </select>
                                            </div>
                                        </div> 
                                    
                                                                            
                                        <div class="form-group col-12">
                                            <label for="description">Informations sur ces/cette boite/s complète/s:</label>
                                            <textarea name="description" rows="4" cols="30"></textarea>
                                        </div> 
                                        <input type="hidden" name="prixHtReference" value="'.$donnees['prix_HT'].'">
                                        <input type="hidden" name="idJeu" value="'.$donnees['idCatalogue'].'">
                                        <div class="col-12 text-center">
                                            <button type="submit" class="btn btn-success">Créer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-11 mx-auto p-0 border border-dark mb-2">
                                <table class="table table-sm align-middle text-center">
                                    <thead>
                                        <th>Référence</th>
                                        <th>Quantité en vente</th>
                                        <th>Prix de vente TTC</th>
                                        <th>Changer prix TTC</th>
                                        <th>Ancien prix TTC</th>
                                        <th>Actions</th>
                                    </thead>
                                    <tbody>';
                                    $allJeux = $bdd->prepare("SELECT * FROM jeux_complets WHERE idCatalogue = ?");
                                    $allJeux->execute(array($donnees['idCatalogue']));
                                    $donneesAllJeux = $allJeux->fetchAll();

                                    if(count($donneesAllJeux) > 0){
                                        $nbrModal = 1;
                                        foreach($donneesAllJeux as $jeuC){
                                            
                                            $sqlIsVendu = $bdd->prepare("SELECT * FROM documents_lignes_achats WHERE idJeuComplet = ?");
                                            $sqlIsVendu->execute(array($jeuC['idJeuxComplet']));
                                            $nbrIsVendu = $sqlIsVendu->rowCount();

                                            if($nbrIsVendu > 0 || $jeuC['vente'] != null ){
                                                $activeSuppression = 'disabled';
                                            }else{
                                                $activeSuppression = '';
                                            }

                                            if($jeuC['stock'] > 0){
                                                $buttonOnline_offline = "";
                                            }else{
                                                $buttonOnline_offline = "disabled";
                                            }

                                            if($jeuC['actif'] == 1){
                                                $buttonOnline =  '<a href="/administration/jeu/ctrl/ctrl-complet-online_offline.php?idComplet='.$jeuC['idJeuxComplet'].'&newValue=0&reference='.$jeuC['reference'].'" class="btn btn-success '.$buttonOnline_offline.'"><i class="fas fa-globe-europe"></i></a>';
                                            }else{
                                                $buttonOnline =  '<a href="/administration/jeu/ctrl/ctrl-complet-online_offline.php?idComplet='.$jeuC['idJeuxComplet'].'&newValue=1&reference='.$jeuC['reference'].'" class="btn btn-danger '.$buttonOnline_offline.'"><i class="fas fa-globe-europe"></i></a>';
                                            }

                                            if($jeuC['don'] == 1){
                                                $buttonDon = '<a href="/administration/jeu/ctrl/ctrl-complet-don.php?idComplet='.$jeuC['idJeuxComplet'].'&newValue=0&reference='.$jeuC['reference'].'" class="btn btn-success"><i class="fas fa-hand-holding-heart"></i></a>';
                                            }else{
                                                $buttonDon = '<a href="/administration/jeu/ctrl/ctrl-complet-don.php?idComplet='.$jeuC['idJeuxComplet'].'&newValue=1&reference='.$jeuC['reference'].'" class="btn btn-danger"><i class="fas fa-hand-holding-heart"></i></a>';
                                            }

                                          

                                            if($jeuC['vente'] != null){
                                                $ventes = explode("|",$jeuC['vente']);
                                                $vente = 'Vendu '.$ventes[0].' en '.$ventes[1].' le '.date('d-m-Y',$jeuC['timeVente']);
                                            }else{
                                                $vente = '
                                                <!-- Button trigger modal -->
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal'.$donnees['idCatalogue'].'-'.$nbrModal.'">
                                                Gestion du jeu
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="exampleModal'.$donnees['idCatalogue'].'-'.$nbrModal.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">'.$donnees['nom'].': '.$jeuC['reference'].'</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body d-flex flex-wrap align-items-center">
                                                        <form class="col-6" action="/administration/jeu/ctrl/ctrl-complet-vente.php" method="get">
                                                            <div class="form-group">
                                                            <label for="exampleInputEmail1">Prix de la vente:</label>
                                                            <input type="text" name="prix" class="form-control col-6 mx-auto text-center mb-3" id="inputText" placeholder="10.00">
                                                            <select name="moyenPaiement" required>
                                                                <option value="ESPÈCES">ESPÈCES</option>
                                                                <option value="CHÈQUE">CHÈQUE</option>
                                                            </select>
                                                            </div>
                                                            <input type="hidden" name="reference" value="'.$jeuC['reference'].'" >
                                                            <button class="btn btn-success mt-3" type="submit">Enregistrer</button>
                                                        </form>
                                                        <div class="col-6">
                                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                                <a href="/administration/jeu/ctrl/ctrl-complet-delete.php?idComplet='.$jeuC['idJeuxComplet'].'" class="btn btn-danger '.$activeSuppression.'"><i class="fas fa-trash-alt"></i></a>'.$buttonOnline.$buttonDon.'
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                                    </div>
                                                    </div>
                                                </div>
                                                </div>';
                                            }

                                            if($jeuC['stock'] > 0){
                                                $online = '<i class="fas fa-circle text-success"></i>';
                                            }else{
                                                $online = '<i class="fas fa-circle text-danger"></i>';
                                            }

                                  

                                            if($jeuC['ancienPrixHT'] != null){
                                                $ancienPrix = number_format(($jeuC['ancienPrixHT'] * $tva)/100 ,2);
                                            }else{
                                                $ancienPrix = "";
                                            }

                                            echo '<tr>
                                                    <td id="'.$jeuC['reference'].'" class="align-middle">'.$jeuC['reference']; if($jeuC['isNeuf'] == 1){echo '<br/><span class="small bg-info text-white p-1">COMME NEUF</span>';} echo '</td>
                                                    <td class="align-middle">'.$jeuC['stock'].' '.$online.'</td>
                                                    <td class="align-middle">'.number_format(($jeuC['prixHT'] * $tva)/100 ,2).'</td>
                                                    <td class="align-middle"><form action="/administration/jeu/ctrl/ctrl-complet-newPrice.php" method="get"><input type="text" class="col-3 text-center" name="nvPrixTTC" pattern="([0-9]{1,2}).([0-9]{2})" placeholder="10.00"><input type="hidden" name="idComplet" value="'.$jeuC['idJeuxComplet'].'"><button type="submit" class="btn"><i class="fas fa-save"></i></button></form></td>
                                                    <td class="align-middle">'.$ancienPrix.'</td>
                                                    <td class="align-middle">'.$vente.'</td>
                                                </tr>';
                                        $nbrModal ++;
                                        }
                                    }else{
                                        echo '<tr><td colspan="8" class="text-center align-middle">Aucun jeu pour le moment</td></tr>';
                                    }
                                    echo '</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        ?>
</div>
                    
<?php include_once("../../commun/bas_de_page-admin.php");?>

<script>
    let checks = document.querySelectorAll('#checkNeuf');
    let divPrixNeufs = document.querySelectorAll('#divPrixNeuf');

    let divPrixNeuf = '<div class="form-group col-12 d-none" id="divPrixNeuf"><label for="prixCommeNeuf">Prix comme neuf (€ TTC):</label><input class="text-center col-3" type="text" name="prixCommeNeuf" pattern="([0-9]{1,2}).([0-9]{2})" placeholder="10.00"></div> ';

    checks.forEach(check => {
        check.addEventListener('change', (e) => {
            let divNewPrice;
            divNewPrice = e.target.parentElement.parentElement.nextElementSibling;
            inputElement = divNewPrice.lastElementChild;
            divSelects = divNewPrice.nextElementSibling;

            if(check.checked){
                divNewPrice.classList.remove('d-none');
                inputElement.required = true;
                divSelects.children[0].firstElementChild.nextElementSibling.selectedIndex = 1;
                divSelects.children[1].firstElementChild.nextElementSibling.selectedIndex = 1;
                divSelects.children[2].firstElementChild.nextElementSibling.selectedIndex = 1;
            }else{
                divNewPrice.classList.add('d-none');
                inputElement.required = false;
                divSelects.children[0].firstElementChild.nextElementSibling.selectedIndex = 0;
                divSelects.children[1].firstElementChild.nextElementSibling.selectedIndex = 0;
                divSelects.children[2].firstElementChild.nextElementSibling.selectedIndex = 0;
            }
        })
    })
</script>