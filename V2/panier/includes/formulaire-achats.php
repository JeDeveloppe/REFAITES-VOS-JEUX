<form class="col" method="post" action="<?php echo $hrefControlPanier;?>" name="contactretrait">
    <!-- PARTIE DES ADRESSES ET CHOIX RETRAIT LIVRAISON -->
    <div class="row mt-5">
        <div class="col-12 col-md-6">
            <div class="col-10 mx-auto">
                <h4>Adresse de facturation:</h4>
                <?php
                    if($donneesClient['adresseFacturation'] == NULL || $donneesClient['cpFacturation'] == NULL || $donneesClient['villeFacturation'] == NULL){
                        
                        echo '<a href="/membre/adresses/#secteurfacturation">Renseigner l\'adresse de facturation</a>';
                    }else{
                        if(strlen($donneesClient['organismeFacturation']) > 0){
                            $organisme = $donneesClient['organismeFacturation'];
                        }else{
                            $organisme = "";
                        }
                        echo '<div class="pl-3">'.$organisme.'<br/>'.$donneesClient['nomFacturation'].' '.$donneesClient['prenomFacturation'].'<br/>'.$donneesClient['adresseFacturation'].'<br/>'.$donneesClient['cpFacturation'].' '.$donneesClient['villeFacturation'].' - '.$donneesClient['paysFacturation'].'<br/>
                        <a href="/membre/adresses/#secteurfacturation" class="btn bg-refaites text-white mt-3">Mettre à jour</a></div>';
                    
                    }
                ?>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-4 mt-md-0">
            <!-- EXPEDITION / RETRAIT -->
            <div class="col-10 mx-auto">
                <h4>Retrait:</h4>
                <div class="pl-3">
                    <?php echo substr($donneesConfig[9]['valeur'],0,-2).'<br/>Du mercredi au Vendredi: 11h00 - 19h00<br/>Le Samedi: 11h00 - 18h00<br/>(prévoir un délai maximum de 7 jours)'; ?>
                </div>
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
            <button id="buttonPayerPanier" type="submit" class="btn btn-success border border-primary"><?php echo $texteBoutonPaiement; ?></button>
        </div>
        <div class="col-12 text-right small mt-3 mt-sm-0">
            <a href="/panier/delete/" class="text-danger">SUPPRIMER MON PANIER</a>
        </div>
        <div class="col-12 text-danger text-left mt-3 mt-sm-0">
            <sup>(1)</sup> Obligatoire.
        </div>
    </div>
</form>