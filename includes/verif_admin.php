<?php
// Accès réservé à l'administrateur.
// Ce fichier doit être inclus APRÈS includes/session.php.
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'administrateur') {
    header('Location: connexion.php');
    exit;
}