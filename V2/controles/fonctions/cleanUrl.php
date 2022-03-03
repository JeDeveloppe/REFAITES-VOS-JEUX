<?php
	function clean_url($texte) {
		//Suppression des espaces en début et fin de chaîne
		$texte = trim($texte);

		//Suppression des accents
		$texte = strtr($texte,'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ','aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn');

		//mise en minuscule
		$texte = strtolower($texte);

		//Suppression des espaces et caracteres spéciaux
		$texte = str_replace(" ",'-',$texte);
		$texte = preg_replace('#([^a-z0-9-])#','-',$texte);

		//Suppression des tirets multiples
		$texte = preg_replace('#([-]+)#','-',$texte);

		//Suppression du premier caractère si c'est un tiret
		// if($texte[0] == '-'){
		// 	$texte = substr($texte,1);
		// }
		//Suppression du dernier caractère si c'est un tiret
		if(substr($texte, -1, 1) == '-'){
			$texte = substr($texte, 0, -1);
		}
		return $texte;
	}
?>