<?php
 $req = $bdd->prepare('SELECT * FROM users WHERE idUser = :user');
 $req->execute(array('user' => $_SESSION['userId']));
 $donnees = $req->fetch();
   if(empty($donnees['annonceCheck']) || empty($donnees['travailCheck'])){
     $rechercheKO = true;
     if(!isset($_SESSION['rechercheKO'])){
        $_SESSION['rechercheKO'] = true;
     }
   }else{
    if(isset($_SESSION['rechercheKO'])){
        unset($_SESSION['rechercheKO']);
     }
   }
?>