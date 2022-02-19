<?php
@session_start (); 
session_destroy(); // on detruit la session

@session_start (); 
$_SESSION['alertMessage'] = "Vous êtes déconnecté(e) !";
$_SESSION['alertMessageConfig'] = "warning";

header("Location: /");
exit();
?>
