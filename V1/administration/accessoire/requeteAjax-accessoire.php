<?php
@session_start ();
require("../../config.php");
require("../../bdd/connexion-bdd.php");

if(isset($_POST['categorie'])){

    $categorie = $_POST['categorie'];
    $sql = $bdd-> prepare("SELECT * FROM catalogue WHERE accessoire_idCategorie = ? ORDER BY nom ASC");
    $sql->execute(array($categorie));
    $donnee = $sql->fetchAll();
?>

<table class="table text-center mt-4">
    <thead>
        <th>Image</th>
        <th>Nom</th>
        <th>Description</th>
        <th>En ligne</th>
        <th>Action</th>
    </thead>
    <tbody>
    <?php
    foreach($donnee as $accessoireLigne){
        $sqlImage = $bdd -> query("SELECT * FROM jeu_image WHERE idJeux = ".$accessoireLigne['idCatalogue']);
        $donneesImage = $sqlImage->fetch();
    ?>
        <tr>
            <td>
                <div class="divImgPresentationExempleAdmin bg-light">
                    <?php echo '<img src="data:image/jpeg;base64,'.$donneesImage['image'].'"/>'; ?>
                </div>
            </td>
            <td>
                <?php echo $accessoireLigne['nom']; ?>
            </td>
            <td>
                <?php 
                $sqlDescriptionAccessoire = $bdd-> query("SELECT * FROM pieces WHERE idJeu =".$accessoireLigne['idCatalogue']);
                $donneesDescriptionAccessoire = $sqlDescriptionAccessoire->fetch();
                echo $donneesDescriptionAccessoire['contenu_total']; ?>
            </td>
            <td>
                <?php
                    if($accessoireLigne['actif'] == 0){
                        echo '<i class="fas fa-circle text-danger"></i>';
                    }else{
                        echo '<i class="fas fa-circle text-success"></i>';
                    }
                ?>
            </td>
            <td>
                <a href="/admin/accessoire/<?php echo $accessoireLigne['idCatalogue'];?>/edition/" class="btn btn-info">Modifier</a>
            </td>
        </tr>
    <?php
    }
    ?>
</table>

<?php
}
?>