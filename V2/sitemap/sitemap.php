<?php

require("../config.php");
require("../bdd/connexion-bdd.php");
require("../controles/fonctions/cleanUrl.php");

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
	$hostname = "https";
} else {
	$hostname = "http";
}

// Ajoutez // à l'URL.
$hostname .= "://";
// Ajoutez l'hôte (nom de domaine, ip) à l'URL.
$hostname .= $_SERVER['HTTP_HOST'];

//tableau vide on on stock les urls
$urls = [];

//liste des urls directes à completer
$urls[] = [
	'loc'        => "/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/mentions-legales/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/conditions-generales-de-vente/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/projet/qui-sommes-nous/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/comment-ca-marche/tarifs/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/contact/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/carte-des-partenaires/belgique/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/carte-des-partenaires/france/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/catalogue-pieces-detachees/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/catalogue-jeux-occasion/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/don-de-jeux/partenaires/france/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/don-de-jeux/partenaires/belgique/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/nous-soutenir/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/connexion/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];
$urls[] = [
	'loc'        => "/inscription/",
	'changefreq' => "monthly", //monthly,daily
	'priority'   => 0.8
];

$sqlBoitesPiecesDetachees = $bdd->query("SELECT * FROM catalogue WHERE actif = 1 ORDER BY nom");
$boitesPiecesDetachees = $sqlBoitesPiecesDetachees->fetchAll();

foreach($boitesPiecesDetachees as $boite){
	$urlEditeurCatalogue = clean_url($boite['editeur']);
	$urlJeuCatalogue = clean_url($boite['urlNom']);
	$urls[] = [
		'loc'        => "/catalogue-pieces-detachees/".$urlEditeurCatalogue."/".$boite['idCatalogue']."/".$urlJeuCatalogue."/",
		'changefreq' => "monthly", //monthly,daily
		'priority'   => 0.8
	];
}

$querySqlJeuOccasion = ("SELECT * FROM jeux_complets JOIN catalogue ON catalogue.idCatalogue = jeux_complets.idCatalogue WHERE jeux_complets.stock > 0 AND jeux_complets.actif = 1");
$sqlBoitesJeuOccasion = $bdd->query($querySqlJeuOccasion);
$boitesJeuOccasion = $sqlBoitesJeuOccasion->fetchAll();

foreach($boitesJeuOccasion as $boite){
	$urlEditeurCatalogue = clean_url($boite['editeur']);
	$urlJeuCatalogue = clean_url($boite['urlNom']);
	$urls[] = [
		'loc'        => "/jeu-occasion/".$boite['idJeuxComplet']."-".$boite['idCatalogue']."/".$urlEditeurCatalogue."/".$urlJeuCatalogue."/",
		'changefreq' => "monthly", //monthly,daily
		'priority'   => 0.8
	];
}

header("Content-Type: text/xml;charset=utf-8");
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xhtml="http://www.w3.org/1999/xhtml">
	<?php
	foreach($urls as $url) {
		echo '<url>
					<loc>'.$hostname.$url['loc'].'</loc>
					<lastmod>'.date("Y-m-d", time()).'</lastmod>
					<changefreq>'.$url['changefreq'].'</changefreq>
					<priority>'.$url['priority'].'</priority>
				</url>';
	}
	?>
</urlset>