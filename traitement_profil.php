<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// 1. Réservé aux clients connectés, et seulement par l'envoi du formulaire (POST)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'utilisateur') {
    header('Location: connexion.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: espace_client.php');
    exit;
}

// 2. Lecture des champs, sans espaces autour (comme à l'inscription)
function lireChamp($nom) {
    if (isset($_POST[$nom])) {
        return trim($_POST[$nom]);
    }
    return '';
}

$nom = lireChamp('nom');
$prenom = lireChamp('prenom');
$telephone = str_replace(' ', '', lireChamp('telephone'));
$email = lireChamp('email');
$adresse_postale = lireChamp('adresse_postale');
$ville = lireChamp('ville');

// 3. Les mêmes vérifications qu'à l'inscription
$erreurs = [];
if ($nom == '' || $prenom == '' || $telephone == '' || $email == '' || $adresse_postale == '' || $ville == '') {
    $erreurs[] = 'Tous les champs sont obligatoires.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}
if (!preg_match('/^0[67][0-9]{8}$/', $telephone)) {
    $erreurs[] = 'Le numéro de GSM doit comporter 10 chiffres et commencer par 06 ou 07.';
}

// 4. L'email ne doit pas déjà appartenir à un AUTRE compte
$requete = $pdo->prepare("SELECT id FROM utilisateur WHERE email = :email AND id != :id");
$requete->execute(['email' => $email, 'id' => $_SESSION['utilisateur_id']]);
if ($requete->fetch()) {
    $erreurs[] = 'Cette adresse email est déjà utilisée par un autre compte.';
}
// 5. S'il y a des erreurs : retour à l'espace client avec les messages
if (count($erreurs) > 0) {
    $_SESSION['erreurs_profil'] = $erreurs;
    header('Location: espace_client.php#titre-infos');
    exit;
}

// 6. Mise à jour du compte de CE client (l'id vient de la session)
$sql = "UPDATE utilisateur
        SET nom = :nom, prenom = :prenom, telephone = :telephone, email = :email,
            adresse_postale = :adresse_postale, ville = :ville
        WHERE id = :id";
$requete = $pdo->prepare($sql);
$requete->execute([
    'nom' => $nom,
    'prenom' => $prenom,
    'telephone' => $telephone,
    'email' => $email,
    'adresse_postale' => $adresse_postale,
    'ville' => $ville,
    'id' => $_SESSION['utilisateur_id']
]);

// Le prénom est aussi gardé en session : on le met à jour
$_SESSION['prenom'] = $prenom;

// 7. Post/Redirect/Get : retour à l'espace client avec un message
$_SESSION['message_profil'] = 'Vos informations ont bien été enregistrées.';
header('Location: espace_client.php#titre-infos');
exit;