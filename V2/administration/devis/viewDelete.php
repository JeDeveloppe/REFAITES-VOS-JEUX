<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');


if(!isset($_GET['devis'])){
    $_SESSION['alertMessage'] = "Il manque une info !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /administration/devis/liste-devis.php");
    exit();
}else{
    require_once("../../controles/fonctions/validation_donnees.php");
    $devis = valid_donnees($_GET['devis']);
    
    $sqlDevisExiste = $bdd -> prepare("SELECT * FROM documents WHERE numero_devis = :devis");
    $sqlDevisExiste-> execute(array("devis" => $devis));
    $donneesDevisExiste = $sqlDevisExiste->fetch();
    $countVerifDevisExiste = $sqlDevisExiste -> rowCount();

    if($countVerifDevisExiste < 1){
        $_SESSION['alertMessage'] = "Devis inconnu dans la base !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: /administration/devis/liste-devis.php");
        exit();
    }else{
        $sqlLignesDocument = $bdd ->prepare("SELECT * FROM documents_lignes WHERE idDocument = ?");
        $sqlLignesDocument-> execute(array($donneesDevisExiste['idDocument']));
        $donneesLignes = $sqlLignesDocument-> fetch();
        $nbr_de_ligne_devis = $sqlLignesDocument-> rowCount();

        $sqlLignesDocumentAchats = $bdd ->prepare("SELECT * FROM documents_lignes_achats WHERE idDocument = ?");
        $sqlLignesDocumentAchats-> execute(array($donneesDevisExiste['idDocument']));
        $donneesLignesAchats = $sqlLignesDocumentAchats-> fetchAll();
        $nbr_de_ligne_devisAchats = $sqlLignesDocumentAchats-> rowCount();

        $titreDeLaPage = "[ADMIN] - Devis ".$devis;
        $descriptionPage = "";
        include_once("../../bdd/table_config.php");
        include_once("../../commun/haut_de_page.php");
        include_once("../../commun/alertMessage.php");
        ?>

        <div class="container-fluid mt-4">
            <div class="col h2 text-center">Devis n° <?php echo $donneesDevisExiste['numero_devis']; ?><br/>Pièce(s) à ranger...</div>
            <div class="col"><a class="text-decoration-none" href="<?php echo $_SERVER['HTTP_REFERER'];?>"><- Retour en arrière</a></div>
                <div class="row mt-3">
                    <div class="col-11 p-0 mx-auto">
                        <div class="card">
                            <div class="card-header text-white bg-dark">Détail(s)</div>
                            <div class="card-body">
                                        <?php
                                            if($nbr_de_ligne_devisAchats > 0){
                                                echo '<table class="table table-striped mt-4 overflow-auto">
                                                <thead class="thead-dark text-center">
                                                    <tr>
                                                        <th scope="col">Jeu d\'occasion</th>
                                                        <th scope="col">État</th>
                                                        <th scope="col">Qté</th>
                                                        <th scope="col">Référence</th>
                                                        <th scope="col">Action stock</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                   
                                                    foreach($donneesLignesAchats as $ligneAchat){
                                                        $sqlJeu = $bdd-> query("SELECT * FROM jeux_complets WHERE idJeuxComplet =".$ligneAchat['idJeuComplet']);
                                                        $donneesJeu = $sqlJeu-> fetch();
                                                        $sqlCatalogue = $bdd-> query("SELECT * FROM catalogue WHERE idCatalogue =".$ligneAchat['idCatalogue']);
                                                        $donneesCatalogue = $sqlCatalogue-> fetch();
                                                    echo '
                                                    <tr>
                                                        <td class="text-center align-middle bg-vos">'.$donneesCatalogue['nom'].'<br/>'.$donneesCatalogue['editeur'].'<br/>'.$donneesCatalogue['annee'].'</td>
                                                        <td class="text-center align-middle bg-vos">'.$ligneAchat['detailsComplet'].'</td>
                                                        <td class="text-center align-middle bg-vos">'.$ligneAchat['qte'].'</td>
                                                        <td class="text-center align-middle bg-vos">'.$donneesJeu['reference'].'</td>
                                                        <td class="text-center align-middle"><a class="btn btn-success" href="/administration/devis/ctrl/ctrl-deleteLigneAchat.php?id='.$ligneAchat['idDocLigneAchat'].'">Remettre en stock</a></td>
                                                    </tr>';
                                                     }
                                                    echo '
                                                </tbody>
                                            </table>';
                                            }
                                        ?>
                                        <table class="table table-striped mt-4 overflow-auto">
                                            <thead class="thead-dark text-center">
                                                <tr>
                                                    <th scope="col">Question</th>
                                                    <th scope="col">Jeu</th>
                                                    <th scope="col">Réponse</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                while($donneesLignes){
                                                    $sqlJeu = $bdd-> query("SELECT * FROM catalogue WHERE idCatalogue =".$donneesLignes['idJeu']);
                                                    $donneesJeu = $sqlJeu-> fetch();
                                                echo '
                                                <tr>
                                                    <td class="align-middle bg-vos">
                                                    <input class="col" type="hidden" name="idLigne[]" value="'.$donneesLignes['idDocLigne'].'"/>
                                                    <input class="col" type="hidden" name="messageClient[]" value="'.$donneesLignes['question'].'"/><b><u>Question client: </u></b><br/>'.$donneesLignes['question'].'</td>
                                                    <td class="text-center align-middle bg-vos">'.$donneesJeu['nom'].'<br/>'.$donneesJeu['editeur'].'<br/>'.$donneesJeu['annee'].'</td>
                                                    <td class="text-center align-middle"><textarea class="col" type="text" name="reponse[]" placeholder="Description pour la/les ligne(s) 50 caractères max..." maxlenght="50" readonly/>'.$donneesLignes['reponse'].'</textarea></td> 
                                                </tr>';
                                                $donneesLignes = $sqlLignesDocument-> fetch();

                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <?php if($donneesDevisExiste['etat'] == 0 || $donneesDevisExiste['etat'] == 1){ // 1 = EDITION, les autres = payer, supprimer ou autre
                                            echo '
                                            <div class="col text-center">
                                                <a href="#" onclick="confirmationSuppressionDevis()" class="btn btn-danger ml-3"><i class="fas fa-trash"> Supprimer définitivement le devis</i></a>
                                            </div>';
                                        }
                                        ?>
                            </div>
                        </div>
                    </div>
                </div>
        <script>
            let devis = <?php echo json_encode($devis); ?>;

            function confirmationSuppressionDevis(){
                var val = confirm("Tout sera supprimer, si jeu(x) il y a, est il (sont-ils) bien remis en stock ?");
                if( val == true ) {
                    window.location.href = "/administration/ctrl/ctrl-delete-devis.php?devis="+devis; 
                } else {
                    window.location.href = "/administration/devis/viewDelete.php?devis="+devis; 
}
            }
        </script>
        </div>
    <?php
    include_once("../../commun/bas_de_page-admin.php");
    }
}
?>