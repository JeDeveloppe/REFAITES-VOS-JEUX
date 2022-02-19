<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");

include('../../config.php');

include('../../bdd/connexion-bdd.php');

$titreDeLaPage = "[ADMIN] - Bouteilles à la mer";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");

include_once("../../commun/alertMessage.php");

//ON COMTPE TOUTES LES DEMANDES
$sqlSatisfactionTotal = $bdd-> query("SELECT * FROM bouteille_mer");
$countSatisfactionTotal = $sqlSatisfactionTotal-> rowCount();

//ON COMTPE TOUTES LES DEMANDES TRAITEES
$sqlSatisfaction = $bdd-> query("SELECT * FROM bouteille_mer WHERE actif != 0");
$countSatisfaction = $sqlSatisfaction-> rowCount();

//ON COMPTE LES CONTANTS
$sqlSatisfactionOk = $bdd-> query("SELECT * FROM bouteille_mer WHERE actif = 3");
$countSatisfactionOk = $sqlSatisfactionOk-> rowCount();

//ON COMPTE LES NEUTRES
$sqlSatisfactionNeutre = $bdd-> query("SELECT * FROM bouteille_mer WHERE actif = 1");
$countSatisfactionNeutre = $sqlSatisfactionNeutre-> rowCount();

//ON COMTE LES PAS CONTANT
$sqlSatisfactionKo = $bdd-> query("SELECT * FROM bouteille_mer WHERE actif = 4");
$countSatisfactionKo = $sqlSatisfactionKo-> rowCount();

//menu alphabet
$menuAlphabet = "A";
?>

