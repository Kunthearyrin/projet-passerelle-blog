<?php
    session_start(); // Initialisation de la session
    session_unset(); // Désactivation de la session
    session_destroy(); // Destruction de la session

    setcookie('auth', '', time() - 1);

    header('location: index.php');
    exit();