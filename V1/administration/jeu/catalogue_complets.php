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

<div class="container-fluid mt-4">
    <div class="col-12 h2 text-center"> LES JEUX COMPLETS </div>
        <div class="row mt-4">
            <div class="col-12 p-0">
                <nav aria-label="Pagination menu alphabet">
                    <ul class="pagination d-flex flex-wrap justify-content-around">
                    <?php
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
            <div class="col-11 mx-auto text-start bg-dark card">
                <?php
                    if(isset($_GET['lettre']) && ctype_alpha($_GET['lettre']) && strlen($_GET['lettre']) < 2){
                        $lettre = $_GET['lettre'];
                    }else{
                        $lettre = "A";
                    }
                    echo strtoupper($lettre);
                ?>
            </div>
            
            <?php
                $searchAlphabet = $bdd-> prepare("SELECT * FROM catalogue WHERE nom LIKE ? AND isComplet = 1 ORDER BY nom");
                $searchAlphabet-> execute(array($lettre.'%'));
                $donneesAlphabet = $searchAlphabet-> fetchAll();
                $countAlphabet = $searchAlphabet-> rowCount();

                foreach($donneesAlphabet as $donnees){
                    echo '<div class="card col-11 mx-auto mt-3 p-0">
                            <div class="card-header d-flex justify-content-around align-items-center">
                                <div class="col" id="'.$donnees['idCatalogue'].'">'.$donnees['nom'].'</div>
                                <div class="col">'.$donnees['editeur'].'</div>
                                <div class="col">'.$donnees['annee'].'</div>
                                <div class="col"><a href="/administration/jeu/edition.php?etat=offline&tri=&jeu='.$donnees['idCatalogue'].'" class="btn btn-info">Retour "coquille"</a></div>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm col-12 align-middle text-center">
                                    <thead>
                                        <th>Référence</th>
                                        <th>Quantité en vente</th>
                                        <th>Prix de vente TTC</th>
                                        <th>Information / description</th>
                                        <th>Actions</th>
                                    </thead>
                                    <tbody>';
                                    $allJeux = $bdd->prepare("SELECT * FROM jeux_complets WHERE idCatalogue = ?");
                                    $allJeux->execute(array($donnees['idCatalogue']));
                                    $donneesAllJeux = $allJeux->fetchAll();

                                    foreach($donneesAllJeux as $jeuC){

                                        //affichage complet d'une référence
                                        if($jeuC['idJeuxComplet'] < 10){
                                            $reference = "0000";
                                        }elseif($jeuC['idJeuxComplet'] > 9 && $jeuC['idJeuxComplet'] < 100){
                                            $reference = "000";
                                        }elseif($jeuC['idJeuxComplet'] > 99 && $jeuC['idJeuxComplet'] < 1000){
                                            $reference = "00";
                                        }elseif($jeuC['idJeuxComplet'] > 999 && $jeuC['idJeuxComplet'] < 10000){
                                            $reference = "0";
                                        }
             
                                        if($jeuC['stock'] > 0){
                                            $online = '<i class="fas fa-circle text-success"></i>';
                                        }else{
                                            $online = '<i class="fas fa-circle text-danger"></i>';
                                        }

                                        if($jeuC['stock'] > 0){
                                            $buttonOnline_offline = "";
                                        }else{
                                            $buttonOnline_offline = "disabled";
                                        }

                                        echo '<tr>
                                                <td>'.$jeuC['reference']; if($jeuC['isNeuf'] == 1){echo ' <span class="small bg-info text-white p-1">COMME NEUF</span>';} echo '</td>
                                                <td>'.$jeuC['stock'].' '.$online.'</td>
                                                <td>'.number_format(($jeuC['prixHT'] * $tva)/100 ,2).'</td>
                                                <td>'.$jeuC['information'].'</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                        <a href="/administration/jeu/ctrl/ctrl-complet-delete.php?idComplet='.$jeuC['idJeuxComplet'].'" class="btn btn-danger"><i class="fas fa-trash-alt"></i></a>';
                                                        if($jeuC['actif'] == 1){
                                                            echo '<a href="/administration/jeu/ctrl/ctrl-complet-online_offline.php?idComplet='.$jeuC['idJeuxComplet'].'&newValue=0" class="btn btn-success '.$buttonOnline_offline.'"><i class="fas fa-globe-europe"></i></a>';
                                                        }else{
                                                            echo '<a href="/administration/jeu/ctrl/ctrl-complet-online_offline.php?idComplet='.$jeuC['idJeuxComplet'].'&newValue=1" class="btn btn-danger '.$buttonOnline_offline.'"><i class="fas fa-globe-europe"></i></a>';
                                                        }
                                                    echo '</div>
                                                </td>
                                            </tr>';
                                    }
                                    
                                    echo '</tbody>
                                </table>

                                <div class="col-12 mt-5 d-flex border-top border-1 border-dark pt-4">
                                    <div class="col-6  align-self-center text-center">
                                        <div class="divImgCatalogueAdmin">';
                                            //on cherche l'image du jeu
                                            $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$donnees['idCatalogue']);
                                            $donneesImage = $sqlImage->fetch();

                                            if($donneesImage['image'] != ""){
                                                echo '<img src="data:image/jpeg;base64,'.$donneesImage['image'].'"/>';
                                            }else{
                                                echo '<img src="/images/design/default.png" />';
                                            }
                                            echo '
                                        </div>
                                    </div>
                                    <div class="col-6 p-0">
                                        <form method="post" action="/administration/jeu/ctrl/ctrl-new-complet.php" class="d-flex flex-wrap">
                                            <div class="form-group col-12">
                                                Prix HT de référence: <span class="prixHtReference">'.number_format($donnees['prix_HT']/100,2,",","").'</span>
                                            </div> 
                                            <div class="form-group col-12">
                                                <label for="">Entrée en stock:</label>
                                                <select name="qte">';
                                                    for($o=1;$o<11;$o++){
                                                        echo '<option value="'.$o.'">'.$o.'</option>';
                                                    }
                                                echo '</select>
                                            </div> 
                                            <div class="form-group col-12">
                                                <label for="etatBoite">État de la boite:</label>
                                                <select name="etatBoite" id="selectEtatBoite" required>
                                                    <option value="">...</option>
                                                    <option value="COMME NEUF">COMME NEUF</option>
                                                    <option value="BON ÉTAT">BON ÉTAT</option>
                                                    <option value="ÉTAT MOYEN">ÉTAT MOYEN</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-12">
                                                <label for="etatMateriel">État du matériel:</label>
                                                <select name="etatMateriel" id="selectEtatMateriel" required>
                                                    <option value="">...</option>
                                                    <option value="COMME NEUF">COMME NEUF</option>
                                                    <option value="BON ÉTAT">BON ÉTAT</option>
                                                    <option value="ÉTAT MOYEN">ÉTAT MOYEN</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-12">
                                                <label for="regleJeu">Règle du jeu:</label>
                                                <select name="regleJeu" id="selectEtatRegle" required>
                                                    <option value="">...</option>
                                                    <option value="ORIGINALE">ORIGINALE</option>
                                                    <option value="IMPRIMÉE">IMPRIMÉE</option>
                                                    <option value="SANS">SANS</option>
                                                    <option value="SUR LA BOITE">SUR LA BOITE</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-12">
                                                <label for="prixCommeNeuf">Prix si COMME NEUF/COMME NEUF/ORIGINALE (€ TTC):</label>
                                                <input class="text-center" type="text" name="prixCommeNeuf" pattern="([0-9]{1,2}).([0-9]{2})" placeholder="10.00">
                                            </div>                                          
                                            <div class="form-group col-12">
                                                <label for="description">Informations sur ces/cette boite/s complète/s:</label>
                                                <textarea name="description"></textarea>
                                            </div> 
                                            <input type="hidden" name="prixHtReference" value="'.$donnees['prix_HT'].'">
                                            <input type="hidden" name="idJeu" value="'.$donnees['idCatalogue'].'">
                                            <div class="col-12 text-center">
                                                <button type="submit" class="btn btn-success">Créer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>';
      
                }
                    ?>
        </div>
</div>
                    
<?php include_once("../../commun/bas_de_page-admin.php");?>