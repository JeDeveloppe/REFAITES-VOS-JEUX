<?php
@session_start ();

if($_SERVER['SERVER_NAME'] == "localhost"){ // CONNEXION BDD EN LOCAL
        try
        {
                $bdd = new PDO('mysql:host='.$GLOBALS['serveur'].';dbname='.$GLOBALS['database'].';charset=utf8', $GLOBALS['utilisateur'], $GLOBALS['password']);
                // Activation des erreurs PDO
                $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // mode de fetch par défaut : FETCH_ASSOC / FETCH_OBJ / FETCH_BOTH
                $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        catch (PDOException $e)
        {
                die('Erreur : ' . $e->getMessage());
        }
}else{ // CONNEXION BDD EN LIGNE
        try 
        {
                $bdd = new PDO("mysql:host=".$GLOBALS['serveur']."; dbname=".$GLOBALS['database'].";", $GLOBALS['utilisateur'], $GLOBALS['password']);
                // Activation des erreurs PDO
                $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // mode de fetch par défaut : FETCH_ASSOC / FETCH_OBJ / FETCH_BOTH
                $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        catch (PDOException $e)
        {
                die('Erreur : ' . $e->getMessage());
        }
}
?>