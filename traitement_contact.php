<?php
require_once 'includes/session.php';
require_once 'includes/mail.php';

// L'adresse de l'entreprise, qui reçoit les demandes
$email_entreprise = 'contact@viteetgourmand.fr';

// 1. Seulement en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contacts.php');
    exit;
}

$titre = (isset($_POST['titre']) && is_string($_POST['titre'])) ? trim($_POST['titre']) : '';
$description = (isset($_POST['description']) && is_string($_POST['description'])) ? trim($_POST['description']) : '';
$email = (isset($_POST['email']) && is_string($_POST['email'])) ? trim($_POST['email']) : '';

// Le titre devient le sujet du mail : on y remplace les retours à la ligne par des espaces
$titre = str_replace(["\r", "\n"], ' ', $titre);

// 2. Vérifications côté serveur
$erreurs = [];
if ($titre == '' || mb_strlen($titre) > 100) {
    $erreurs[] = 'Le titre est obligatoire (100 caractères au maximum).';
}
if ($description == '' || mb_strlen($description) > 2000) {
    $erreurs[] = 'Le message est obligatoire (2000 caractères au maximum).';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
    $erreurs[] = 'L\'adresse email n\'est pas valide.';
}

if (count($erreurs) > 0) {
    $_SESSION['erreurs_contact'] = $erreurs;
    $_SESSION['saisie_contact'] = ['titre' => $titre, 'description' => $description, 'email' => $email];
    header('Location: contacts.php');
    exit;
}

// 3. Envoi de la demande à l'entreprise
$message = "Nouvelle demande reçue depuis le formulaire de contact du site.\n\n"
         . "Titre : " . $titre . "\n"
         . "Email du visiteur (pour lui répondre) : " . $email . "\n\n"
         . "Message :\n" . $description . "\n";

if (envoyer_mail($email_entreprise, 'Contact : ' . $titre, $message)) {
    $_SESSION['message_contact'] = 'Merci, votre demande a bien été envoyée. Nous vous répondrons à l\'adresse ' . $email . '.';
} else {
    $_SESSION['erreurs_contact'] = ['Votre demande n\'a pas pu être envoyée. Réessayez dans un instant.'];
    $_SESSION['saisie_contact'] = ['titre' => $titre, 'description' => $description, 'email' => $email];
}

// 4. Retour sur la page Contact (Post/Redirect/Get)
header('Location: contacts.php');
exit;