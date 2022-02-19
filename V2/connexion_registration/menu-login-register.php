<div class="row mt-5 menu-connexion-inscription">
    <div class="col-12">
        <ul class="nav justify-content-center h6" id="myTab" role="tablist">
            <li class="nav-item border-bottom  mx-1 <?php if(preg_match('#/connexion/#',$_SERVER['REQUEST_URI'])){echo "li-active";}?>">
                <a class="nav-link" href="/connexion/" >Connexion</a>
            </li>
            <li class="nav-item border-bottom mx-1 <?php if(preg_match('#/inscription/#',$_SERVER['REQUEST_URI'])){echo "li-active";}?>">
                <a class="nav-link" href="/inscription/" >Inscription</a>
            </li>
        </ul>
    </div>
</div>