<div class="container-fluid mt-4">
    <div class="col-12 h2 text-center"><i class="fas fa-wine-bottle text-success"></i> LES BOUTEILLES A LA MER </div>
        <nav class="nav mt-5 border-bottom border-2 border-primary">
            <a class="nav-link active" href="/admin/bouteille-a-la-mer/">Liste des bouteilles</a>
            <a class="nav-link" href="/admin/bouteille-a-la-mer/les-20-dernieres/">Les 20 dernières reçues</a>
        </nav>
        <div class="row mt-4 d-flex justify-content-around">
            <div class="col-2">Nombre de bouteilles : <?php echo $countSatisfactionTotal;?></div>
            <div class="col-2">Nombre traité : <?php echo $countSatisfaction;?> ( <?php echo number_format(($countSatisfaction/$countSatisfactionTotal)*100);?> %)</div>
            <div class="col-2"><i class="fas fa-smile-beam text-success"> : <?php echo $countSatisfactionOk;?></i> ( <?php echo number_format(($countSatisfactionOk/$countSatisfaction)*100);?> %)</div>
            <div class="col-2"><i class="fas fa-not-equal"> : <?php echo $countSatisfactionNeutre;?></i> ( <?php echo number_format(($countSatisfactionNeutre/$countSatisfaction)*100);?> %)</div>
            <div class="col-2"><i class="fas fa-angry text-danger"> : <?php echo $countSatisfactionKo;?></i> ( <?php echo number_format(($countSatisfactionKo/$countSatisfaction)*100);?> %)</div>
        </div>
        <div class="row mt-4">
            <div class="col-12 p-0">
                <nav aria-label="Pagination menu alphabet">
                    <ul class="pagination d-flex flex-wrap justify-content-around">
                    <?php
                        for($x=1;$x<27;$x++)
                        {
                        $searchAlphabetLettre = $bdd-> prepare("SELECT * FROM bouteille_mer WHERE nom LIKE ? AND actif = 0 ORDER BY nom");
                        $searchAlphabetLettre-> execute(array($menuAlphabet.'%'));
                        $countAlphabetLettre = $searchAlphabetLettre-> rowCount();
      
                        echo '<li class="page-item"><a class="page-link" href="/admin/bouteille-a-la-mer/'.$menuAlphabet.'/">'.$menuAlphabet++.'<br/><span class="small">('.$countAlphabetLettre.')</span></a></li>';
                        }
                    ?>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12 table-responsive">
                <table class="table table-striped table-sm mx-auto text-center">
                    <thead>
                        <th>#</th>
                        <th>Nom du jeu</th>
                        <th>Éditeur</th>
                        <th>Année</th>
                        <th>Date de la bouteille</th>
                        <th><i class="fas fa-clock" data-html="true" data-toggle="tooltip" data-placement="top" title="Date retournée"></i></th>
                        <th>Etat</th>
                        <th colspan="2">Action</th>
                    </thead>
                    <tbody>                    
                    <?php
                        if(isset($_GET['lettre']) && ctype_alpha($_GET['lettre']) && strlen($_GET['lettre']) < 2){
                            $lettre = $_GET['lettre'];
                        }else{
                            $lettre = "A";
                        }

                            echo '<tr>
                                    <td class="bg-dark pl-2 text-left" colspan="8">'.strtoupper($lettre).'</td>
                                </tr>';
                            $searchAlphabet = $bdd-> prepare("SELECT * FROM bouteille_mer WHERE nom LIKE ? ORDER BY nom");
                            $searchAlphabet-> execute(array($lettre.'%'));
                            $donneesAlphabet = $searchAlphabet-> fetchAll();
                            $countAlphabet = $searchAlphabet-> rowCount();

                            foreach($donneesAlphabet as $donnees){
                                //si y a plus d'un an c'est que c'est une relance
                                if($donnees['actif'] > 0){
                                    $relanceBouteille = '<i class="fas fa-clock text-success" data-html="true" data-toggle="tooltip" data-placement="top" title="'.date("d.m.Y",$donnees['end_time']).'"></i>';
                                }else{
                                    $relanceBouteille = '<i class="fas fa-clock text-danger" data-html="true" data-toggle="tooltip" data-placement="top" title="(vide)"></i>';
                                }
                                echo '<tr>
                                        <td></td>
                                        <td class="text-left align-middle">'.$donnees['nom'].'</td>
                                        <td class="text-center align-middle">'.$donnees['editeur'].'</td>
                                        <td class="text-center align-middle">'.$donnees['annee'].'</td>
                                        <td class="text-center align-middle">Lancée le '.date("d.m.Y",$donnees['time']).' par <br/>'.$donnees['email'].'</td>
                                        <td class="text-center align-middle">'.$relanceBouteille.'</td>
                                        <td class="text-center align-middle">';
                                            if($donnees['actif'] == 3){ //bouteille envoyée et réponse oui
                                                echo '<i class="fas fa-smile-beam text-success" data-html="true" data-toggle="tooltip" data-placement="top" title="Relance avec satisfaction"></i>';
                                            }else if($donnees['actif'] == 1){ //bouteille envoyée et en attente de réponse
                                                echo '<i class="fas fa-not-equal" data-html="true" data-toggle="tooltip" data-placement="top" title="Relance sans réponse..."></i>';
                                            }else if($donnees['actif'] == 4){ //bouteille envoyée et réponse non
                                                echo '<i class="fas fa-angry text-danger" data-html="true" data-toggle="tooltip" data-placement="top" title="Relance sans satisfaction"></i>';
                                            }else{
                                                echo '<i class="fas fa-wine-bottle" data-html="true" data-toggle="tooltip" data-placement="top" title="Bouteille en attente de relance..."></i>';
                                            }
                                        echo '</td>
                                        <td class="text-center align-middle">';
                                        if($donnees['actif'] != 3){ //si relance n'est pas satisfaisance on peut relancer un mail
                                            echo '<form methode="get" action="/administration/bouteilleMer/ctrl/ctrl-envoi-mail-bouteille.php"><input type="hidden" name="bouteille" value="'.$donnees['idBouteille'].'"><input type="text" name="url" placeholder="Url du jeu sur le site" required><button class="btn btn-warning ml-1"><i class="fas fa-paper-plane"></i></button></form></td>';
                                        }else{
                                            echo '<i class="fas fa-tasks text-success"> Mission accomplie !</i></td>';
                                        }
                                echo '</tr>';
                            }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>

<?php include_once("../../commun/bas_de_page-admin.php");?>