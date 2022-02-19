<?php
require("../config.php");
require('../bdd/connexion-bdd.php');

$sqlSitemap = $bdd->query("SELECT * FROM sitemaps WHERE actif = 1 ORDER BY url ASC");
$donneesSitemap = $sqlSitemap->fetch();

header("Content-Type: text/xml;charset=utf-8");
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xhtml="http://www.w3.org/1999/xhtml">
	<?php
		while($donneesSitemap){
			echo '<url>
					<loc>'.$donneesSitemap['url'].'</loc>
					<lastmod>'.date("Y-m-d",$donneesSitemap['time']).'</lastmod>
					<changefreq>daily</changefreq>
					<priority>0.8</priority>
				</url>';

		$donneesSitemap = $sqlSitemap->fetch();
		}
	?>
</urlset>