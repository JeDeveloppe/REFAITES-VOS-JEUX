<?php
@session_start ();
require("../../controles/fonctions/adminOnline.php");

include('../../config.php');

include('../../bdd/connexion-bdd.php');

$titreDeLaPage = "[ADMIN] - Livre d'or";
$descriptionPage = "";
include_once("../../commun/haut_de_page.php");

include_once("../../commun/alertMessage.php");

$sqlLivreOr = $bdd -> prepare("SELECT * FROM livreOr WHERE actif = ?"); 
$sqlLivreOr-> execute(array(0)); //0 = MESSAGE PAS EN LIGNE
$donneesLivreOr = $sqlLivreOr->fetch();
$countLivreOr = $sqlLivreOr -> rowCount();
?>

<div class="container-fluid mt-4">
    <div class="col h2 text-center">MESSAGES HORS LIGNE DU LIVRE D'OR </div>
        <div class="row d-flex justify-content-around p-2">
            <div class="col-11 mx-auto">
                <?php
                //si y a plus rien dans la table on retourne au catalogue
                if($countLivreOr < 1){
                    echo '<div class="card col mx-auto p-0">
                                <div class="card-header bg-dark text-white">Messages du livre...</div>
                                <div class="card-body text-center align-middle"><i class="fas fa-angry text-danger"></i> Aucuns à traiter !</div>
                            </div>';
                }else{
                ?>
                    <div class="row mt-5 d-flex flex-wrap justify-content-start">
                        <?php
                        while($donneesLivreOr){
                            $sqlMessageLivre = $bdd -> prepare("SELECT * FROM livreOr_messages WHERE idLivre = ?");
                            $sqlMessageLivre-> execute(array($donneesLivreOr['idLivre']));
                            $donneesMessageLivre = $sqlMessageLivre-> fetch();
                            $count = $sqlMessageLivre -> rowCount();
                            if($count == 1){
                                $message = $donneesMessageLivre['message'];
                            }else{
                                $message = "";
                            }
                            echo '<div class="col-11 my-2 col-sm-6 col-md-4 mx-auto mx-sm-0" >
                                    <div class="jumbotron py-2 bg-secondary text-white h-100">
                                        <div class="col-12 h5 mt-2">Message de '.$donneesLivreOr['pseudo'].'</div>
                                        <div class="col-12 text-right small">...le '.date("d.m.Y",$donneesLivreOr['time']).' à '.date("h:i:s",$donneesLivreOr['time']).'</div>
                                        <div class="col-12 mt-3">'.$donneesLivreOr['content'].'</div>
                                        <div class="col-12 mt-3">
                                        <hr>
                                            <form method="get" action="/administration/livreOr/ctrl/ctrl-livreOr-online_offline.php">
                                                <textarea class="col-12" name="message" row="3" placeholder="Non obligatoire..." maxlenght="200">'.$message.'</textarea>
                                                <input type="hidden" name="idLivre" value="'.$donneesLivreOr['idLivre'].'">
                                                <input type="hidden" name="newValue" value="1">
                                                <div class="btn-group mt-2 col-11 mx-auto text-center">
                                                    <button class="btn btn-primary">Poster en ligne</button>
                                                    <a href="/administration/livreOr/ctrl/ctrl-livreOr-online_offline.php?newValue=0&idLivre='.$donneesLivreOr['idLivre'].'" class="btn btn-danger border-primary">Supprimer</a>
                                                </div>
                                            </form>
                                                
                                        </div>     
                                    </div>
                                </div>';
                        $donneesLivreOr = $sqlLivreOr-> fetch();
                        }
                        ?>
                    </div>
                <?php
                }//fin du if count <1
                ?>
            </div>
        </div>
</div>

<?php include_once("../../commun/bas_de_page-admin.php");?>