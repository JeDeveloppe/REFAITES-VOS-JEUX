<?php
@session_start ();
include_once("../config.php");
$titreDeLaPage = "On en parle dans le livre d'or | ".$GLOBALS['titreDePage'];
$descriptionPage = "Le livre d'or est là pour laisser des messages sur le service !";
include_once("../commun/haut_de_page.php");
include_once("../commun/alertMessage.php");
include_once("../bdd/connexion-bdd.php");

$sqlLivre = $bdd-> prepare("SELECT * FROM livreOr WHERE actif = ? ORDER BY time DESC");
$sqlLivre-> execute(array(1));
$countLivreOr = $sqlLivre -> rowCount();

require("./pagination-livreOr.php");
?>
<div class="container-fluid mt-5 d-none">
    <div class="col-12 text-center mb-4"><h1>Livre d'or</h1></div>

    <div class="col-12 text-center my-5">Encore en construction...</div>

    <div class="col-12 text-center my-5">Revenez dans quelque temps...</div>

    <p class="col-12 text-center my-5">
        <a href="/on-en-parle/livre-d-or/" class="btn btn-info bg-refaites my-2">Aller au catalogue</a>
    </p>
</div>
<div class="container-fluid mt-5">
    <div class="col-12 text-center mb-4"><h1>Livre d'or</h1></div>

    <div class="row">
        <!-- les messages -->
        <div class="col-md-6">
            <?php
            //si y a pas de message
            if($countLivreOr < 1){
                echo '<div class="col-11 my-2 mx-auto">
                            <div class="col-11 text-center mx-auto"><i class="fas fa-smile-beam fa-2x text-warning"></i> Les messages vont arrivés...</div>
                        </div>';
            }else{
                while($donneesLivre){
                    //pour chaque message utilisateur on regarde si admin a mis en remerciement
                    $sqlMessageLivre = $bdd -> prepare("SELECT * FROM livreOr_messages WHERE idLivre = ?");
                    $sqlMessageLivre-> execute(array($donneesLivre['idLivre']));
                    $donneesMessageLivre = $sqlMessageLivre-> fetch();
                    $count = $sqlMessageLivre -> rowCount();
                    if($count == 1){
                        $messageAdmin = '<div class="jumbotron bg-vos mt-4 mb-0 p-3 bg-light mb-0">'.$donneesMessageLivre['message'].'</div>';
                    }else{
                        $messageAdmin = "";
                    }
                    echo '<div class="col-11 col-sm-8 col-md-11 col-lg-9 col-xl-8 my-2 mx-auto" >
                            <div class="jumbotron py-2 h-100">
                                <div class="col-12 h5 mt-2">Message de '.$donneesLivre['pseudo'].'</div>
                                <div class="col-12 text-right small">...le '.date("d.m.Y",$donneesLivre['time']).' à '.date("h:i",$donneesLivre['time']).'</div>
                                <div class="col-12 mt-3">'.$donneesLivre['content'].'</div>
                                '.$messageAdmin.'          
                            </div>
                        </div>';
                $donneesLivre = $sqlLivrePagination-> fetch();
                }
            }
            ?>

