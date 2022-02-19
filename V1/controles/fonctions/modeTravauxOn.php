<?php
  if($GLOBALS['travaux'] == true){
    if(!isset($_SESSION['levelUser']) || $_SESSION['levelUser'] < 4){
      $_SESSION['alertMessage'] = "Site en travaux...";
      $_SESSION['alertMessageConfig'] = "warning";
      header("Location: /travaux/");
      exit();
    }
  }
?>
