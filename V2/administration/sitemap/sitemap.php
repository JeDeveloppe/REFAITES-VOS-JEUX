<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");
include('../../config.php');
include('../../bdd/connexion-bdd.php');
$titreDeLaPage = "[ADMIN] - Gestion du sitemap";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");
include_once("../../commun/alertMessage.php");

if(isset($_GET['tri']) && preg_match('#url|vues#',$_GET['tri'])){
    require("../../controles/fonctions/validation_donnees.php");
    $tri = valid_donnees($_GET['tri']);
        if($tri == "vues"){
            $ordreTri = "DESC";
        }else{
            $ordreTri = "ASC";
        }
}else{
    $tri = "idJeu, url";
    $ordreTri = "ASC";
}

$sqlSitemapRecherche = $bdd->prepare("SELECT * FROM sitemaps ORDER BY $tri $ordreTri");
$sqlSitemapRecherche->execute(array($url));
$donneesSitemapRecherche = $sqlSitemapRecherche->fetch();
$count = $sqlSitemapRecherche-> rowCount();
?>
<div class="container-fluid mt-5">
    <h1 class="col-12 text-center">Sitemap <?php echo $count; ?> urls <a href="/sitemap.xml" target="_blank" class="small">Voir</a></h1>
    <table class="table table-sm col-12">
        <thead class="text-center">
            <th>#</th>
            <th>Url <a href="/admin/sitemap/?tri=url"><i class="fas fa-caret-square-down"></i></a></th>
            <th>Vues <a href="/admin/sitemap/?tri=vues"><i class="fas fa-caret-square-down"></i></a></th>
            <th>Actif</th>
            <th>Action</th>
        </thead>
        <tbody>
            <?php
                $rangee = 1;
                while($donneesSitemapRecherche){
                    $rangeePrecedante = $rangee-1;
                    if($donneesSitemapRecherche['actif'] == 1){
                        $actif = '<i class="fas fa-circle text-success"></i>';
                    }else{
                        $actif = '<i class="fas fa-circle text-danger"></i>';
                    }
                    echo '<tr>
                        <td class="text-center" id='.$rangee.'>'.$rangee.'</td>
                        <td>'.$donneesSitemapRecherche['url'].'</td>
                        <td class="text-center">'.$donneesSitemapRecherche['vues'].'</td>
                        <td class="text-center">'.$actif.'</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="/administration/sitemap/ctrl/ctrl-delete.php?id='.$donneesSitemapRecherche['idSitemaps'].'&rangee='.$rangeePrecedante.'" class="btn btn-danger">Supprimer</a>';

                                if($donneesSitemapRecherche['idJeu'] != 0){
                                    echo '<a href="/admin/jeu/'.$donneesSitemapRecherche['idJeu'].'/edition/" target="_blank" class="btn btn-info">Voir</a>';
                                }else{
                                    echo '<a href="'.$donneesSitemapRecherche['url'].'" target="_blank" class="btn btn-info">Voir</a>';
                                }
                            echo '
                            </div>
                        </td>
                        </tr>';
                $rangee++;
                $donneesSitemapRecherche = $sqlSitemapRecherche->fetch();
                }
                ?>
        </tbody>

    </table>
</div>
<?php include_once("../../commun/bas_de_page-admin.php");?>