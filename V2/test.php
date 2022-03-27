<?php

$telephone = '1233350345';
if(preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/', $telephone,  $matches ) ){ // numero à 10 chiffres
    $telephone = $matches[1] . '-' .$matches[2] . '-' . $matches[3] . '-' . $matches[4] . '-' . $matches[5];
}else if(preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $telephone,  $matches ) ){ //numero à 8 chiffres
    $telephone = $matches[1] . '-' .$matches[2] . '-' . $matches[3] . '-' . $matches[4];
}else{
    $telephone = $telephone;
}

echo $telephone;