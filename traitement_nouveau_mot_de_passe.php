<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/Reinitialisations.php';

// 1. Seulement en POST, avec un jeton au bon format
$jeton = (isset($_POST['jeton']) && is_string($_POST['jeton'])) ? $_POST['jeton'] : '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || strlen($jeton) != 64 || !ctype_xdigit($jeton)) {
    header('Location: mot_de_passe_oublie.php');
    exit;
}

// 2. Le jeton est revérifié ici : il a pu expirer pendant que le client tapait
$modele_reinit = new Reinitialisations($pdo);
$utilisateur_id = $modele_reinit->trouver_utilisateur($jeton);
if ($utilisateur_id === false) {
    header('Location: nouveau_mot_de_passe.php?jeton=' . $jeton);
    exit;
}

$mot_de_passe = (isset($_POST['mot_de_passe']) && is_string($_POST['mot_de_passe'])) ? $_POST['mot_de_passe'] : '';
$confirmation = (isset($_POST['confirmation']) && is_string($_POST['confirmation'])) ? $_POST['confirmation'] : '';

// 3. Les mêmes règles qu'à l'inscription (énoncé, page 5)
$erreurs = [];
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
if ($mot_de_passe !== $confirmation) {
    $erreurs[] = 'Les deux mots de passe ne sont pas identiques.';
}

if (count($erreurs) > 0) {
    $_SESSION['erreurs_nouveau_mdp'] = $erreurs;
    header('Location: nouveau_mot_de_passe.php?jeton=' . $jeton);
    exit;
}

// 4. Changement du mot de passe et suppression du jeton (transaction dans la méthode)
$modele_reinit->changer_mot_de_passe($utilisateur_id, $mot_de_passe);
header('Location: connexion.php?reinitialisation=ok');
exit;