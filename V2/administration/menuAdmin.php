
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

<div class="col-12 d-flex justify-content-between align-items-center py-2 border-bottom border-primary bg-info">
  <div class="col-6">
    <a href="/" class="btn btn-info p-0"><i class="fas fa-exchange-alt"> Retour au site</i></a>
    <a class="ml-1 btn btn-danger " href="/admin-logout/"><i class="fas fa-power-off text-white"></i></a>
  </div>
  <div class="col-6 text-right">
    <?php echo "Bienvenue ".$_SESSION['pseudo']; ?>
  </div>
</div>


<ul class="nav justify-content-center border-bottom border-primary bg-vos">
  <li class="nav-item">
    <a class="nav-link"  href="https://mail.ionos.fr/" target="_blank"><i class="fas fa-envelope"> Les 2 messageries</i></a>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">COMMERCE</a>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="/admin/accueil/"><i class="fas fa-puzzle-piece"> Accueil / Demandes</i> (<?php echo $countDemandes;?>)</a>
      <a class="dropdown-item" href="/admin/commande/accueil/"><i class="fas fa-sort-amount-down-alt"> Commandes</i> (<?php echo $countCommandesAenvoyer + $countCommandesApreparer;?>)</a>
      <a class="dropdown-item text-center" href="/admin/recherche-document/"><i class="fas fa-file-alt"> Recherche de documents</i><br/><small>(saisir un paiement manuel)</small></a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item" href="/admin/client/liste/"><i class="fas fa-book"> Fichier client</i></a>
      <a class="dropdown-item" href="/admin/paiement/PAYPLUG/"><i class="fas fa-credit-card"> Etat d'un paiement</i></a>
      <a class="dropdown-item" href="/admin/statistiques/"><i class="fas fa-chart-line"> Stats</i></a>
      <a class="dropdown-item" href="/admin/comptabilite/"><i class="fas fa-money-bill-wave"> Comptabilité</i></a>
    </div>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">JEUX</a>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="/admin/jeu/demandes/pieces/"><i class="fas fa-thermometer-three-quarters"> Les 20 plus demandés (pièces)</i></a>
      <a class="dropdown-item" href="/admin/jeu/demandes/occasion/"><i class="fas fa-thermometer-three-quarters"> Les 20 plus demandés (occasions)</i></a>
      <a class="dropdown-item" href="/admin/jeu/new/"><i class="fas fa-plus"> Nouveau jeu</i></a>
      <a class="dropdown-item" href="/admin/jeu/catalogue/complet/"><i class="fas fa-square-full"> Les jeux complets</i></a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item" href="/admin/jeu/catalogue/general/"><i class="fas fa-database"> Catalogue général</i></a>
    </div>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">CATEGORIES / ACC</a>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="/admin/categories/"><i class="fas fa-plus"> Gestion des catégories</i></a>
        <a class="dropdown-item" href="/admin/accessoires/"><i class="fas fa-plus"> Gestion des accessoires</i></a>
      </div>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">DIVERS</a>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="/admin/bouteille-a-la-mer/"><i class="fas fa-wine-bottle"> Bouteilles à la mer</i></a>
        <a class="dropdown-item" href="/admin/demande-de-don/"><i class="fas fa-hand-holding-heart"> Les demandes de don</i></a>
        <a class="dropdown-item" href="/admin/partenaires/"><i class="fas fa-handshake"> Gestion des partenaires</i></a>
        <a class="dropdown-item" href="/admin/config/parametres-du-site/"><i class="fas fa-laptop-code"> Configuration</i></a>
      </div>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="/admin/sitemap/"><i class="fas fa-globe-europe"> Sitemap</i></a>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">RENE</a>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="/admin/test/"><i class="fas fa-vial"> Liste Gr</i></a></i>
      </div>
  </li>

</ul>