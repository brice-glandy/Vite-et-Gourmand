<?php
require_once 'includes/session.php';

// 1. On vide toutes les données de la session
$_SESSION = [];

// 2. On demande au navigateur de supprimer le cookie de session
setcookie(session_name(), '', time() - 3600, '/');

// 3. On détruit la session côté serveur
session_destroy();

// 4. Retour à la page de connexion
header('Location: connexion.php');
exit;