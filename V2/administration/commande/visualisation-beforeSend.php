<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
require('../../bdd/table_config.php');
$tva = $donneesConfig[6]['valeur'];


if(!isset($_GET['doc'])){
    $_SESSION['alertMessage'] = "Il manque une info !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /administration/devis/liste-devis.php");
    exit();
}else{
    require_once("../../controles/fonctions/validation_donnees.php");
    $doc = valid_donnees($_GET['doc']);
    
    $sqlDevisExiste = $bdd -> prepare("SELECT * FROM documents WHERE idDocument = :doc");
    $sqlDevisExiste-> execute(array("doc" => $doc));
    $donneesDevisExiste = $sqlDevisExiste->fetch();
    $countVerifDevisExiste = $sqlDevisExiste -> rowCount();

    if($countVerifDevisExiste < 1){
        $_SESSION['alertMessage'] = "Devis inconnu dans la base !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: /administration/devis/liste-devis.php");
        exit();
    }else{

        $sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = ?");
        $sqlClient-> execute(array($donneesDevisExiste['idUser']));
        $donneeClient = $sqlClient->fetch();

        $sqlLignesDocument = $bdd ->prepare("SELECT * FROM documents_lignes WHERE idDocument = ?");
        $sqlLignesDocument-> execute(array($donneesDevisExiste['idDocument']));
        $donneesLignes = $sqlLignesDocument->fetch();
        $nbr_de_ligne_devis = $sqlLignesDocument->rowCount();

        //recherche si y a eu des achats de jeu complet
        $sqlDetailsDocumentsAchats = $bdd-> prepare("SELECT * FROM documents_lignes_achats WHERE idDocument = ?");
        $sqlDetailsDocumentsAchats->execute(array($donneesDevisExiste['idDocument']));
        $donneesDetailsDocumentAchats = $sqlDetailsDocumentsAchats->fetchAll();

        $titreDeLaPage = "[ADMIN] - Visualisation Facture ".$donneesDevisExiste['numero_facture'];
        $descriptionPage = "";
        include_once("../../commun/haut_de_page.php");
        include_once("../../commun/alertMessage.php");
        ?>

        <div class="container mt-5">
            <a href="/admin/commande/accueil/" class="btn btn-info"><i class="fas fa-arrow-alt-circle-left"> Retour aux commandes</i></a>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header text-white bg-dark text-center h4">Facture n° <?php echo $donneesDevisExiste['numero_facture']; ?></div>
                            <div class="card-body p-0">
                                    <table class="table table-striped mt-4 overflow-auto col-12 p-0">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th scope="col">Facturation</th>
                                                <th scope="col">Livraison</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center"><?php echo $donneesDevisExiste['adresse_facturation']; ?></td>
                                                <td class="text-center"><?php echo $donneesDevisExiste['adresse_livraison']; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
  
                                    <?php 
                                    $totalAchats = 0;
                                        if(count($donneesDetailsDocumentAchats) > 0){
                                            echo '<div class="col-12 p-0"><table class="table table-sm table-bordered">
                                            <thead class="thead-dark text-center">
                                                <th colspan="5">Jeux complets</th>
                                            </thead>
                                            <tbody>';
                                            foreach($donneesDetailsDocumentAchats as $detail){
                                                    //on recupere tout de la boite de jeu
                                                    $sqlJeuxCatalogue = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = ".$detail['idCatalogue']);
                                                    $donneesJeuxCatalogue = $sqlJeuxCatalogue -> fetch();
                                                    //on recupere tout de la boite de jeu
                                                    $sqlJC = $bdd -> query("SELECT * FROM jeux_complets WHERE idJeuxComplet = ".$detail['idJeuComplet']);
                                                    $donneesJC = $sqlJC->fetch();
                                                    echo '<tr>
                                                        <td class="align-middle text-center">'.$donneesJC['reference'].'</td>
                                                        <td class="align-middle">'.$donneesJeuxCatalogue['nom'].'<br/>'.$donneesJeuxCatalogue['editeur'].'<br/>'.$donneesJeuxCatalogue['annee'].'</td>
                                                        <td class="align-middle"><p>Jeu complet:</p>'.$detail['detailsComplet'].'</td>
                                                        <td class="align-middle text-center">'.$detail['qte'].'</td>
                                                        <td class="align-middle text-right">'.number_format(($detail['prix'] * $tva) / 100,'2','.','').'</td>
                                                    
                                                    </tr>';
                                                    $totalAchats += $detail['prix'] /100;
                                            }
                                            echo '</tbody></table></div>';
                                        }
                                    ?>
  
                                    <?php
                                        if($nbr_de_ligne_devis > 0) { ?>
                                        <table class="table table-striped mt-4 overflow-auto col-12">
                                            <thead class="thead-dark text-center">
                                                <tr>
                                                    <th scope="col" colspan="2">Image admin</th>
                                                    <th scope="col">Question / réponse</th>
                                                    <th scope="col">Jeu</th>
                                                    <th scope="col">Prix de la ligne</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                while($donneesLignes){
                                                    $sqlJeu = $bdd-> query("SELECT * FROM catalogue WHERE idCatalogue =".$donneesLignes['idJeu']);
                                                    $donneesJeu = $sqlJeu-> fetch();
                                                    //on regarde si y a deja une image enregistrer
                                                    $sqlVerifImageDocument = $bdd-> query("SELECT * FROM documents_images WHERE idDocuments = ".$donneesDevisExiste['idDocument']);
                                                    $donneesVerifImageDocument = $sqlVerifImageDocument-> fetch();
                                                    $countVerifImageDocument = $sqlVerifImageDocument-> rowCount();
                                                        if($countVerifImageDocument == 1){
                                                            $nameImageDocument = $donneesVerifImageDocument['nom'];
                                                            $affichageImage = '<div class="divImgPresentationExempleAdmin"><img src="data:image/jpeg;base64,'.$donneesVerifImageDocument['image'].'"><br/>'.$donneesVerifImageDocument['nom'].'</div>' ;
                                                        }else{
                                                            $nameImageDocument = "";
                                                            $affichageImage = "";
                                                        }

                                                echo '
                                                <tr>
                                                    <td class="text-center align-middle bg-vos">';
                                                    echo $affichageImage;
                                                    echo '
                                                    </td>
                                                    <td class="align-middle bg-vos">
                                                        <div class="form-group p-0">
                                                            <div class="image-upload text-center">
                                                                <label for="file-input">
                                                                    <i class="fas fa-camera fa-2x cursor-grab"></i>
                                                                </label>
                                                                <input type="file" name="photo[]" id="file-input" onchange="getFileInfo()" multiple/>
                                                            </div>
                                                        <div class="col-12 text-center" id="resultatInput"></div>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle bg-vos">
                                                        <input class="col" type="hidden" name="idLigne[]" value="'.$donneesLignes['idDocLigne'].'"/>
                                                        <input class="col" type="hidden" name="messageClient[]" value="'.$donneesLignes['question'].'"/><b><u>Question client: </u></b><br/>'.$donneesLignes['question'].'
                                                    </td>
                                                    <td class="text-center align-middle bg-vos">'.$donneesJeu['nom'].'</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="align-middle">Votre réponse:  '.$donneesLignes['reponse'].'</td>
                                                    <td class="text-right align-middle">'.number_format(($donneesLignes['prix'] * $tva) /100,"2",".","").'</td>
                                                </tr>';
                                                $donneesLignes = $sqlLignesDocument-> fetch();

                                                }
                                                ?>
                                                <tr>
                                                    <td colspan="4" class="text-right align-middle">Adhésion au service:</td>
                                                    <td class="text-right align-middle"><?php echo number_format(($donneesDevisExiste['prix_preparation'] * $tva )/ 100,"2",".",""); ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-right align-middle">
                                                        <?php
                                                            if($donneesDevisExiste['expedition'] == "poste"){
                                                                echo 'Envoi par La Poste';
                                                            }else{
                                                                echo 'Retrait à la Coop';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-right align-middle"><?php echo number_format(($donneesDevisExiste['prix_expedition'] * $tva) / 100,"2",".",""); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php 
                                        } 
                                    ?>
                                        <table class="table text-right col-9 mx-auto mt-5">
                                            <?php
                                                if($totalAchats > 0){
                                                    echo '<tr>
                                                    <td class="align-middle">Total HT achats:</td><td>'.$donneesDevisExiste['totalOccasions'].'</td>
                                                </tr>';
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-right align-middle">Total HT:</td><td><?php echo number_format($donneesDevisExiste['totalHT'] / 100,2,",",""); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right align-middle">TVA:</td><td><?php echo number_format($donneesDevisExiste['totalTVA'] / 100,2,",",""); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right align-middle">Total TTC:</td><td><?php echo number_format($donneesDevisExiste['totalTTC'] / 100,2,",",""); ?></td>
                                            </tr>
                                        </table>
                              
            

                            </div>
                        </div>
                    </div>
                </div>
      
        </div>
    <?php
    include_once("../../commun/bas_de_page-admin.php");
    }
}
?>