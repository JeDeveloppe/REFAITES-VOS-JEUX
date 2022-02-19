
<?php
//LES DEMANDES DE DEVIS
$sqlDemandes = $bdd -> prepare("SELECT * FROM listeMessages WHERE statut = ? GROUP BY idUser ORDER BY time ASC");
$sqlDemandes-> execute(array(1));
$countDemandes = $sqlDemandes -> rowCount();

//LES COMMANDES A ENVOYER
$sqlCommandesAenvoyer = $bdd -> prepare("SELECT * FROM documents WHERE etat = ? AND expedition NOT LIKE ? AND envoyer = ? ORDER BY time_transaction"); //etat 2 = payer
$sqlCommandesAenvoyer-> execute(array(2,"%retrait%",0));
$countCommandesAenvoyer = $sqlCommandesAenvoyer -> rowCount();
//LES COMMANDES AVEC RETRAIT
$sqlCommandesApreparer = $bdd -> prepare("SELECT * FROM documents WHERE etat = ? AND expedition LIKE ? AND envoyer = ? ORDER BY time_transaction"); //etat 2 = payer
$sqlCommandesApreparer-> execute(array(2,"%retrait%",0));
$countCommandesApreparer = $sqlCommandesApreparer -> rowCount();

?>

<div class="col-12 mt-4 pl-3">
  <?php echo "Bienvenue ".$_SESSION['pseudo']; ?>
  <hr class="border border-primary">
</div>
<div class="col-12 text-center">
  <a href="/" class="btn btn-info p-0"><i class="fas fa-exchange-alt"> Retour au site</i></a>
  <a class="ml-1 btn btn-danger p-1" href="/admin-logout/"><i class="fas fa-power-off text-white"></i></a>
</div>

<nav class="navbar navbar-expand-lg navbar-light border-0" >
  <a href="/accueil/" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Accueil du site"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse h6 flex-column" id="navbarColor02">
      <ul class="nav flex-column list-unstyled mt-3 col-12 p-0">
        IMPORTANT
          <li class="nav-item ml-3 py-1"><a href="https://mail.ionos.fr/" target="_blank"><i class="fas fa-envelope"> Les 2 messageries</i></a></li>
      </ul>
      <ul class="nav flex-column list-unstyled mt-3 col-12 p-0">
        COMMERCE
          <li class="nav-item ml-3 py-1"><a href="/admin/accueil/"><i class="fas fa-puzzle-piece"> Accueil / Demandes</i></a> (<?php echo $countDemandes;?>)</li>
          <li class="nav-item ml-3 py-1"><a href="/admin/commande/accueil/"><i class="fas fa-sort-amount-down-alt"> Commandes</i></a> (<?php echo $countCommandesAenvoyer + $countCommandesApreparer;?>)</li>
          <li class="nav-item ml-3 py-1"><a href="/admin/recherche-document/"><i class="fas fa-file-alt"> Recherche de documents</i></a><li>
          <li class="nav-item ml-3 py-1"><a href="/admin/paiements-en-cours/"><i class="fas fa-exclamation-triangle"> Paiement en cours...</i></a><li>
          <li class="nav-item ml-3 py-1"><a href="/admin/commande/en-attente/"><i class="fas fa-exclamation-triangle"> Messages en attente...</i></a><li>
          
      </ul>
      <ul class="nav flex-column list-unstyled mt-3 col-12 p-0">
        PAIEMENTS
          <li class="nav-item ml-3 py-1"><a href="/admin/paiement/PAYPLUG/"><i class="fas fa-credit-card"> Etat d'un paiement</i></a></li>
      </ul>
      <ul class="nav flex-column list-unstyled mt-3 col-12 p-0">
        CLIENTS
          <li class="nav-item ml-3 py-1"><a href="/admin/client/"><i class="fas fa-couch"> Recherche / mise à jour</i></a></li>
          <li class="nav-item ml-3 py-1"><a href="/admin/client/liste/"><i class="fas fa-book"> Fichier client</i></a></li>
      </ul>
      <ul class="nav flex-column list-unstyled mt-3 col-12 p-0">
        ON EN PARLE / MEDIAS / PARTENAIRES
          <li class="nav-item ml-3 py-1"><a href="/admin/livre-or/"><i class="fas fa-book"> Livre d'or</a></i></li>
          <li class="nav-item ml-3 py-1"><a href="/admin/bouteille-a-la-mer/"><i class="fas fa-wine-bottle"> Bouteilles à la mer</i></a></li>
          <li class="nav-item ml-3 py-1"><a href="/admin/demande-de-don/"><i class="fas fa-hand-holding-heart"> Les demandes de don</i></a></li>
          <li class="nav-item ml-3 py-1"><a href="/admin/medias/new/"><i class="fas fa-rss"> Nouveau média</i></a></li>
          <li class="nav-item ml-3 py-1"><a href="/admin/partenaires/new/"><i class="fas fa-handshake"> Nouveau partenaire</i></a></li>
      </ul>
      <ul class="nav flex-column list-unstyled mt-3 col-12 p-0">
        JEUX
          <li class="nav-item ml-3 py-1"><a href="/admin/jeu/demandes/"><i class="fas fa-thermometer-three-quarters"> Les 10 plus demandés</i></a></li>
          <li class="nav-item ml-3 py-1"><a href="/admin/jeu/new/"><i class="fas fa-plus"> Nouveau jeu</i></a></li>
          <li class="nav-item ml-3 py-1"><a href="/admin/jeu/sans-ventes/"><i class="fab fa-creative-commons-zero text-danger"> Sans vente</i></a></li>
          <li class="nav-item ml-3 py-1"><a href="/admin/jeu/catalogue/general/"><i class="fas fa-database"> Catalogue général</i></a></li>
          <li class="nav-item ml-3 py-1"><a href="/admin/jeu/catalogue/complet/"><i class="fas fa-square-full"> Les jeux complets</i></a></li>
      </ul>
      <ul class="nav flex-column list-unstyled mt-3 col-12 p-0">
        CATEGORIES / ACCESSOIRES
          <li class="nav-item ml-3 py-1"><a href="/admin/categories/"><i class="fas fa-plus"> Gestion des catégories</i></a></li>
          <li class="nav-item ml-3 py-1"><a href="/admin/accessoires/"><i class="fas fa-plus"> Gestion des accessoires</i></a></li>
      </ul>
      <ul class="nav flex-column list-unstyled mt-3 col-12 p-0">
        STATS / CONFIG
          <li class="nav-item ml-3 py-1"><a href="/admin/config/statistiques/"><i class="fas fa-chart-line"> Stats</i></a></li>
          <li class="nav-item ml-3 py-1"><a href="/admin/config/parametres-du-site/"><i class="fas fa-laptop-code"> Configuration</i></a></li>
      </ul>
      <ul class="nav flex-column list-unstyled mt-3 col-12 p-0">
        COMPTABILITE
          <li class="nav-item ml-3 py-1"><a href="/admin/comptabilite/"><i class="fas fa-money-bill-wave"> Liste des factures</i></a></li>
      </ul>
      <ul class="nav flex-column list-unstyled mt-3 col-12 p-0">
        SEO
          <li class="nav-item ml-3 py-1"><a href="/admin/sitemap/"><i class="fas fa-globe-europe"> Sitemap</i></a></li>
      </ul>
      <ul class="nav flex-column list-unstyled mt-5 col-12 p-0">
        TEST (René)
          <li class="nav-item ml-3 py-1"><a href="/admin/test/"><i class="fas fa-vial"> Liste Gr</i></a></li>
      </ul>
    </div>
</nav>