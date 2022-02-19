<?php
@session_start ();
switch($_GET['code']){
    case '404';
    $_SESSION['alertMessage'] = "404 - Page non trouvée !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /");
    break;

    default:
    $_SESSION['alertMessage'] = "Page non trouvée ou protégée !";
    $_SESSION['alertMessageConfig'] = "danger";
    header("Location: /");
}
?>