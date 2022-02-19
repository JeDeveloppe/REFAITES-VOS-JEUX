<?php
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
    $url = "https"; 
    else
    $url = "http"; 
    
    // Ajoutez // à l'URL.
    $url .= "://"; 
    
    // Ajoutez l'hôte (nom de domaine, ip) à l'URL.
    $url .= $_SERVER['HTTP_HOST']; 
    
    // Ajouter l'emplacement de la ressource demandée à l'URL
    $url .= $_SERVER['REQUEST_URI']; 
?>

<!-- Open Graph meta pour Facebook, LinkedIn, etc... (sauf Twitter) -->
<meta property="og:title" content="<?php echo $titreDeLaPage; ?>" />
<meta property="og:url" content="<?php echo $url; ?>" />
<meta property="og:image" content="<?php echo $GLOBALS['domaine'].'/catalogue/decoder.php?data='.$_GET['jeu']; ?>" />
<meta property="og:description" content="<?php echo $descriptionPage; ?>" />
<meta property="og:type" content="website" />