<?php if($nombreDePages > 1){?>

                <div class="col-12 mt-4">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center">
                                <?php 
                                $variation = 0;
                                $milieu = ceil($nombreDePages/2);
                                if($variation < $milieu){
                                    $variation = $variation;
                                }else{
                                    $variation = $milieu;
                                }

                                if($pageActuelle == 1){
                                echo '<li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-fast-backward"></i></a></li>';
                                echo '<li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-step-backward"></i></a></li>';
                                    for($i=1;$i<=$pageActuelle+$variation;$i++){
                                        if($pageActuelle == $i){
                                            $active = " active";
                                        }else{
                                            $active = "";
                                        }
                                        echo '<li class="page-item'.$active.'"><a class="page-link" href="/on-en-parle/livre-d-or/?page='.$i.'">'.$i.'</a></li>';
                                    }
                                    $pageSuivante = $pageActuelle+1;
                                    echo '<li rel="next" class="page-item"><a class="page-link" href="/on-en-parle/livre-d-or/?page='.$pageSuivante.'"><i class="fas fa-step-forward"></i></a></li>';
                                    echo '<li rel="next" class="page-item"><a class="page-link" href="/on-en-parle/livre-d-or/?page='.$nombreDePages.'"><i class="fas fa-fast-forward"></i></a></li>';
                                }
                                
                                if($pageActuelle > 1 && $pageActuelle < $nombreDePages - $variation){
                                    $pageAvant = $pageActuelle-1;
                                    echo '<li rel="prev" class="page-item"><a class="page-link" href="/on-en-parle/livre-d-or/?page=1"><i class="fas fa-fast-backward"></i></a></li>';
                                    echo '<li rel="prev" class="page-item"><a class="page-link" href="/on-en-parle/livre-d-or/?page='.$pageAvant.'"><i class="fas fa-step-backward"></i></a></li>';
                                        for($i=$pageActuelle-1;$i<=$pageActuelle+$variation;$i++){
                                            if($pageActuelle == $i){
                                                $active = " active";
                                            }else{
                                                $active = "";
                                            }
                                            echo '<li class="page-item'.$active.'"><a class="page-link" href="/on-en-parle/livre-d-or/?page='.$i.'">'.$i.'</a></li>';
                                        }
                                    $pageSuivante = $pageActuelle+1;
                                    echo '<li rel="next" class="page-item"><a class="page-link" href="/on-en-parle/livre-d-or/?page='.$pageSuivante.'"><i class="fas fa-step-forward"></i></a></li>';
                                    echo '<li rel="next" class="page-item"><a class="page-link" href="/on-en-parle/livre-d-or/?page='.$nombreDePages.'"><i class="fas fa-fast-forward"></i></a></li>';
                                }

                                if($pageActuelle >= $nombreDePages - $variation && $pageActuelle < $nombreDePages+1){
                                    if($pageActuelle < $nombreDePages - $variation+1){
                                        $back = 1;
                                    }elseif($pageActuelle < $nombreDePages - $variation+2){
                                        $back = 2;
                                    }elseif($pageActuelle < $nombreDePages - $variation+3){
                                        $back = 3;
                                    }elseif($pageActuelle < $nombreDePages - $variation+4){
                                        $back = 4;
                                    }
                                    $pageAvant = $pageActuelle-1;
                                    echo '<li rel="prev" class="page-item"><a class="page-link" href="/on-en-parle/livre-d-or/?page=1"><i class="fas fa-fast-backward"></i></a></li>';
                                    echo '<li rel="prev" class="page-item"><a class="page-link" href="/on-en-parle/livre-d-or/?page='.$pageAvant.'"><i class="fas fa-step-backward"></i></a></li>';
                                        for($i=$pageActuelle-$back;$i<=$nombreDePages;$i++){
                                            if($pageActuelle == $i){
                                                $active = " active";
                                            }else{
                                                $active = "";
                                            }
                                            echo '<li class="page-item'.$active.'"><a class="page-link" href="/on-en-parle/livre-d-or/?page='.$i.'">'.$i.'</a></li>';
                                        }
                                    if($pageActuelle < $nombreDePages){
                                        $pageSuivante = $pageActuelle+1;
                                        echo '<li rel="next" class="page-item"><a class="page-link" href="/on-en-parle/livre-d-or/?page='.$pageSuivante.'"><i class="fas fa-step-forward"></i></a></li>';
                                        echo '<li rel="next" class="page-item"><a class="page-link" href="/on-en-parle/livre-d-or/?page='.$nombreDePages.'"><i class="fas fa-fast-forward"></i></a></li>';
                                    }else{
                                        echo '<li rel="next" class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-step-forward"></i></a></li>';
                                        echo '<li rel="next" class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-fast-forward"></i></a></li>';
                                    }
                                }             
                                ?>
                        </ul>
                    </nav>
                </div>
                <div class="col-12 text-center">Total des pages: <?php echo $nombreDePages; ?></div>

            

        <?php } ?>




        </div>
        <!-- formulaire -->
        <div class="col-md-6">
            <hr class="col-11 mx-auto d-md-none">
            <form method="post" action="/on-en-parle/ctrl/ctrl-livreOr.php" class="sticky pt-5 col-12 col-xl-10 mx-auto">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">Pseudo:</span>
                    </div>
                    <input type="text" class="form-control" name="pseudo" placeholder="Pseudo" pattern="[a-zA-ZÀ-ÿ]{3,30}" maxlength="30" value="<?php if(isset($_SESSION['pseudo'])){echo $_SESSION['pseudo'];}?>" required>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">@</span>
                    </div>
                    <input type="email" class="form-control" name="email" placeholder="Email valide" value="<?php if(isset($_SESSION['email'])){echo $_SESSION['email'];}?>" required>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Votre message:</span>
                    </div>
                    <textarea class="form-control" name="content" placeholder="Entre 15 et 300 caractères..." pattern="[a-zA-ZÀ-ÿ ]{15,300}" maxlength="300" required><?php if(isset($_SESSION['content'])){echo $_SESSION['content'];}?></textarea>
                </div>
                <div class="col-12 text-center my-3">
                    <input id="lu" type="checkbox" name="conditionBienOk" value="lu" required> <label for="lu">J'ai lu et j'accepte les</label> <a href="/mentions-legales/#cgu">conditions générales d'utilisation</a>.<sup>(1)</sup>
                </div> 
                <div class="col-12 text-center">
                    <input type="hidden" id="recaptchaResponse" name="recaptcha-response">
                    <button class="btn btn-primary mt-2">Envoyer</button>
                </div>
                <div class="col-12 text-danger text-right mt-3 mt-sm-0">
                    <sup>(1)</sup> Obligatoire.
                </div>
            </form>
        </div>
    </div>


</div>
<?php
require_once("../captcha/captchaGoogle.php");
include_once("../commun/bas_de_page.php");
?>
