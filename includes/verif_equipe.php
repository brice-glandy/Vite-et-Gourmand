<?php
// Accès réservé à l'administrateur et aux employés.
// Ce fichier doit être inclus APRÈS includes/session.php.
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'administrateur' && $_SESSION['role'] != 'employe')) {
    header('Location: connexion.php');
    exit;
}