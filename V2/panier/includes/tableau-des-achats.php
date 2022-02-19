<div class="row mt-2 overflow-auto">
    <div class="col-12 mt-4">
       <div class="col-12 border-bottom">Jeux d'occasion:</div>
            <div class="table-responsive">
                <table class="table table-sm table-striped col-12 mt-3">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>Action</th>
                            <th>Image</th>
                            <th>Jeu</th>
                            <th>État du jeu</th>
                            <th>Total TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $totalHT = 0;
                        while($donneesListeAchats){  
                            //information du jeu complet
                            $requeteJeuxComplet = "SELECT * FROM jeux_complets WHERE idJeuxComplet = ".$donneesListeAchats['idJeu'];
                            $sqlJeuxComplet = $bdd -> query($requeteJeuxComplet);
                            $donneesJeuxComplet = $sqlJeuxComplet-> fetch();

                            //on recupere tout de la boite de jeu
                            $sqlJeux = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = (SELECT idCatalogue FROM jeux_complets WHERE idJeuxComplet = ".$donneesListeAchats['idJeu'].")");
                            $donneesJeux = $sqlJeux -> fetch();
                            ?>
                            <tr>
                            <td class="text-center align-middle">
                                <a href="/achats/delete/<?php echo $donneesListeAchats['idListeMessages'];?>/" class="btn btn-danger p-1"><i class="fas fa-trash-alt" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Supprimer de la liste"></i></a>
                            </td>
                            <td class="text-center align-middle"><?php echo '<div class="divImgTableauListeMessage"><img src="data:image/jpeg;base64,'.$donneesJeux['imageBlob'].'"/></div>'; ?></td>
                            <td class="text-center align-middle"><?php echo $donneesJeux['nom']."<br/>".$donneesJeux['editeur']; ?></td>
                            <td class="text-center align-middle">
                                <?php 
                                    if($donneesJeuxComplet['isNeuf'] == true){
                                        echo 'COMME NEUF';
                                    }else{
                                        echo 'État de la boite: '.$donneesJeuxComplet['etatBoite'].'<br/>État du matériel: '.$donneesJeuxComplet['etatMateriel'].'<br/>Règle du jeu: '.$donneesJeuxComplet['regleJeu']; 
                                    }
                                ?>
                            </td>
                            <td class="text-center align-middle"><?php echo number_format(($donneesListeAchats['qte'] * $donneesJeuxComplet['prixHT'] * $tva )/100,2,",",' '); ?></td>
                            </tr>
                        <?php
                        $totalHT += $donneesListeAchats['qte'] * $donneesJeuxComplet['prixHT'];
                        $donneesListeAchats = $sqlListeAchats->fetch();
                        }
                        ?>
                    </tbody>
                </table>
            </div>
    
    </div>
</div>

<?php
    //SI Y A PAS DES PIECES DETACHEES 
    if($countDemandes < 1){ ?>
        <div class="row mt-3 justify-content-end">
            <div class="col-11 col-sm-6 col-md-4 col-lg-3">
                <table class="table table-sm table-striped mt-4">
                    <tbody>
                        <tr>
                            <td>Sous total HT:</td>
                            <td class="text-right align-middle">
                                <?php
                                    $_SESSION['totalAchats'] = 0;
                                    $sousTotalHT = number_format($totalHT / 100,2,","," ");
                                    $_SESSION['totalAchats'] =  number_format($totalHT / 100,2,"."," ");
                                    echo $sousTotalHT;
                                    $totalHTavecAdhesion = $totalHT + $adhesionRVJ * 100;
                                    $_SESSION['ht'] = number_format($totalHTavecAdhesion / 100,2,"."," ");
                                    $totalTTC = ($totalHT+ $adhesionRVJ*100) * $tva;
                                    $_SESSION['ttc'] = number_format($totalTTC/100 ,2,"."," ");
                                    $adhesionRVJTTC = $adhesionRVJ * $tva;
                                    $TVA = $totalTTC - $totalHTavecAdhesion;
                                    $_SESSION['tva'] = number_format($TVA/100,2,"."," ");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Adhésion RVJ:
                                <?php
                                    if($donneesClient['isAssociation'] > time()){
                                        echo '<br><small>Jusqu\'au '.date("d.m.Y à G:i",$donneesClient['isAssociation']).'</small>';
                                    }
                                ?>
                            </td>
                            <td class="text-right align-middle">
                                <?php
                                    if($donneesClient['isAssociation'] > time()){
                                        echo '0,00';
                                    }else{
                                        echo number_format($adhesionRVJ,2,","," ");
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>T.V.A:</td>
                            <td class="text-right align-middle"><?php echo number_format(($TVA/100),2,",",""); ?></td>
                        </tr>
                        <tr>
                            <td>Total TTC:</td>
                            <td class="text-right align-middle"><?php echo number_format(($totalTTC/100),2,",",""); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php
    }
?>