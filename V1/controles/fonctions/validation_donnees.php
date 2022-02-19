<?php

function valid_donnees($donnees){
    if(isset($donnees) && !empty($donnees)){
        $donnees = trim($donnees);
        $donnees = stripslashes($donnees);
        $donnees = htmlspecialchars($donnees);
        return $donnees;
    }else{
        $donnees = "";
    }    
}
function valid_donnees_int($donnees){
    $donnees = trim($donnees);
    $donnees = stripslashes($donnees);
    $donnees = htmlspecialchars($donnees);
    return $donnees;
}

?>