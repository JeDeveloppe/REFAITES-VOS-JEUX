<div class="col-12 mt-5 h5 text-center text-danger animated faster fadeInRight">
    <i class="fas fa-rss"></i> Pensez à regrouper vos demandes de pièces détachées!
</div>
<div class="row overflow-auto">
    <div class="col-12 mt-4">
       <div class="col-12 border-bottom">Pièces détachées:</div>
            <table class="table table-sm table-striped mt-3 col-12">
                <thead class="thead-dark text-center">
                    <tr>
                        <th class="col-1">Action</th>
                        <th class="col-2">Image / Jeu</th>
                        <th class="col">Demande</th>
                        <th class="col-2">Image(s) d'exemple</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    while($donneesListeDemandes){

                        //on recupere tout de la boite de jeu
                        $sqlJeux = $bdd -> query("SELECT * FROM catalogue WHERE idCatalogue = ".$donneesListeDemandes['idJeu']);
                        $donneesJeux = $sqlJeux -> fetch();
                        //on recupere les images d'exemple s'il y en a
                        $sqlImageExemple = $bdd -> query("SELECT * FROM listeMessages_images WHERE idListeMessages = ".$donneesListeDemandes['idListeMessages']);
                        $countImageExemple = $sqlImageExemple->rowCount();

                        if($countImageExemple < 1){
                            $textImageExemple = '<div class="text-danger">Non fournie</div>';
                        }elseif($countImageExemple == 1){
                            $textImageExemple = '<div class="text-success">Une fournie</div>';
                        }else{
                            $textImageExemple = '<div class="text-success">2 fournies</div>';
                        }
                        ?>
                        <tr>
                        <td class="text-center align-middle">
                            <a href="/demande-devis/delete/<?php echo $donneesListeDemandes['idListeMessages'];?>/" class="btn btn-danger p-1"><i class="fas fa-trash-alt" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Supprimer de la liste"></i></a>
                        </td>
                        <td class="text-center align-middle">
                            <?php echo '<div class="divImgTableauListeMessage"><img src="data:image/jpeg;base64,'.$donneesJeux['imageBlob'].'"/></div>'; ?><br/><?php echo $donneesJeux['nom']."<br/>".$donneesJeux['editeur']."<br/>".$donneesJeux['annee']; ?>
                        </td>
                        
                        <td class="align-middle"><?php echo $donneesListeDemandes['message']; ?></td>
                        <td class="text-center align-middle"><?php echo $textImageExemple; ?></td>
                        </tr>
                    <?php
                    $donneesListeDemandes = $sqlListeDemandes->fetch();
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>