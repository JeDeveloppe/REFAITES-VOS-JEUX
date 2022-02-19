<?php

/*************************************************
 ATTENTION IL FAUT 4 rang dans un array
 1/ Question
 2/ Placeholder
 3/ Pattern
 4/ Réponse
 ****************************************************/


$captcha = array(
    1 => array("Quelle est la couleur d'un cheval blanc ?","Valide sous la forme AaaaA !","^[A-Z]{1}[a-z]{3}[A-Z]{1}$","BlanC"),
    2 => array("Combien font 2 + 5 ?","Valide sous la forme AaaA !","^[A-Z]{1}[a-z]{2}[A-Z]{1}$","SepT"),
    3 => array("Combien de carte dans un jeu de 52 cartes ?","Valide sous la forme 00","^[0-9]{2}$",52)
    );

     
    /****************************************************
        Détermination de la question à poser
    *****************************************************/
    $choix = 1;
    $choix = array_rand($captcha, 1);
    /****************************************************
        On met la réponse dans une variable de session que l'on récupére après
    *****************************************************/
    $_SESSION['reponseCaptcha'] = $captcha[$choix][3];
     
?>