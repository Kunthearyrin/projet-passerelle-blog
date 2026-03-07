<?php

session_start();
require_once(__DIR__ . '/option.php');

// Vérifier que l'utilisateur est bien connecté
if (!isset($_SESSION['connect']) || $_SESSION['connect'] != 1) {
    header('Location: login.php?error=1&message=Vous devez être connecté.');
    exit();
}

// Vérifier qu'il s'agit de l'admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?error=1&message=Accès refusé. Zone réservée aux administrateurs.');
    exit();
}

?>