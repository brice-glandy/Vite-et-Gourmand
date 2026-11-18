<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';
require_once 'includes/Commandes.php';

// Petite fonction : message d'erreur, puis retour à la rubrique Commandes
function retour_erreur($message) {
    $_SESSION['erreur_admin_commandes'] = $message;
    header('Location: admin.php#commandes');
    exit;
}

// 1. Seulement par l'envoi du formulaire (POST) ; verif_equipe.php a déjà vérifié le rôle
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php#commandes');
    exit;
}

// 2. La commande et le nouveau statut
$modele_commandes = new Commandes($pdo);
$commande = false;
if (isset($_POST['commande_id']) && ctype_digit($_POST['commande_id'])) {
    $commande = $modele_commandes->trouver_par_id((int) $_POST['commande_id']);
}
$nouveau_statut = isset($_POST['statut']) ? $_POST['statut'] : '';

if (!$commande) {
    retour_erreur('Commande introuvable.');
}
if (!in_array($nouveau_statut, Commandes::STATUTS_MISE_A_JOUR, true)) {
    retour_erreur('Statut invalide.');
}
if ($commande['statut'] == 'terminée' || $commande['statut'] == 'annulée') {
    retour_erreur('La commande ' . $commande['numero_commande'] . ' est clôturée : son statut ne peut plus changer.');
}
if ($nouveau_statut == $commande['statut']) {
    retour_erreur('La commande ' . $commande['numero_commande'] . ' est déjà « ' . $nouveau_statut . ' ».');
}

// 3. Nouveau statut ET étape de suivi, dans une même transaction
try {
    $pdo->beginTransaction();

    $requete = $pdo->prepare("UPDATE commande SET statut = :statut WHERE id = :id");
    $requete->execute(['statut' => $nouveau_statut, 'id' => $commande['id']]);

    $modele_commandes->ajouter_suivi($commande['id'], $nouveau_statut);

    // Cohérence avec les colonnes de prêt de matériel de la table commande
    if ($nouveau_statut == 'en attente du retour de matériel') {
        $requete = $pdo->prepare("UPDATE commande SET pret_materiel = TRUE WHERE id = :id");
        $requete->execute(['id' => $commande['id']]);
    }
    if ($nouveau_statut == 'terminée' && $commande['pret_materiel']) {
        $requete = $pdo->prepare("UPDATE commande SET restitution_materiel = TRUE WHERE id = :id");
        $requete->execute(['id' => $commande['id']]);
    }

    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Changement de statut impossible : ' . $e->getMessage());
    retour_erreur('Une erreur technique empêche la mise à jour. Merci de réessayer.');
}

// 4a. La copie MongoDB suit le statut (les statistiques excluent seulement « annulée »)
try {
    require_once 'includes/mongo.php';
    $ecriture = new MongoDB\Driver\BulkWrite();
    $ecriture->update(
        ['numero_commande' => $commande['numero_commande']],
        ['$set' => ['statut' => $nouveau_statut]]
    );
    $mongo->executeBulkWrite($mongo_base . '.commandes', $ecriture);
} catch (MongoDB\Driver\Exception\Exception $e) {
    error_log('Mise à jour MongoDB impossible pour ' . $commande['numero_commande'] . ' : ' . $e->getMessage());
}

// 4b. Les mails prévus par l'énoncé (simulés, voir includes/mail.php)
require_once 'includes/mail.php';
if ($nouveau_statut == 'en attente du retour de matériel') {
    envoyer_mail(
        $commande['email'],
        'Retour du matériel prêté - commande ' . $commande['numero_commande'],
        'Bonjour ' . $commande['prenom'] . ', du matériel vous a été prêté pour votre commande ' . $commande['numero_commande'] . '.'
        . ' Merci de prendre contact avec nous pour le restituer. Sans restitution sous 10 jours ouvrés,'
        . ' des frais de 600 € vous seront facturés, conformément à nos conditions générales de vente. Vite & Gourmand'
    );
}
if ($nouveau_statut == 'terminée') {
    envoyer_mail(
        $commande['email'],
        'Votre avis nous intéresse - commande ' . $commande['numero_commande'],
        'Bonjour ' . $commande['prenom'] . ', votre commande ' . $commande['numero_commande'] . ' est terminée.'
        . ' Connectez-vous à votre espace pour nous donner votre avis (une note de 1 à 5 et un commentaire). Merci ! Vite & Gourmand'
    );
}

// 5. Post/Redirect/Get : retour à la rubrique Commandes
$_SESSION['message_admin_commandes'] = 'La commande ' . $commande['numero_commande'] . ' est maintenant « ' . $nouveau_statut . ' ».';
header('Location: admin.php#commandes');
exit;