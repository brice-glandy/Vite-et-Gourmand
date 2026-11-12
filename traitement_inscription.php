<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// 1. On n'accepte que les envois du formulaire (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: inscription.php');
    exit;
}

// 2. Petite fonction : lit un champ texte du formulaire, sans espaces autour
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
$mot_de_passe = lireChamp('mot_de_passe');
$confirmation = lireChamp('confirmation');

// 3. Vérifications : chaque problème ajoute un message dans la liste
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

// Les règles du mot de passe, une par une, pour un message précis
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
if (!isset($_POST['rgpd'])) {
    $erreurs[] = 'Vous devez accepter les conditions d\'utilisation de vos données.';
}

// 4. L'email ne doit pas déjà avoir un compte
$requete = $pdo->prepare("SELECT id FROM utilisateur WHERE email = :email");
$requete->execute(['email' => $email]);
if ($requete->fetch()) {
    $erreurs[] = 'Un compte existe déjà avec cette adresse email.';
}

// 5. S'il y a des erreurs : on les garde en session avec la saisie, et on revient au formulaire
if (count($erreurs) > 0) {
    $_SESSION['erreurs_inscription'] = $erreurs;
    $_SESSION['saisie_inscription'] = [
        'nom' => $nom,
        'prenom' => $prenom,
        'telephone' => $telephone,
        'email' => $email,
        'adresse_postale' => $adresse_postale,
        'ville' => $ville
    ];
    header('Location: inscription.php');
    exit;
}

// 6. Tout est bon : création du compte avec le rôle "utilisateur" et le mot de passe haché
$sql = "INSERT INTO utilisateur (role_id, email, mot_de_passe, nom, prenom, telephone, adresse_postale, ville)
        VALUES ((SELECT id FROM role WHERE libelle = 'utilisateur'), :email, :mot_de_passe, :nom, :prenom, :telephone, :adresse_postale, :ville)";
$requete = $pdo->prepare($sql);
$requete->execute([
    'email' => $email,
    'mot_de_passe' => password_hash($mot_de_passe, PASSWORD_DEFAULT),
    'nom' => $nom,
    'prenom' => $prenom,
    'telephone' => $telephone,
    'adresse_postale' => $adresse_postale,
    'ville' => $ville
]);

// 7. Retour à la connexion avec un message de succès
header('Location: connexion.php?inscription=ok');
exit;