<?php
//si le membre n'est plus connecté, redirection à l'index du site
if(!isset($_SESSION['levelUser'])){
    $_SESSION['alertMessage'] = "Site en travaux...";
    $_SESSION['alertMessageConfig'] = "warning";
    header("Location: /travaux/");
    exit();
  }
?>
