<?php

    function htEnTtc($prix,$tva){
        
        return number_format($prix * $tva / 100,"2",".","");
    }

    function ttcEnHt($prix,$tva){
        
        return number_format($prix / $tva * 100,"2",".","");
    }

    function affichageHTouTTC($prix){
        return number_format($prix / 100 ,"2",".","");
    }
?>