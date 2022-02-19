<?php
//si le membre n'est plus connecté, redirection à l'index du site
if(!isset($_SESSION['levelUser']) || $_SESSION['levelUser'] < 4){
    $_SESSION['alertMessage'] = "Réservé aux administraturs !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /accueil/");
    exit();
  }
?>
