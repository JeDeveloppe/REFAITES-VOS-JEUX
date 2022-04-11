<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
include('../../bdd/table_config.php');
$tva = $donneesConfig[6]['valeur'];
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
    $panier = valid_donnees($_GET['panier']);
    
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
        //on verifie si membre a date
        if($donneesVerifClientExiste['isAssociation'] > time()){
            $inputAssociationValue = '<input type="text" name="prixPreparation" id="prixPreparation" value="0.00" size="6" class="text-center" pattern="^\d+(?:[.]\d{2})$"" onKeyUp="calculDevis()" required readonly="readonly"/>';
            $dateEndAssociation = '<span class="text-success">Jusqu\'au '.date('d/m/Y',$donneesVerifClientExiste['isAssociation']).'</span>';
        }else{
            $inputAssociationValue = '<input type="text" name="prixPreparation" id="prixPreparation" value="'.$donneesConfig[28]['valeur'].'" size="6" class="text-center" pattern="^\d+(?:[.]\d{2})$"" onKeyUp="calculDevis()" required/>';
            $dateEndAssociation = '<span class="text-danger">A facturer</span>';
        }

        $sqlCommandesAchats = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND statut = ? AND qte > 0 AND panierKey = ?");
        $sqlCommandesAchats-> execute(array($donneesVerifClientExiste['idUser'],1,$panier));
        $donneesCommandeAchats = $sqlCommandesAchats->fetchAll();
        $nbr_de_ligne_achat = $sqlCommandesAchats-> rowCount();

        $sqlCommandes = $bdd -> prepare("SELECT * FROM listeMessages WHERE idUser = ? AND statut = ? AND qte IS NULL AND panierKey = ?");
        $sqlCommandes-> execute(array($donneesVerifClientExiste['idUser'],1,$panier));
        $donneesCommande = $sqlCommandes->fetch();
        $nbr_de_ligne_demande = $sqlCommandes-> rowCount();

        include_once("../../bdd/table_config.php");
        include_once("../../commun/haut_de_page.php");
        include_once("../../commun/alertMessage.php");
        ?>
  
        <div class="container mt-4">
            <div class="col-12 text-left my-4"><a class="text-secondary" href="/admin/accueil/"><i class="fas fa-chevron-left"> Retour à la liste des demandes</i></a></div>
                <form method="post" action="/administration/demande/ctrl/ctrl-creation-devis.php">
                    <div class="col-12 h2 text-center">Demande / création devis</div>
                    <?php
                        $totalAchats = 0;
                        if($nbr_de_ligne_achat > 0){
                            echo '<div class="row">
                            <div class="col-12 mt-2">
                                <table class="table table-sm table-striped mt-4 overflow-auto table-bordered">
                                    <thead class="thead-dark text-center">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Infos du jeu</th>
                                            <th scope="col">Détails du jeu</th>
                                            <th scope="col">Prix HT</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    
                                    $ligneTableauAchat = 1;
                                    
                                        foreach($donneesCommandeAchats as $achat){
                                            //on recupere tout de la boite de jeu
                                            $sqlJeuxComplet = $bdd->query("SELECT * FROM jeux_complets WHERE idJeuxComplet = ".$achat['idJeu']);
                                            $donneesJeuxComplet = $sqlJeuxComplet->fetch();
                                            //on recupere les informations de la coquille
                                            $sqlCoquille = $bdd->query("SELECT * FROM catalogue WHERE idCatalogue = ".$donneesJeuxComplet['idCatalogue']);
                                            $donneesCoquille = $sqlCoquille->fetch();                                       
                                            ?>
                                            <tr>
                                                <td class="text-center align-middle"><?php echo $ligneTableauAchat; ?></td>
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
                                                            echo '<img class="zoom__image" src="data:image/jpeg;base64,'.$donneesCoquille['imageBlob'].'"/>';
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 lead text-muted small text-center">
                                                        Photo non contractuelle
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle"><?php echo "<p>Référence: ".$donneesJeuxComplet['reference']."</p><p>".$donneesCoquille['nom']."<br/>".$donneesCoquille['editeur']."<br/>".$donneesCoquille['annee']."</p>"; ?></td>
                                                <td class="text-center align-middle">
                                                    <?php 
                                                        if($donneesJeuxComplet['isNeuf'] == true){
                                                            echo 'COMME NEUF';
                                                        }else{
                                                            echo 'État de la boite: '.$donneesJeuxComplet['etatBoite'].'<br/>État du matériel: '.$donneesJeuxComplet['etatMateriel'].'<br/>Règle du jeu: '.$donneesJeuxComplet['regleJeu']; 
                                                        }
                                                    ?>
                                                </td>
                                                <td class="align-middle text-center"><?php echo number_format($achat['tarif']/100,'2',',',''); ?></td>
                                            </tr>
                                        <?php                                 
                                        $totalAchats += $achat['tarif'] / 100;
                                        $ligneTableauAchat++;
                                        }
                                        echo '
                                    </tbody>
                                </table>
                            </div>
                        </div>';
                        }
                    ?>
                    <div class="row">
                        <div class="col-12 mx-auto mt-2">
                            <table class="table table-sm table-striped mt-4 overflow-auto table-bordered">
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
                                                        echo '<img class="zoom__image" src="data:image/jpeg;base64,'.$donneesJeux['imageBlob'].'"/>';
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
                                        <td colspan="5" class="text-right align-middle">Adhésion au service <?php echo $dateEndAssociation; ?> : <?php echo $inputAssociationValue;?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="card col-9 mx-auto">
                            <div class="card-body d-flex flex-wrap align-items-center">
                                <div class="col-5">
                                    <div class="col-12">Client / adresse:</div>
                                    <div class="col pl-5">
                                    <?php
                                    echo $donneesVerifClientExiste['nomFacturation'].' '.$donneesVerifClientExiste['prenomFacturation'].'<br/>'.$donneesVerifClientExiste['adresseFacturation'].'<br/>'.$donneesVerifClientExiste['cpFacturation'].' '.$donneesVerifClientExiste['villeFacturation'].' '.$donneesVerifClientExiste['paysFacturation'].'<br/>'.$donneesVerifClientExiste['telephone'];
                                    ?>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="col-12">Retrait / envoi:</div>
                                    <div class="col-12 pl-5">
                                    <?php
                                    if($donneesVerifClientExiste['port'] == "expedition"){
                                            echo "Expédition par La Poste.";
                                            $selectValue = "la_poste";
                                    }else if($donneesVerifClientExiste['port'] == "retrait_caen1"){
                                        echo "RETRAIT A LA COOP 5 pour 100.";
                                        $selectValue = "retrait_caen1";
                                    }
                                    ?>
                                    </div>
                                </div>
                                <div class="col-2 text-center">
                                    Pays:
                                    <?php
                                        echo $donneesVerifClientExiste['paysFacturation'];
                                    ?>
                                
                                </div>
                            </div>
                        </div>
                        <div class="card col-9 mx-auto mt-4">
                            <div class="card-body d-flex flex-wrap">
                                <div class="col-7 text-right"><i class="fas fa-caret-square-right text-info fa-2x ml-3"> Frais d'envoi:</i></div>
                                <div class="col-4"><select name="envoi" class="form-control">
                                        <option value="poste"  required >par La Poste</option>
                                        <option value="retrait_caen1" <?php if($donneesVerifClientExiste['port'] == "retrait_caen1"){ echo "selected";}?> >RETRAIT CAEN à la COOP</option>
                                    </select>
                                </div>
                                <div class="col-1 text-right h5">
                                    <input type="text" name="prixExpedition" id="prixExpedition" size="5" placeholder="0.00" pattern="^\d+(?:[.]\d{2})$" class="text-center" onKeyUp="calculDevis()" required/>
                                </div>
                                <div class="col-12 mt-3">
                                    <textarea type="text" name="commentaire" placeholder="Libre pour un commentaire... (max 400 caractères)" class="text-center col" maxlength="400"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <table class="table text-center col-9 mx-auto mt-3">
                                <?php
                                    if($totalAchats > 0){
                                        echo '<tr>
                                        <td class="align-middle">Total HT achats:</td><td><div id="totalHTachats"></div></td>
                                    </tr>';
                                    }
                                ?>
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
                            <input type="hidden" name="panier" value="<?php echo $panier;?>" />
                        </div>
                        <div class="col-12 text-center">
                        <button class="btn btn-primary " type="submit">Créer le devis</button>
                        </div>
                        <div class="col-12 text-center mt-4">
                            <a href="/administration/demande/ctrl/ctrl-envoi-devis-0-mail.php?client=<?php echo $client;?>&panier=<?php echo $panier;?>" class="btn btn-warning text-dark border border-primary">Envoyer mail "pas de pièce(s)"</a>
                            <a href="/administration/demande/ctrl/ctrl-supprimer-demande.php?client=<?php echo $client;?>&panier=<?php echo $panier;?>" class="btn btn-danger border border-primary"><i class="fas fa-trash-alt"> Supprimer la demande !</i></a>
                            <a href="/administration/demande/ctrl/ctrl-envoi-3cm-mail.php?client=<?php echo $client;?>&panier=<?php echo $panier;?>" class="btn btn-warning text-dark border border-primary">Envoyer mail "Pièce(s) > 3cm"</a>
                        </div>
                    </div>
                </form>
        </div>

    <?php
    include_once("../../commun/bas_de_page-admin.php");
    }
}
?>
<script>
    var totalAchats = <?php echo json_encode($totalAchats); ?>;
    var ttHTachats = document.getElementById("totalHTachats");
    var tva = <?php echo json_encode($donneesConfig[6]['valeur']); ?>;
    var ttc = document.getElementById("totalDevisHT");
    var ht = document.getElementById("totalDevisTTC");
    var tvaDevis = document.getElementById("totalDevisTVA");

    if(totalAchats > 0){
        ttHTachats.innerHTML = '<input type="hidden" name="totalPrixOccasion" value="'+totalAchats*100+'">'+totalAchats.toFixed(2);
    }
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
    let totalht = notnull(totalLignes)+ notnull(prixPreparation) + notnull(prixExpedition) + notnull(totalAchats);
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