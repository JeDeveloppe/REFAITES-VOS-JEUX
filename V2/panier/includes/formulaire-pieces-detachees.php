<form class="col" method="post" action="<?php echo $hrefControlPanier;?>" name="contactretrait">
    <!-- PARTIE DES ADRESSES ET CHOIX RETRAIT LIVRAISON -->
    <div class="row mt-5">
    <div class="col-12 col-md-7 mt-4 mt-md-0">
            <!-- EXPEDITION / RETRAIT -->
            <div class="col-12">
                <div class="col-12 h4 text-center">Expédition / retrait:</div>
                    <ul class="list-group text-left">
                        <div class="list-group-item"> 
                            <div class="col-12 mt-2 d-flex">                   
                                <div class="col-1 pl-lg-5">
                                    <input type="radio" id="envoi" name="port" value="expedition" onclick="checkExpedition()" checked="" required="">
                                </div>
                                <div class="col-11">
                                    <label for="envoi">Je souhaite un envoi à mon domicile.</label>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="col-12 mt-2 d-flex">
                                <div class="col-1 pl-lg-5">
                                    <input type="radio" id="retrait" name="port" value="retrait_caen1" onclick="deleteExpedition()">
                                </div>
                                <div class="col-11">
                                    <label for="retrait">Je souhaite retirer ma commande à La Coop 5 pour 100, 33 route de Trouville, 14 000 Caen <br/>(pas de frais de port - prévoir un délai maximum de 7 jours).</label>
                                </div>
                            </div>
                        </div>
                    </ul>
            </div>
        </div>
        <div class="col-12 col-md-5 d-flex flex-wrap">
            <div class="col-10 mx-auto">
                <h4>Adresse de facturation:</h4>
                <?php
                    if($donneesClient['adresseFacturation'] == NULL || $donneesClient['cpFacturation'] == NULL || $donneesClient['villeFacturation'] == NULL){
                        
                        echo '<a href="/membre/adresses/#secteurfacturation">Renseigner l\'adresse de facturation</a>';
                    }else{
                        echo '<div class="pl-3">'.$donneesClient['nomFacturation'].' '.$donneesClient['prenomFacturation'].'<br/>'.$donneesClient['adresseFacturation'].'<br/>'.$donneesClient['cpFacturation'].' '.$donneesClient['villeFacturation'].' - '.$donneesClient['paysFacturation'].'<br/>
                        <a href="/membre/adresses/#secteurfacturation" class="btn bg-refaites text-white mt-3">Mettre à jour</a></div>';
                    
                    }
                ?>
            </div>
            <div class="col-10 mx-auto mt-3 <?php if($countAchats != 0){ echo 'd-none'; }?>" id="colAdresseLivraison">
                <h4>Adresse de livraison:</h4>
                <?php
                    if($donneesClient['adresseLivraison'] == NULL || $donneesClient['cpLivraison'] == NULL || $donneesClient['villeLivraison'] == NULL){
                        
                        echo '<a href="/membre/adresses/#secteurlivraison" >Renseigner l\'adresse de livraison</a>';
                    }else{
                        echo '<div class="pl-3">'.$donneesClient['nomLivraison'].' '.$donneesClient['prenomLivraison'].'<br/>'.$donneesClient['adresseLivraison'].'<br/>'.$donneesClient['cpLivraison'].' '.$donneesClient['villeLivraison'].' - '.$donneesClient['paysLivraison'].'<br/>
                        <a href="/membre/adresses/#secteurlivraison" class="btn bg-refaites text-white mt-3">Mettre à jour</a></div>';
                        
                    }
                ?>
            </div>
        </div>
    </div>
    <!-- CAPTCHA ET BOUTONS -->
    <div class="col mt-5 text-center">
        <div class="col-12 text-center">
            <div class="col-12 text-center mt-3 mb-3">
                <input id="lu" type="checkbox" name="conditionBienOk" value="lu" required> <label for="lu">J'ai lu et j'accepte les</label> <a href="/mentions-legales/#cgu">conditions générales d'utilisation</a>.<sup>(1)</sup>
            </div>
            <input type="hidden" id="recaptchaResponse" name="recaptcha-response">
            <?php
                if(
                    $donneesClient['adresseFacturation'] == NULL ||
                    $donneesClient['cpFacturation'] == NULL ||
                    $donneesClient['villeFacturation'] == NULL ||
                    $donneesClient['adresseLivraison'] == NULL ||
                    $donneesClient['cpLivraison'] == NULL ||
                    $donneesClient['villeLivraison'] == NULL){
                    echo '<a href="/membre/adresses/" id="href_bouton_panier" class="btn btn-danger">Merci de compléter les données manquantes afin de pouvoir continuer...</a>';
                }else{
                    echo '<button id="buttonPayerPanier" type="submit" class="btn btn-success border border-primary">'.$texteBoutonPaiement.'</button>';
                }
            ?>
        </div>
        <div class="col-12 text-right small mt-3 mt-sm-0">
            <a href="/panier/delete/" class="text-danger">SUPPRIMER MON PANIER</a>
        </div>
        <div class="col-12 text-danger text-left mt-3 mt-sm-0">
            <sup>(1)</sup> Obligatoire.
        </div>
    </div>
</form>