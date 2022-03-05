<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");

include('../../config.php');

include('../../bdd/connexion-bdd.php');

$titreDeLaPage = "[ADMIN] - Fichier client";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");

include_once("../../commun/alertMessage.php");

//menu alphabet
$menuAlphabet = "A";
?>

<div class="container-fluid mt-4">
        <div class="row mt-4">
            <div class="col-12 h2 text-center">FICHIER CLIENT </div>
            <div class="col-12 p-0 mt-4">
                <nav aria-label="Pagination menu alphabet">
                    <ul class="pagination d-flex flex-wrap justify-content-around">
                    <?php
                                    
                        for($x=1;$x<27;$x++)
                        {
                        $searchAlphabetLettre = $bdd-> prepare("SELECT * FROM clients WHERE nomFacturation LIKE ? ");
                        $searchAlphabetLettre-> execute(array($menuAlphabet.'%'));
                        $countAlphabetLettre = $searchAlphabetLettre-> rowCount();
                        echo '<li class="page-item"><a class="page-link" href="/admin/client/liste/'.$menuAlphabet.'/">'.$menuAlphabet++.'<br/><span class="small">('.$countAlphabetLettre.')</span></a></li>';
                        }
                      
                    ?>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12 table-responsive">
                <table class="table table-striped table-sm mx-auto text-center">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nom</th>
                            <th scope="col">Prénom</th>
                            <th scope="col">Adresse</th>
                            <th scope="col">Code postal</th>
                            <th scope="col">Ville</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>                    
                    <?php
                        if(isset($_GET['lettre']) && ctype_alpha($_GET['lettre']) && strlen($_GET['lettre']) < 2){
                            $lettre = $_GET['lettre'];
                        }else{
                            $lettre = "A";
                        }
                            echo '<tr>
                                    <td class="bg-dark pl-2 text-left" colspan="7">'.strtoupper($lettre).'</td>
                                </tr>';
                            $searchAlphabet = $bdd-> prepare("SELECT * FROM clients WHERE nomFacturation LIKE ? ORDER BY nomFacturation ASC");
                            $searchAlphabet-> execute(array($lettre.'%'));
                            $donneesAlphabet = $searchAlphabet-> fetchAll();
                            $countAlphabet = $searchAlphabet-> rowCount();

                            foreach($donneesAlphabet as $donnees){
                                $searchDocument = $bdd-> prepare("SELECT * FROM documents WHERE idUser = (SELECT idClient FROM clients WHERE idUser = ?)");
                                $searchDocument->execute(array($donnees['idUser']));
                                $donneesDocument = $searchDocument->fetchAll();
                                if(count($donneesDocument) > 0){
                                    $disable = 'disabled';
                                    $disableFacture = '';
                                }else{
                                    $disable = '';
                                    $disableFacture = 'disabled';
                                }
                                echo '<tr>
                                        <td class="align-middle">'.$donnees['idClient'].'</td>
                                        <td class="align-middle">'.$donnees['nomFacturation'].'</td>
                                        <td class="align-middle">'.$donnees['prenomFacturation'].'</td>
                                        <td class="align-middle">'.$donnees['adresseFacturation'].'</td>
                                        <td class="align-middle">'.$donnees['cpFacturation'].'</td>
                                        <td class="align-middle">'.$donnees['villeFacturation'].'</td>
                                        <td>
                                        <a href="/admin/client/factures/?client='.$donnees['idUser'].'" class="btn btn-info '.$disableFacture.'"><i class="fas fa-file-invoice-dollar"></i></a>
                                        <a href="/admin/client/edition/?client='.$donnees['idUser'].'" class="btn btn-warning"><i class="fas fa-binoculars"></i></a>
                                        <a class="btn btn-danger '.$disable.'" href="/administration/client/ctrl/ctrl-delete-client.php?client='.$donnees['idUser'].'"><i class="fas fa-trash"></i></a></td>
                                    </tr>';
                                
                                    foreach($donneesDocument as $doc){
                                        echo '<tr>
                                        <td class="bg-info align-middle">Client: '.$donnees['idClient'].'</td>
                                        <td colspan="3" class="bg-info align-middle">Réf document en BDD: '.$doc['idDocument'].'</td>
                                        <td colspan="3" class="bg-info align-middle"><form method="POST" action="/administration/client/ctrl/ctrl-update-document-client.php">
                                        A transférer au client (idClient en BDD): <input type="text" name="nouvelId">
                                        <input type="hidden" name="doc" value="'.$doc['idDocument'].'">
                                        <button class="btn btn-success">MàJ</button>
                                        </form></td>
                                    </tr>';
                                    }
                            }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>

<?php include_once("../../commun/bas_de_page-admin.php");?>