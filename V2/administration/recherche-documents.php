<?php
@session_start ();
require("../controles/fonctions/adminOnline.php");
include('../config.php');
include('../bdd/connexion-bdd.php');
include('../bdd/table_config.php');
$tva = $donneesConfig[6]['valeur'];
$titreDeLaPage = "[ADMIN] - Recherche de documents";
$descriptionPage = "";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
require("../controles/fonctions/calculePrix.php");
?>
<div class="container-fluid">
    <div class="row mt-4">
        <div class="card col-xl-9 mx-auto p-0">
            <div class="card-header bg-secondary text-white">Recherche de documents</div>
            <div class="card-body">
                <form method="get" action ="" class="d-flex">
                    <div class="form-group text-center mx-3">Type de document:
                        <select name="type" class="form-control" required >
                            <option value="">...</option>
                            <option value="numero_devis">DEVIS</option>
                            <option value="numero_facture">FACTURE</option>
                        </select>
                    </div>
                    <div class="form-group text-center mx-3">Numéro du document:
                        <input type="text" class="form-control" name="numero" placeholder="SANS LES LETTRES..." pattern="[0-9]{2,8}"/>
                    </div>
                    <div class="form-group text-center">Nom du client:
                        <input type="text" class="form-control" name="nom" maxlength="20" placeholder="MAX 20 caractères..."/>
                    </div>
                    <div class="col text-center mt-1 mb-2">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-info border border-secondary">Chercher</button>
                            <a href="/administration/recherche-documents.php" class="btn btn-warning border border-secondary">Reset</a>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">Résultat de la recherche:</div>
                <div class="card-body p-0">
                    <?php
                    if(!isset($_GET['type'])){
                        echo '<div class="card-text text-center h4 p-2"> AUCUNE RECHERCHE...</div>';
                    }else{
                        $moyens = $GLOBALS['moyenPaiementManuel'];
                        require("../controles/fonctions/validation_donnees.php");
                        $type = valid_donnees($_GET['type']);
                        
                        if($_GET['type'] == "numero_devis"){
                            $textEtat = 'etat = 1';
                        }else{
                            $textEtat = 'etat > 1';
                        }


                        if($_GET['nom'] != ""){
                            $nom = valid_donnees($_GET['nom']);
                            $textNom = "AND adresse_facturation LIKE '%$nom%'";
                        }else{
                            $nom = "";
                            $textNom = '';
                        }
                        if($_GET['numero'] != ""){
                            $numero = valid_donnees($_GET['numero']);
                            $textNumero = "AND $type LIKE '%$numero'";
                        }else{
                            $numero = "";
                            $textNumero = "AND ".$type." != '' ";
                        }

                        $parametresUrl = "&type=".$_GET['type']."&numero=".$numero."&nom=".$nom;
                        //ON RECHERCHE LES DOCUMENTS
                        $query = "SELECT * FROM documents WHERE $textEtat $textNumero $textNom";
                        $sqlRecherche = $bdd->query($query);
                        $nb = $sqlRecherche->rowCount();
                            if($nb < 1){
                                echo '<div class="card-text text-center h4">AUCUN RESULTAT...</div>';
                            }else{
                                require("./pagination-admin.php");
                                echo '<div class="col-12 h5 text-center p-2">'.$nb.' résultat(s)</div>
                                <table class="table table-sm mt-2 text-center">
                                    <thead class="thead-dark text-center">
                                        <tr>
                                            <th scope="col">Client</th>
                                            <th scope="col">Facture</th>
                                            <th scope="col">Devis</th>
                                            <th scope="col">Total TTC</th>
                                            <th scope="col">Cde expédiée</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                while($donneesRecherche){
                                    $sqlClientFacturation = $bdd->prepare("SELECT * FROM clients WHERE idClient = ?");
                                    $sqlClientFacturation->execute(array($donneesRecherche['idUser']));
                                    $donneesClient = $sqlClientFacturation->fetch();
                                    echo '<tr>
                                            <td class="align-middle">'.$donneesClient['nomFacturation'].' '.$donneesClient['prenomFacturation'].'<br/>'.$donneesClient['adresseFacturation'].'<br />'.$donneesClient['cpFacturation'].' '.$donneesClient['villeFacturation'].'</td>
                                            <td class="align-middle">';
                                            if($donneesRecherche['numero_facture'] != ""){
                                                echo $donneesRecherche['numero_facture'].'<br/> le '.date('d.m.Y',$donneesRecherche['time_transaction'])." par ".$donneesRecherche['moyen_paiement'].'<br/>Num. transaction:<br/>'.$donneesRecherche['num_transaction'];
                                            }else{
                                                echo 'PAS ENCORE FACTURER';
                                            }
                                            echo '</td>
                                            <td class="align-middle">'.$donneesRecherche['numero_devis'].'<br/> Créé le '.date('d.m.Y',$donneesRecherche['time']).'<br/>';
                                                if($donneesRecherche['time_mail_devis'] != ""){
                                                    echo '<i class="fas fa-paper-plane text-success"> '.date('d.m.Y',$donneesRecherche['time_mail_devis']).'</i>';
                                                }else{
                                                    echo '<i class="fas fa-paper-plane text-danger"> Devis non envoyé</i>';
                                                }
                                            echo '</td>
                                            <td class="align-middle">'.htEnttc($donneesRecherche['totalTTC'],$tva).'</td>
                                            <td class="align-middle">';
                                            if($donneesRecherche['envoyer'] != 0){
                                                $envoyer = explode("|",$donneesRecherche['envoyer']);
                                                if($envoyer[1] != "SANS"){
                                                    $numSuivi = "<br/>N° Colissimo: ".$envoyer[1];
                                                }else{
                                                    $numSuivi = "<br/>par ".$donneesRecherche['expedition']."<br/> et sans suivi.";
                                                }
                                                echo 'LE '.date('d.m.Y',$envoyer[0]).' '.$numSuivi; 
                                            }else{
                                            echo "PAS ENVOYER !";
                                            }
                                            echo '</td>
                                            <td class="align-middle">';
                                                if($donneesRecherche['numero_facture'] != ""){
                                                echo '<a href="/administration/facture/generation-pdf.php?document='.$donneesRecherche['idDocument'].'" target="_blank" class="btn btn-warning border-primary mt-1">Télécharger la facture</a>';
                                                }else{
                                                    echo '
                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                        <a href="/admin/devis/edition/'.$donneesRecherche['numero_devis'].'" class="btn btn-warning mt-2">Voir et modifier</a>
                                                        <a href="/admin/devis/delete/'.$donneesRecherche['numero_devis'].'" class="btn btn-danger mt-2">Voir et supprimer</a>
                                                    </div>
                                                    <form method="POST" action="/administration/comptabilite/saisie-paiement-manuelle.php" class="mt-3">
                                                        <div class="form-group">
                                                            <select name="methode" class="form-control col-6 mx-auto" required>
                                                                <option value="">...</option>';
                                                                foreach($moyens as $moyen){
                                                                    echo '<option value="'.$moyen[0].'">'.$moyen['1'].'</option>';
                                                                }
                                                            echo '</select>
                                                            <input type="hidden" name="doc" value="'.$donneesRecherche['idDocument'].'" />
                                                            <button type="submit" class="btn btn-info mt-2">Saisir un paiement manuel</button>
                                                            <div class="small text-danger">Attention c\'est irréversible !</div>
                                                        </div>
                                                    </form>';
                                                }
                                            echo '</td>
                                          </tr>';
                                        $donneesRecherche = $sqlRecherchePagination-> fetch();
                                }
                                echo '</table>';  
                            }
                    }
                    ?>


                <?php if(isset($nombreDePages) && $nombreDePages > 1){?>
                    <div class="row">
                        <div class="col-12 mt-4">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-center">
                                        <?php 
                                        $variation = 1;
                                        $milieu = ceil($nombreDePages/2);
                                        if($variation < $milieu){
                                            $variation = $variation;
                                        }else{
                                            $variation = $milieu;
                                        }

                                        if($pageActuelle == 1){
                                        echo '<li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-fast-backward"></i></a></li>';
                                        echo '<li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-step-backward"></i></a></li>';
                                            for($i=1;$i<=$pageActuelle+$variation;$i++){
                                                if($pageActuelle == $i){
                                                    $active = " active";
                                                }else{
                                                    $active = "";
                                                }
                                                echo '<li class="page-item'.$active.'"><a class="page-link" href="/admin/recherche-document/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                            }
                                            $pageSuivante = $pageActuelle+1;
                                            echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/recherche-document/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                            echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/recherche-document/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
                                        }
                                        
                                        if($pageActuelle > 1 && $pageActuelle < $nombreDePages - $variation){
                                            $pageAvant = $pageActuelle-1;
                                            echo '<li rel="prev" class="page-item"><a class="page-link" href="/admin/recherche-document/?page=1'.$parametresUrl.'"><i class="fas fa-fast-backward"></i></a></li>';
                                            echo '<li rel="prev" class="page-item"><a class="page-link" href="/admin/recherche-document/?page='.$pageAvant.$parametresUrl.'"><i class="fas fa-step-backward"></i></a></li>';
                                                for($i=$pageActuelle-1;$i<=$pageActuelle+$variation;$i++){
                                                    if($pageActuelle == $i){
                                                        $active = " active";
                                                    }else{
                                                        $active = "";
                                                    }
                                                    echo '<li class="page-item'.$active.'"><a class="page-link" href="/admin/recherche-document/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                                }
                                            $pageSuivante = $pageActuelle+1;
                                            echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/recherche-document/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                            echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/recherche-document/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
                                        }

                                        if($pageActuelle >= $nombreDePages - $variation && $pageActuelle < $nombreDePages+1){
                                            if($pageActuelle < $nombreDePages - $variation+1){
                                                $back = 1;
                                            }elseif($pageActuelle < $nombreDePages - $variation+2){
                                                $back = 2;
                                            }elseif($pageActuelle < $nombreDePages - $variation+3){
                                                $back = 3;
                                            }elseif($pageActuelle < $nombreDePages - $variation+4){
                                                $back = 4;
                                            }
                                            $pageAvant = $pageActuelle-1;
                                            echo '<li rel="prev" class="page-item"><a class="page-link" href="/admin/recherche-document/?page=1'.$parametresUrl.'"><i class="fas fa-fast-backward"></i></a></li>';
                                            echo '<li rel="prev" class="page-item"><a class="page-link" href="/admin/recherche-document/?page='.$pageAvant.$parametresUrl.'"><i class="fas fa-step-backward"></i></a></li>';
                                                for($i=$pageActuelle-$back;$i<=$nombreDePages;$i++){
                                                    if($pageActuelle == $i){
                                                        $active = " active";
                                                    }else{
                                                        $active = "";
                                                    }
                                                    echo '<li class="page-item'.$active.'"><a class="page-link" href="/admin/recherche-document/?page='.$i.$parametresUrl.'">'.$i.'</a></li>';
                                                }
                                            if($pageActuelle < $nombreDePages){
                                                $pageSuivante = $pageActuelle+1;
                                                echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/recherche-document/?page='.$pageSuivante.$parametresUrl.'"><i class="fas fa-step-forward"></i></a></li>';
                                                echo '<li rel="next" class="page-item"><a class="page-link" href="/admin/recherche-document/?page='.$nombreDePages.$parametresUrl.'"><i class="fas fa-fast-forward"></i></a></li>';
                                            }else{
                                                echo '<li rel="next" class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-step-forward"></i></a></li>';
                                                echo '<li rel="next" class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-fast-forward"></i></a></li>';
                                            }
                                        }             
                                        ?>
                                </ul>
                            </nav>
                            <div class="col-12 text-center">Total des pages: <?php echo $nombreDePages; ?></div>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("../commun/bas_de_page-admin.php");?>