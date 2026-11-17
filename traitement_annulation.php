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

// 2. La commande doit exister ET appartenir à ce client
$commande = false;
if (isset($_POST['commande_id']) && ctype_digit($_POST['commande_id'])) {
    $modele_commandes = new Commandes($pdo);
    $commande = $modele_commandes->trouver_du_client((int) $_POST['commande_id'], $_SESSION['utilisateur_id']);
}
if (!$commande) {
    $_SESSION['erreur_commandes'] = 'Commande introuvable.';
    header('Location: espace_client.php#titre-commandes');
    exit;
}

// 3. Annulation possible seulement tant que la commande n'a pas été acceptée
if ($commande['statut'] != 'en attente') {
    $_SESSION['erreur_commandes'] = 'La commande ' . $commande['numero_commande'] . ' a déjà été prise en charge : contactez-nous pour toute modification.';
    header('Location: espace_client.php#titre-commandes');
    exit;
}

// 4. Statut « annulée » ET stock rendu, dans une même transaction (tout ou rien)
try {
    $pdo->beginTransaction();

    // a. On annule, seulement si elle est toujours en attente (un employé a pu l'accepter entre-temps)
    $requete = $pdo->prepare("UPDATE commande SET statut = 'annulée' WHERE id = :id AND statut = 'en attente'");
    $requete->execute(['id' => $commande['id']]);
    if ($requete->rowCount() == 0) {
        $pdo->rollBack();
        $_SESSION['erreur_commandes'] = 'La commande vient d\'être prise en charge : contactez-nous pour l\'annuler.';
        header('Location: espace_client.php#titre-commandes');
        exit;
    }

    // b. On rend 1 au stock du menu
    $requete = $pdo->prepare("UPDATE menu SET quantite_restante = quantite_restante + 1 WHERE id = :menu_id");
    $requete->execute(['menu_id' => $commande['menu_id']]);
    // c. Une étape de plus dans le suivi : « annulée »
    $modele_commandes->ajouter_suivi($commande['id'], 'annulée');

    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Annulation impossible : ' . $e->getMessage());
    $_SESSION['erreur_commandes'] = 'Une erreur technique empêche l\'annulation. Merci de réessayer.';
    header('Location: espace_client.php#titre-commandes');
    exit;
}

// 5. La copie MongoDB passe au statut exact « annulée » (les statistiques l'excluent).
// MySQL reste la référence : si MongoDB ne répond pas, l'annulation reste valable.
try {
    require_once 'includes/mongo.php';
    $ecriture = new MongoDB\Driver\BulkWrite();
    $ecriture->update(
        ['numero_commande' => $commande['numero_commande']],
        ['$set' => ['statut' => 'annulée']]
    );
    $mongo->executeBulkWrite($mongo_base . '.commandes', $ecriture);
} catch (MongoDB\Driver\Exception\Exception $e) {
    error_log('Mise à jour MongoDB impossible pour ' . $commande['numero_commande'] . ' : ' . $e->getMessage());
}

// 6. Post/Redirect/Get : retour à l'espace client avec un message
$_SESSION['message_commandes'] = 'La commande ' . $commande['numero_commande'] . ' a bien été annulée.';
header('Location: espace_client.php#titre-commandes');
exit;