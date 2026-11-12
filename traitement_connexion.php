<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// 1. On n'accepte que les envois du formulaire (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: connexion.php');
    exit;
}

// 2. Les deux champs doivent être présents
if (!isset($_POST['email']) || !isset($_POST['mot_de_passe'])) {
    header('Location: connexion.php?erreur=1');
    exit;
}

$email = trim($_POST['email']);
$mot_de_passe = $_POST['mot_de_passe'];

// 3. On cherche l'utilisateur par son email, avec le nom de son rôle
$sql = "SELECT utilisateur.id, utilisateur.prenom, utilisateur.mot_de_passe, utilisateur.actif, role.libelle AS role
        FROM utilisateur
        JOIN role ON utilisateur.role_id = role.id
        WHERE utilisateur.email = :email";
$requete = $pdo->prepare($sql);
$requete->execute(['email' => $email]);
$utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

// 4. Email inconnu OU mot de passe faux : même message dans les deux cas
if (!$utilisateur || !password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
    header('Location: connexion.php?erreur=1');
    exit;
}

// 5. Compte désactivé (par exemple un employé qui a quitté l'entreprise)
if (!$utilisateur['actif']) {
    header('Location: connexion.php?erreur=inactif');
    exit;
}

// 6. Connexion réussie : nouveau numéro de session, puis on mémorise l'utilisateur
session_regenerate_id(true);
$_SESSION['utilisateur_id'] = $utilisateur['id'];
$_SESSION['prenom'] = $utilisateur['prenom'];
$_SESSION['role'] = $utilisateur['role'];

// 7. Redirection selon le rôle
if ($utilisateur['role'] == 'administrateur' || $utilisateur['role'] == 'employe') {
    header('Location: admin.php');
} else {
    header('Location: menus.php');
}
exit;