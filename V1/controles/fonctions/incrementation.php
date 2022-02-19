<?php

function incrementation($doc,$last_id){
        $numeroCreer = "";
        $annee = date("ym", time());
        //on verifie la longueur de id
        $longueur = strlen($last_id); //dernier enregistrement

        if($longueur < 2){                        //moins de 10
                $numeroCreer = $doc.$annee."000".$last_id;
        }else if($longueur == 2){                 //de 10 à 99
                $numeroCreer = $doc.$annee."00".$last_id;
        }else if($longueur == 3){                 //de 100 à 999
                $numeroCreer = $doc.$annee."0".$last_id;
        }else if($longueur == 4){                 //de 1000 à 9999
                $numeroCreer = $doc.$annee.$last_id;
        }

        return $numeroCreer;
}

?>