<?php
// Accès réservé à l'administrateur et aux employés.
// Ce fichier doit être inclus APRÈS includes/session.php.
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'administrateur' && $_SESSION['role'] != 'employe')) {
    header('Location: connexion.php');
    exit;
}

// Un compte désactivé perd l'accès immédiatement, même s'il était déjà connecté
require_once __DIR__ . '/db.php';
$requete = $pdo->prepare("SELECT actif FROM utilisateur WHERE id = :id");
$requete->execute(['id' => $_SESSION['utilisateur_id']]);
if (!$requete->fetchColumn()) {
    header('Location: deconnexion.php');
    exit;
}