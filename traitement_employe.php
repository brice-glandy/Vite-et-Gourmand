<?php
require_once 'includes/session.php';
require_once 'includes/verif_admin.php';
require_once 'includes/db.php';
require_once 'includes/Employes.php';
require_once 'includes/mail.php';

// 1. Seulement en POST (le rôle administrateur est vérifié par verif_admin.php)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php#equipe');
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';
$modele_employes = new Employes($pdo);

// 2. Vérifications : email valide et libre, mot de passe robuste (mêmes règles qu'à l'inscription)
$erreurs = [];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
} elseif ($modele_employes->email_existe($email)) {
    $erreurs[] = 'Un compte existe déjà avec cette adresse email.';
}
if (strlen($mot_de_passe) < 10) {
    $erreurs[] = 'Le mot de passe doit contenir au moins 10 caractères.';
}
if (!preg_match('/[A-Z]/', $mot_de_passe)) {
    $erreurs[] = 'Le mot de passe doit contenir au moins une majuscule.';
}
if (!preg_match('/[a-z]/', $mot_de_passe)) {
    $erreurs[] = 'Le mot de passe doit contenir au moins une minuscule.';
}
if (!preg_match('/[0-9]/', $mot_de_passe)) {
    $erreurs[] = 'Le mot de passe doit contenir au moins un chiffre.';
}
if (!preg_match('/[^a-zA-Z0-9]/', $mot_de_passe)) {
    $erreurs[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
}

if (count($erreurs) > 0) {
    $_SESSION['erreurs_equipe'] = $erreurs;
    header('Location: admin.php#equipe');
    exit;
}

// 3. Création du compte employé
$modele_employes->creer($email, $mot_de_passe);

// 4. Mail à l'employé, SANS le mot de passe (énoncé, page 9)
envoyer_mail(
    $email,
    'Votre compte employé Vite & Gourmand',
    'Bonjour, un compte employé a été créé pour vous sur le site Vite & Gourmand, avec l\'identifiant ' . $email . '.'
    . ' Pour des raisons de sécurité, votre mot de passe ne figure pas dans ce mail : rapprochez-vous de l\'administrateur pour l\'obtenir.'
);

// 5. Post/Redirect/Get
$_SESSION['message_equipe'] = 'Le compte de ' . $email . ' a été créé. Communiquez-lui son mot de passe de vive voix.';
header('Location: admin.php#equipe');
exit;