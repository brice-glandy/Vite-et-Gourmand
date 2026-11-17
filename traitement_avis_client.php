<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/Commandes.php';

// 1. Réservé aux clients connectés, et seulement en POST
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'utilisateur') {
    header('Location: connexion.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: espace_client.php');
    exit;
}

function retour_erreur($message) {
    $_SESSION['erreur_commandes'] = $message;
    header('Location: espace_client.php#titre-commandes');
    exit;
}

// 2. La commande doit appartenir à ce client ET être terminée
$modele_commandes = new Commandes($pdo);
$commande = false;
if (isset($_POST['commande_id']) && ctype_digit($_POST['commande_id'])) {
    $commande = $modele_commandes->trouver_du_client((int) $_POST['commande_id'], $_SESSION['utilisateur_id']);
}
if (!$commande || $commande['statut'] != 'terminée') {
    retour_erreur('Vous pourrez donner votre avis quand la commande sera terminée.');
}

// 3. La note (1 à 5) et le commentaire
$note = 0;
if (isset($_POST['note']) && ctype_digit($_POST['note'])) {
    $note = (int) $_POST['note'];
}
$commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : '';
if ($note < 1 || $note > 5) {
    retour_erreur('Choisissez une note entre 1 et 5.');
}
if ($commentaire === '' || mb_strlen($commentaire) > 1000) {
    retour_erreur('Le commentaire est obligatoire (1000 caractères au maximum).');
}

// 4. Enregistrement « en attente » (l'employé le validera avant publication)
try {
    $requete = $pdo->prepare("INSERT INTO avis (utilisateur_id, commande_id, note, commentaire)
                              VALUES (:utilisateur_id, :commande_id, :note, :commentaire)");
    $requete->execute([
        'utilisateur_id' => $_SESSION['utilisateur_id'],
        'commande_id' => $commande['id'],
        'note' => $note,
        'commentaire' => $commentaire
    ]);
} catch (PDOException $e) {
    // Le cas le plus probable : un avis existe déjà pour cette commande (UNIQUE)
    error_log('Avis impossible : ' . $e->getMessage());
    retour_erreur('Vous avez déjà donné votre avis sur cette commande.');
}

// 5. Post/Redirect/Get
$_SESSION['message_commandes'] = 'Merci pour votre avis ! Il sera publié après validation par notre équipe.';
header('Location: espace_client.php#titre-commandes');
exit;