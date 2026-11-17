<?php
require_once 'includes/session.php';
require_once 'includes/verif_admin.php';
require_once 'includes/db.php';
require_once 'includes/Employes.php';

// Seulement en POST, avec un numéro d'employé et une valeur 0 ou 1
if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_POST['employe_id']) || !ctype_digit($_POST['employe_id'])
    || !isset($_POST['actif']) || !in_array($_POST['actif'], ['0', '1'], true)) {
    header('Location: admin.php#equipe');
    exit;
}

$modele_employes = new Employes($pdo);
$modele_employes->changer_actif((int) $_POST['employe_id'], (int) $_POST['actif']);

if ($_POST['actif'] == '1') {
    $_SESSION['message_equipe'] = 'Le compte a été réactivé.';
} else {
    $_SESSION['message_equipe'] = 'Le compte a été désactivé : il ne peut plus se connecter.';
}
header('Location: admin.php#equipe');
exit;