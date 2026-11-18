<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/mail.php';
require_once 'includes/Reinitialisations.php';

// 1. Seulement en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mot_de_passe_oublie.php');
    exit;
}

$email = (isset($_POST['email']) && is_string($_POST['email'])) ? trim($_POST['email']) : '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['erreur_oubli'] = 'L\'adresse email n\'est pas valide.';
    header('Location: mot_de_passe_oublie.php');
    exit;
}

// 2. Le compte existe-t-il, et est-il actif ?
$requete = $pdo->prepare("SELECT id, prenom, actif FROM utilisateur WHERE email = :email");
$requete->execute(['email' => $email]);
$utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

// 3. Seulement si oui : un jeton et un mail avec le lien
if ($utilisateur && $utilisateur['actif']) {
    $modele_reinit = new Reinitialisations($pdo);
    $jeton = $modele_reinit->creer_jeton($utilisateur['id']);
    $lien = $config_url_site . '/nouveau_mot_de_passe.php?jeton=' . $jeton;

    envoyer_mail(
        $email,
        'Réinitialisation de votre mot de passe',
        "Bonjour " . $utilisateur['prenom'] . ",\n\n"
        . "Vous avez demandé à réinitialiser votre mot de passe Vite & Gourmand.\n"
        . "Pour en choisir un nouveau, ouvrez ce lien (valable 1 heure, une seule fois) :\n"
        . $lien . "\n\n"
        . "Si vous n'êtes pas à l'origine de cette demande, ignorez ce mail : votre mot de passe actuel reste valable.\n\n"
        . "Julie et José, Vite & Gourmand"
    );
}

// 4. La même réponse dans tous les cas : on ne révèle pas si l'adresse a un compte
$_SESSION['message_oubli'] = 'Si un compte existe avec cette adresse, un lien de réinitialisation vient de lui être envoyé. Pensez à vérifier vos courriers indésirables.';
header('Location: mot_de_passe_oublie.php');
exit;