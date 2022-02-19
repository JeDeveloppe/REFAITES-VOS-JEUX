<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
$titreDeLaPage = "[ADMIN] - Gestion du sitemaps";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");

$sqlSitemapRecherche = $bdd->prepare("SELECT * FROM sitemaps ORDER BY url ASC");
$sqlSitemapRecherche->execute(array($url));
$donneesSitemapRecherche = $sqlSitemapRecherche->fetch();
$count = $sqlSitemapRecherche-> rowCount();
?>
<div class="container mt-5">
    <h1 class="">Gestion du sitemaps: <?php echo $count; ?> urls</h1>
    <table class="table table-sm col-11 mx-auto">
        <thead>
            <th>Url</th>
            <th>Action</th>
        </thead>
        <tbody>
            <?php
                while($donneesSitemapRecherche){
                    echo '<tr>
                        <td>'.$donneesSitemapRecherche['url'].'</td>
                        <td><a href="/administration/test/ctrl/ctrl-delete.php?id='.$donneesSitemapRecherche['idSitemaps'].'" class="btn btn-danger">Supprimer</a></td>
                        </tr>';
                $donneesSitemapRecherche = $sqlSitemapRecherche->fetch();
                }
                ?>
        </tbody>

    </table>
</div>
<?php include_once("../../commun/bas_de_page-admin.php");?>