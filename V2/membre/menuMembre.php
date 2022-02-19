<div class="row mt-5">
    <div class="col-11 mx-auto h6 text-center border-bottom border-primary pb-1">
        <span class="<?php if(!isset($_SESSION['animationMenuMembreBonjour'])){echo 'menuMembreBonjour'; $_SESSION['animationMenuMembreBonjour'] = true;} ?>">Bonjour <?php echo $_SESSION['pseudo'];?>,</span>
    </div>
</div>

<div class="row py-3 menu-membre">
    <div class="col-12">
        <ul class="nav justify-content-center">
            <li class="nav-item border-bottom mx-1 <?php if(preg_match('#/membre/dashboard/#',$_SERVER['REQUEST_URI'])){echo "li-active";}?>">
                <a class="nav-link" href="/membre/dashboard/">Tableau de bord</a>
            </li>
            <li class="nav-item border-bottom mx-1 <?php if(preg_match('#/membre/historique/#',$_SERVER['REQUEST_URI'])){echo "li-active";}?>">
                <a class="nav-link" href="/membre/historique/">Historique</a>
            </li>
            <li class="nav-item border-bottom mx-1 <?php if(preg_match('#/membre/adresses/#',$_SERVER['REQUEST_URI'])){echo "li-active";}?>">
                <a class="nav-link" href="/membre/adresses/">Mes adresses</a>
            </li>
            <li class="nav-item border-bottom mx-1 <?php if(preg_match('#/membre/mon-compte/#',$_SERVER['REQUEST_URI'])){echo "li-active";}?>">
                <a class="nav-link" href="/membre/mon-compte/">Mon compte</a>
            </li>
        </ul>
    </div>
</div>