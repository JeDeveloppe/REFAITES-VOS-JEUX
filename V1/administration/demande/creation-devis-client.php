<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$titreDeLaPage = "[ADMIN] - Détails d'une demande";
$descriptionPage = "";

if(!isset($_GET['client'])){
    $_SESSION['alertMessage'] = "Il manque une info !";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /admin/commandes/");
    exit();
}else{
    require_once("../../controles/fonctions/validation_donnees.php");
    $client = valid_donnees($_GET['client']);
    
    $sqlVerifClientExiste = $bdd -> prepare("SELECT * FROM clients WHERE idClient = :client");
    $sqlVerifClientExiste-> execute(array("client" => $client));
    $donneesVerifClientExiste = $sqlVerifClientExiste->fetch();
    $countVerifClientExiste = $sqlVerifClientExiste -> rowCount();

    if($countVerifClientExiste < 1){
        $_SESSION['alertMessage'] = "Client inconnu dans la base !";
        $_SESSION['alertMessageConfig'] = "warning";
        header("Location: /admin/commandes/");
        exit();
    }else{
        $sqlCommandes = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = :user AND statut = :statut");
        $sqlCommandes-> execute(array("user" => $donneesVerifClientExiste['idUser'],"statut" => 1));
        $donneesCommande = $sqlCommandes->fetch();
        $nbr_de_ligne_demande = $sqlCommandes-> rowCount();

        include_once("../../bdd/table_config.php");
        include_once("../../commun/haut_de_page.php");
        include_once("../../commun/alertMessage.php");
        ?>
  
        <div class="container mt-4">
            <div class="col-12 text-center mb-5"><a class="text-secondary" href="/admin/accueil/">Retour à la liste des demandes</a></div>
                <form method="post" action="/administration/demande/ctrl/ctrl-creation-devis.php">
                    <div class="col h2 text-center">Demande / création devis</div>
                    <div class="row">
                        <div class="col mx-auto mt-2">
                                <table class="table table-sm table-striped mt-4 overflow-auto">
                                    <thead class="thead-dark text-center">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Infos du jeu</th>
                                            <th scope="col">Image(s) fournie(s)</th>
                                            <th scope="col">Message</th>
                                            <th scope="col">Image du jeu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $ligneTableau = 1;
                                        while($donneesCommande){
                                            //on recupere tout de la boite de jeu
                                            $sqlJeux = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = ".$donneesCommande['idJeu']);
                                            $donneesJeux = $sqlJeux -> fetch();
                                            //on cherche l'image du jeu
                                            $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$donneesJeux['idCatalogue']);
                                            $donneesImage = $sqlImage->fetch();
                                            //on recupere les images d'exemple s'il y en a
                                            $sqlImageExemple = $bdd -> query("SELECT * FROM listeMessages_images WHERE idListeMessages = ".$donneesCommande['idListeMessages']);
                                            $donneesImageExemple = $sqlImageExemple-> fetch();
                                            $countImageExemple = $sqlImageExemple->rowCount();
                                            
                                            ?>
                                            <tr>
                                                <td class="text-center align-middle"><?php echo $ligneTableau; ?></td>
                                                <td class="text-center align-middle"><?php echo $donneesJeux['nom']."<br/>".$donneesJeux['editeur']."<br/>".$donneesJeux['annee']; ?></td>
                                                <td class="text-danger text-center align-middle">
                                                    <?php 
                                                    if($countImageExemple < 1){
                                                        echo 'Aucune!';
                                                    }else{
                                                        while($donneesImageExemple){
                                                        echo '
                                                        <div class="divImgPresentationExempleAdmin mt-2">
                                                            <img src="data:image/jpeg;base64,'.$donneesImageExemple['image'].'"/>
                                                         </div>';
                                                        $donneesImageExemple = $sqlImageExemple-> fetch();
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td class="align-middle"><?php echo $donneesCommande['message']; ?><input type="hidden" name="messageClient[]" value="<?php echo $donneesCommande['message']; ?>" ></td>
                                                <td class="text-center align-middle">
                                                    <div class="divImgPresentationAdmin mt-4">
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
                                                            <?php
                                                            echo '<img class="zoom__image" src="data:image/jpeg;base64,'.$donneesImage['image'].'"/>';
                                                            ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center"><i class="fas fa-caret-square-right text-info fa-2x"></i><input type="hidden" name="jeu[]" value="<?php echo $donneesCommande['idJeu'];?>"</td>
                                                <td colspan="3" class="text-center align-middle"><input class="col" type="text" name="reponse[]" placeholder="Réponse propre, pensez à la facture... 80 caractères max..." maxlenght="80" required/></td>
                                                <td class="text-right align-middle">Prix: <input type="text" name="prixLigne[]" id="prixLigne" size="6" placeholder="0.00" pattern="^\d+(?:[.]\d{2})$" onKeyUp="calculDevis()" class="text-right" required/></td>
                                            </tr>
                                        <?php                                 
                                        $donneesCommande = $sqlCommandes->fetch();
                                        $ligneTableau++;
                                        }
                                        ?>
                                        <tr>
                                            <td colspan="4" class="text-right align-middle">Forfait de base :</td>
                                            <td class="text-right align-middle"><input type="text" name="prixPreparation" id="prixPreparation" value="<?php echo $donneesConfig[5]['valeur'];?>" size="6" class="text-center" pattern="^\d+(?:[.]\d{2})$"" onKeyUp="calculDevis()" required/></td>
                                        </tr>
                                    </tbody>
                                </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="jumbotron col-8 p-1 mx-auto">
                            <div class="col-12 d-flex">
                                <div class="col-6">
                                    <div class="col">Client / adresse:</div>
                                    <div class="col pl-5">
                                    <?php
                                    echo $donneesVerifClientExiste['nom'].' '.$donneesVerifClientExiste['prenom'].' '.$donneesVerifClientExiste['adresse'].' '.$donneesVerifClientExiste['cp'].' '.$donneesVerifClientExiste['ville'].' '.$donneesVerifClientExiste['pays'];
                                    ?>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="col">Retrait / envoi:</div>
                                    <div class="col pl-5">
                                    <?php
                                    if($donneesVerifClientExiste['port'] == "expedition"){
                                        if($donneesVerifClientExiste['optionExpedition'] == "mondialrelay"){
                                            echo "Expédition par Mondial Relay.";
                                            $selectValue = "mondialRelay";
                                        }else{
                                            echo "Expédition par Colissimo.";
                                            $selectValue = "colissimo";
                                        }
                                    }else if($donneesVerifClientExiste['port'] == "retrait_caen1"){
                                        echo "RETRAIT A LA COOP 5 pour 100.";
                                        $selectValue = "retrait_caen1";
                                    }
                                    ?>
                                    </div>
                                </div>
                                <div class="col-1 mt-3">
                                    <?php
                                        echo $donneesVerifClientExiste['pays'];
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-8 mx-auto">
                            <table class="table table-sm table-striped mt-1 overflow-auto">
                                <tbody>
                                    <tr>
                                        <td class="text-right align-middle"><i class="fas fa-caret-square-right text-info fa-2x ml-3"></i> Frais d'envoi:
                                            <select name="envoi">
                                                <option value="poste" selected>par La Poste</option>
                                                <option value="retrait_caen1">RETRAIT CAEN_1</option>
                                                <option value="mondialRelay">par Mondial Relay</option>
                                                <option value="colissimo">par Colissimo</option>
                                            </select>
                                        </td>
                                        <td class="text-right align-middle">
                                            <input type="text" name="prixExpedition" id="prixExpedition" size="5" placeholder="0.00" pattern="^\d+(?:[.]\d{2})$" class="text-center" onKeyUp="calculDevis()" required/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-center align-middle"><textarea type="text" name="commentaire" placeholder="Libre pour un commentaire... (max 400 caractères)" class="text-center col" maxlength="400"></textarea></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table text-center col-9 mx-auto">
                                <tr>
                                    <td class="align-middle">Total HT:</td><td><div id="totalDevisHT"></div></td>
                                </tr>
                                <tr>
                                    <td class="align-middle">TVA:</td><td><div id="totalDevisTVA"></div></td>
                                </tr>
                                <tr>
                                    <td class="align-middle">Total TTC:</td><td><div id="totalDevisTTC"></div></td>
                                </tr>
                            </table>
                            <input type="hidden" name="client" value="<?php echo $client;?>" />
                            <input type="hidden" name="nbr_lignes" value="<?php echo $nbr_de_ligne_demande;?>" />
                            <div class="col text-center">
                                <button class="btn btn-primary" type="submit">Créer le devis</button>
                                <a href="/administration/demande/ctrl/ctrl-envoi-devis-0-mail.php?client=<?php echo $client;?>" class="btn btn-warning border border-primary">Envoyer mail "pas de pièce(s)"</a>
                                <a href="/administration/demande/ctrl/ctrl-supprimer-demande.php?client=<?php echo $client;?>" class="btn btn-danger border border-primary"><i class="fas fa-trash-alt"> Supprimer la demande !</i></a>
                            </div>

                        </div>
                    </div>
                </form>
            <script>
                var tva = <?php echo json_encode($donneesConfig[6]['valeur']); ?>;
                var ttc = document.getElementById("totalDevisHT");
                var ht = document.getElementById("totalDevisTTC");
                var tvaDevis = document.getElementById("totalDevisTVA");

                ttc.innerHTML = "0.00";
                ht.innerHTML = "0.00";
                tvaDevis.innerHTML = "0.00";
                
                                                
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
                let totalht = notnull(totalLignes)+ notnull(prixPreparation) + notnull(prixExpedition);
                let totalHT = totalht.toFixed(2);
                let totalttc = totalHT * tva;
                let totalTTC = totalttc.toFixed(2);
                let totaltva = Number(totalTTC) - Number(totalHT);
                let totalTVA = totaltva.toFixed(2);

                document.getElementById("totalDevisHT").innerHTML = '<input type="text" name="totalHT" value="'+totalHT+'" class="col-4 text-right" size="5" readonly>';
                document.getElementById("totalDevisTVA").innerHTML = '<input type="text" name="totalTVA" value="'+totalTVA+'" class="col-4 text-right" size="5" readonly>';
                document.getElementById("totalDevisTTC").innerHTML = '<input type="text" name="totalTTC" value="'+totalTTC+'" class="col-4 text-right" size="5" readonly>';

                }
            </script>
        </div>
    <?php
    include_once("../../commun/bas_de_page-admin.php");
    }
}
?>