<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
require('../../bdd/table_config.php');
$tva = $donneesConfig[6]['valeur'];


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
        $sqlClient = $bdd -> prepare("SELECT * FROM clients WHERE idClient = ?");
        $sqlClient-> execute(array($donneesDevisExiste['idUser']));
        $donneeClient = $sqlClient->fetch();

        $sqlLignesDocument = $bdd ->prepare("SELECT * FROM documents_lignes WHERE idDocument = ?");
        $sqlLignesDocument-> execute(array($donneesDevisExiste['idDocument']));
        $donneesLignes = $sqlLignesDocument-> fetch();
        $nbr_de_ligne_devis = $sqlLignesDocument-> rowCount();

        //recherche si y a eu des achats de jeu complet
        $sqlDetailsDocumentsAchats = $bdd-> prepare("SELECT * FROM documents_lignes_achats WHERE idDocument = ?");
        $sqlDetailsDocumentsAchats->execute(array($donneesDevisExiste['idDocument']));
        $donneesDetailsDocumentAchats = $sqlDetailsDocumentsAchats->fetchAll();

        $titreDeLaPage = "[ADMIN] - Devis ".$devis;
        $descriptionPage = "";
        include_once("../../commun/haut_de_page.php");
        include_once("../../commun/alertMessage.php");
        ?>

        <div class="container mt-5">
            <div class="col-12 h2 text-center">Devis n° <?php echo $donneesDevisExiste['numero_devis']; ?></div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header text-white bg-dark">Devis:</div>
                            <div class="card-body">
                                <div class="col-12 p-0">
                                    <table class="table table-striped mt-4 overflow-auto col-12 p-0">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th scope="col">Destinataire</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="col text-center"><?php echo $donneeClient['nomFacturation']." ".$donneeClient['prenomFacturation']." ".$donneeClient['adresseFacturation']." ".$donneeClient['cpFacturation']." ".$donneeClient['villeFacturation']." - ".$donneeClient['paysFacturation']."<br/><i class='fas fa-envelope'></i> : ".$donneeClient['email'].'<br/>DEMANDE D\' ORIGINE:<br /><span class="text-danger">'.mb_strtoupper($donneeClient['port']).'</span>';?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
                               

                                </div>
                                <div class="col-12 p-0">
                                    <form method="post" action="/administration/devis/ctrl/ctrl-edition.php" enctype="multipart/form-data">
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
                                                    <td colspan="4" class="text-center align-middle"><textarea class="form-control" style="min-width: 100%" name="reponse[]" placeholder="Description pour la/les ligne(s)..." required>'.$donneesLignes['reponse'].'</textarea></td>
                                                    <td class="text-right align-middle"><input class="text-right" type="text" name="prixLigne[]" id="prixLigne" size="6" pattern="^\d+(?:[.]\d{2})$" placeholder="0.00" onKeyUp="calculDevis()" value="'.number_format(($donneesLignes['prix'] * $tva) /100,"2",".","").'" required/></td>
                                                </tr>';
                                                $donneesLignes = $sqlLignesDocument-> fetch();

                                                }
                                                ?>
                                                <tr>
                                                    <td colspan="4" class="text-right align-middle">Adhésion au service:</td>
                                                    <td class="text-right align-middle"><input type="text" name="prixPreparation" id="prixPreparation" value="<?php echo number_format(($donneesDevisExiste['prix_preparation'] * $tva )/ 100,"2",".",""); ?>" size="6" pattern="^\d+(?:[.]\d{2})$" class="text-center" onKeyUp="calculDevis()" required readonly="readonly"/></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-right align-middle">Frais d'envoi:
                                                        <select name="envoi">
                                                            <option value="poste" <?php if($donneesDevisExiste['expedition'] == "poste"){echo 'selected';}?>>par La Poste</option>
                                                            <option value="retrait_caen1" <?php if($donneesDevisExiste['expedition'] == "retrait_caen1"){echo 'selected';}?>>RETRAIT CAEN_1</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-right align-middle"><input type="text" name="prixExpedition" id="prixExpedition" value="<?php echo number_format(($donneesDevisExiste['prix_expedition'] * $tva) / 100,"2",".",""); ?>" size="6" placeholder="0.00" pattern="^\d+(?:[.]\d{2})$" class="text-right" onKeyUp="calculDevis()" required/></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="text-center align-middle"><textarea type="text" name="commentaire" placeholder="Libre pour un commentaire... (max 200 caractères)" class="text-center col" maxlength="200"><?php echo $donneesDevisExiste['commentaire']; ?></textarea></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table text-right col-9 mx-auto">
                                            <?php
                                                if($totalAchats > 0){
                                                    echo '<tr>
                                                    <td class="align-middle">Total HT achats:</td><td><div id="totalHTachats"></div></td>
                                                </tr>';
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-right align-middle">Total HT:</td><td><div id="totalDevisHT"></div></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right align-middle">TVA:</td><td><div id="totalDevisTVA"></div></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right align-middle">Total TTC:</td><td><div id="totalDevisTTC"></div></td>
                                            </tr>
                                        </table>
                                        <input type="hidden" name="client" value="<?php echo $donneesDevisExiste['idUser'];?>" />
                                        <input type="hidden" name="document" value="<?php echo $donneesDevisExiste['idDocument'];?>" />
                                        <input type="hidden" name="doc" value="<?php echo $devis?>" />
                                        <input type="hidden" name="nbr_lignes" value="<?php echo $nbr_de_ligne_devis;?>" />
                                        <?php
                                        if($donneesDevisExiste['etat'] == 1){ // 1 = EDITION, les autres = payer, supprimer ou autre
                                            echo '
                                            <div class="col-12 text-center">
                                                <button class="btn btn-primary" type="submit">Mettre à jour</button>
                                                <a href="#" class="btn btn-warning border-primary" onclick="confirmationEnvoiEmail()">Envoyer par email</a>';
                                                if($donneesDevisExiste['time_mail_devis'] != ""){
                                                    echo '<br/><i class="fas fa-paper-plane text-success mt-3"> '.date('d.m.Y',$donneesDevisExiste['time_mail_devis']).'</i>';
                                                }else{
                                                    echo '<br/><i class="fas fa-paper-plane text-danger mt-3"> Devis non envoyé</i>';
                                                }
                                                
                                            echo '</div>';
                                        }
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <script>
            var totalHTAchats = <?php echo json_encode($totalAchats); ?>;
            let devis = <?php echo json_encode($devis); ?>;
            let nbLigne = <?php echo json_encode($nbr_de_ligne_devis); ?>;
            var tva = <?php echo json_encode($donneesConfig[6]['valeur']); ?>;
            var ht = <?php echo json_encode(number_format($donneesDevisExiste['totalHT'] / 100,"2",".","")); ?>;
            var ttc = <?php echo json_encode(number_format($donneesDevisExiste['totalTTC'] / 100,"2",".","")); ?>;
            var tvaDevis = <?php echo json_encode(number_format($donneesDevisExiste['totalTVA'] / 100,"2",".","")); ?>;;

            if(totalHTAchats > 0){
            document.getElementById("totalHTachats").innerHTML = totalHTAchats.toFixed(2);
            }
            document.getElementById("totalDevisHT").innerHTML = '<input type="text" name="totalHT" value="'+ht+'" class="col-2 text-right" readonly>';
            document.getElementById("totalDevisTVA").innerHTML = '<input type="text" name="totalTVA" value="'+tvaDevis+'" class="col-2 text-right" readonly>';
            document.getElementById("totalDevisTTC").innerHTML = '<input type="text" name="totalTTC" value="'+ttc+'" class="col-2 text-right" readonly>';
            
                                            
            function notnull(str){
                if(str === ""){
                    str = "0";
                }
                return str;
            }
            function calculDevis(){
                
                elts = document.getElementsByName('prixLigne[]');
                    totalLignes = 0;
                    for (myinput of elts) {
                    totalLignes += +myinput.value;
                    }


                const prixPreparation = Number(document.getElementById("prixPreparation").value);
                const prixExpedition = Number(document.getElementById("prixExpedition").value);
                let totalht = notnull(totalLignes) + notnull(prixPreparation) + notnull(prixExpedition) + notnull(totalHTAchats);
                let totalHT = totalht.toFixed(2);
                let totalttc = totalHT * tva;
                let totalTTC = totalttc.toFixed(2);
                let totaltva = Number(totalTTC) - Number(totalHT);
                let totalTVA = totaltva.toFixed(2);

                document.getElementById("totalDevisHT").innerHTML = '<input type="text" name="totalHT" value="'+totalHT+'" class="col-4 text-right" readonly>';
                document.getElementById("totalDevisTVA").innerHTML = '<input type="text" name="totalTVA" value="'+totalTVA+'" class="col-4 text-right" readonly>';
                document.getElementById("totalDevisTTC").innerHTML = '<input type="text" name="totalTTC" value="'+totalTTC+'" class="col-4 text-right" readonly>';
            }

            function confirmationEnvoiEmail(){
                var val = confirm("Vous êtes sûr de vouloir envoyer le devis dans l'état ?");
                if( val == true ) {
                    window.location.href = "/administration/devis/ctrl/ctrl-envoi-devis-mail.php?devis="+devis; 
                } else {
                    window.location.href = "/administration/devis/edition.php?devis="+devis; 
            }
            }

            var countImageDocument = <?php echo json_encode($countVerifImageDocument); ?>;
            var resultatInput = document.getElementById('resultatInput');  

            if(isset(countImageDocument) && countImageDocument == 1){
                var nameImageDocument = <?php echo json_encode($nameImageDocument); ?>;
                resultatInput.innerHTML = '<a href=""><i class="fas fa-trash-alt fa-2x text-danger"></i></a>';
            }else{
                resultatInput.innerHTML = "Aucune image !";
            }
            

            function getFileInfo(){
                var countFiles = document.getElementById('file-input').files.length;

                if(countFiles > 1){
                    resultatInput.innerHTML = "2 images séléctionnées";
                }else{
                    var name = document.getElementById('file-input').files[0].name;
                    resultatInput.innerHTML = "Image: "+name;
                }
            }
        </script>
        </div>
    <?php
    include_once("../../commun/bas_de_page-admin.php");
    }
}
?>