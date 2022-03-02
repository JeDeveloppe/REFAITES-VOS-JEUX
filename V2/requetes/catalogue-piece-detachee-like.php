<?php
session_start();

if(isset($_GET['recherche'])){

    require_once("../config.php");
    require_once("../bdd/connexion-bdd.php");
    require_once("../controles/fonctions/validation_donnees.php");
    require_once("../controles/fonctions/cleanUrl.php");
    
    $recherche = valid_donnees(trim($_GET['recherche']));
    

    $req = $bdd-> prepare("SELECT * FROM catalogue WHERE nom LIKE ? AND accessoire_idCategorie = 0 AND actif = 1");
    $req-> execute(array("%".strtoupper(str_replace(" ","%",$recherche))."%"));
    $donnees = $req->fetch();
    $nbrDonnees = $req->rowCount();

    

    if($nbrDonnees > 0){
        echo '<div class="row px-xl-9 justify-content-start">';
            while($donnees){
                $urlEditeurCatalogue = clean_url($donnees['editeur']);
                $urlJeuCatalogue = clean_url($donnees['nom']);
     
                //url du jeu propre
                if($donnees['urlNom'] == ""){
                    $sqlUpdateUrlNom = $bdd -> prepare("UPDATE catalogue SET urlNom = ? WHERE idCatalogue = ?");
                    $sqlUpdateUrlNom-> execute(array($urlJeuCatalogue,$donnees['idCatalogue']));
                }

                //affichage pièces vendu ou pas de ce jeu
                $sqlVendu = $bdd -> prepare("SELECT * FROM documents_lignes WHERE idJeu = ?");
                $sqlVendu-> execute(array($donnees['idCatalogue']));
                $countVendu = $sqlVendu-> rowCount();
                if($countVendu > 0){
                    $iconeVenduAdmin = '<i class="fas fa-money-bill-alt text-success" data-html="true" data-toggle="tooltip" data-placement="right" title="DEMANDE FAITE"> ('.$countVendu.') </i>';
                }else{
                    $iconeVenduAdmin = '<i class="fas fa-search-dollar text-danger" data-html="true" data-toggle="tooltip" data-placement="right" title="PAS DE DEMANDE"></i>';
                }
                echo '
                        <div class="col-9 col-sm-5 col-md-4 col-lg-3 p-2 mx-auto mx-md-0 animated fadeInRight" id="'.$donnees['idCatalogue'].'">
                            <div class="col-12 p-2 border shadow scale-hover">   
                                <div class="col-12 p-0"> 
                                    <div class="row">
                                        <div class="col-12 mt-2">
                                            <div class="divImgCatalogue"><img src="data:image/jpeg;base64,'.$donnees['imageBlob'].'" alt="Boite du jeu '.$donnees['nom'].' par '.$donnees['editeur'].'" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2 p-2">';
                                            if(strlen($donnees['nom']) > 17){
                                                echo '<div class="col-12 text-center" data-html="true" data-toggle="tooltip" data-placement="top" title="'.$donnees['nom'].'">'.substr(nl2br($donnees['nom']),0,17).'...</div>';
                                            }else{
                                                echo '<div class="col-12 text-center">'.$donnees['nom'].'</div>';
                                            }  
                                        echo '                             
                                        <div class="col-12 mt-2 text-center">'.$donnees['editeur'].'</div>
                                        <div class="col-12 text-center">';
                                            if($donnees['annee'] == "Année inconnue"){echo "&nbsp;";}else{echo $donnees['annee'];}
                                        echo '</div>
                                    </div>
                                </div>
                                <div class="row text-center mt-2">
                                        <div class="col-12 d-none d-sm-block">
                                            <a href="/catalogue-pieces-detachees/'.$urlEditeurCatalogue.'/'.$donnees['idCatalogue'].'/'.$donnees['urlNom'].'/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Faire une demande de pièces</span></a>
                                        </div>
                                        <div class="col-12 mx-auto d-sm-none">
                                            <a href="/catalogue-pieces-detachees/'.$urlEditeurCatalogue.'/'.$donnees['idCatalogue'].'/'.$donnees['urlNom'].'/" class="btn btn-primary border-0 bg-refaites p-1"><span class="p-1">Faire une demande...</span></a>
                                        </div>';
                                        
                                        if(isset($_SESSION['levelUser']) && $_SESSION['levelUser'] == 4){
                                            echo '<div class="col-12 mt-3 d-flex justify-content-around align-items-center">
                                                    <a href="/admin/jeu/'.$donnees['idCatalogue'].'/edition/"><i class="fas fa-cog fa-2x text-gray-dark ml-3" data-html="true" data-toggle="tooltip" data-placement="right" title="Créé par: '.$donnees['createur'].'<br/>le '.date ('d-m-Y à H:i:s', strtotime($donnees['created_at'])).'"></i></a> '.$iconeVenduAdmin.'
                                                </div>';
                                        }
                                
                                echo '</div>
                            </div>
                        </div>'; 
            $donnees = $req->fetch();
            }
        echo '</div>';
    }else{
        unset($_SESSION['recherchePieceDetachees']);
        $sqlPartenaires = $bdd->query("SELECT * FROM partenaires WHERE detachee = 1 ORDER BY RAND()");
        $donneesPartenaires = $sqlPartenaires->fetchAll();

        echo '<div class="row px-xl-9 justify-content-start">';
        echo '<div class="col-12 text-center">
            <p class="h2">Nous n\'avons pas ce jeu en stock pour le moment.</p>
            <p class="h5">Vous pouvez suivre nos arrivages sur la page Facebook !</p>    
            <p class="h5 mt-5">Ou tenter votre chance chez l\'un de nos partenaires:</p>
        </div>';
                foreach($donneesPartenaires as $partenaire){ 
                    echo '
                        <div class="col-7 col-sm-5 col-md-4 col-lg-3 p-1 mx-auto mx-md-0 animated fadeInRight">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="col-12">
                                        <div class="divImgCatalogue">';
                                            echo '<img src="data:image/jpeg;base64,'.$partenaire['image'].'" alt="Image du partenaire: '.$partenaire['nom'].'" />';
                                        echo '</div>
                                    </div>
                                    <div class="col-12 mt-2">'.$partenaire['nom'].'</div>
                                    <div class="col-12 mt-2"><a href="'.$partenaire['url'].'" target="blank" class="cursor-alias">Voir le site</a></div>
                                </div>
                            </div>
                        </div>';
                }
        echo '</div>';
    }
} 
?>