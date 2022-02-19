<?php
@session_start ();
require("../../../controles/fonctions/adminOnline.php");
$titreDeLaPage = "[ADMIN] - PAIEMENTS";
$descriptionPage = "";
require("../../../commun/haut_de_page.php");
?>
<div class="container">
    <div class="row mt-2">
        <div class="card col-xl-9 mx-auto p-0">
            <div class="card-header bg-secondary text-white">État d'un paiement</div>
            <div class="card-body">
                <form method="get" action ="" class="d-flex">
                    <div class="form-group text-center">Identifiant Payplug (pay_xxx):
                        <input type="text" class="col" name="recherche" maxlength="40" placeholder="MAX 40 caractères..." required/>
                    </div> 
                    <div class="col text-center mt-1 mb-2">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-info border border-secondary">Chercher</button>
                            <a href="/admin/paiement/PAYPLUG/" class="btn btn-warning border border-secondary">Reset</a>
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
                    if(!isset($_GET['recherche'])){
                        echo '<div class="card-text text-center h4 p-2"> AUCUNE RECHERCHE...</div>';
                    }else{
                        require("../../../controles/fonctions/validation_donnees.php");
                        $recherche = valid_donnees($_GET['recherche']); 
                        $secretkey = $GLOBAL['secretPaiement'];
                            require_once('../../../PAIEMENT/PAYPLUG/payplug_php/lib/init.php');
                            Payplug\Payplug::init(array(
                                'secretKey' => $secretkey,
                                'apiVersion' => $GLOBALS['api-version'],
                            ));
                            
                        $payment = \Payplug\Payment::retrieve($recherche);
                        echo '<div class="col-11 mx-auto mt-3">
                                    Explications:
                                    <ul>
                                        <li>[id] => pay_xxxxxxxxxxxxx c\'est le numéro de transaction PAYPLUG</li>
                                        <li>[amount] => 350 veut dire le montant de la transaction en centimes</li>
                                        <li>[created_at] => 1614617508 veut dire crée le <a href="http://www.timestamp.fr/" target="_blank">Voir site de conversion</a></li>
                                        <li>[is_paid] => 1 veut dire transaction payée (si pas de chiffre = pas payée)</li>
                                        <li>[paid_at] => 1614617513 veut dire payée le <a href="http://www.timestamp.fr/" target="_blank">Voir site de conversion</a></li>
                                    </ul>
                                </div>';
                        
                        echo '<pre class="mt-4 col-11 mx-auto">';
                        print_r($payment);
                        echo '</pre>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

