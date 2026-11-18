<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';
require_once 'includes/Commandes.php';

function retour_erreur($message) {
    $_SESSION['erreur_admin_commandes'] = $message;
    header('Location: admin.php#commandes');
    exit;
}

// 1. Seulement en POST (le rôle est déjà vérifié par verif_equipe.php)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php#commandes');
    exit;
}

// 2. La commande, le mode de contact et le motif (énoncé, page 8)
$modele_commandes = new Commandes($pdo);
$commande = false;
if (isset($_POST['commande_id']) && ctype_digit($_POST['commande_id'])) {
    $commande = $modele_commandes->trouver_par_id((int) $_POST['commande_id']);
}
$mode_contact = isset($_POST['mode_contact']) ? $_POST['mode_contact'] : '';
$motif = isset($_POST['motif']) ? trim($_POST['motif']) : '';

if (!$commande) {
    retour_erreur('Commande introuvable.');
}
if (!in_array($mode_contact, ['GSM', 'mail'], true)) {
    retour_erreur('Indiquez comment le client a été contacté (GSM ou mail).');
}
if ($motif === '' || mb_strlen($motif) > 1000) {
    retour_erreur('Le motif de l\'annulation est obligatoire (1000 caractères au maximum).');
}
if ($commande['statut'] == 'terminée' || $commande['statut'] == 'annulée') {
    retour_erreur('La commande ' . $commande['numero_commande'] . ' est déjà clôturée.');
}

// 3. Annulation + mode de contact + motif, stock rendu, suivi : tout ou rien
try {
    $pdo->beginTransaction();

    $requete = $pdo->prepare("UPDATE commande
                              SET statut = 'annulée', mode_contact = :mode_contact, motif_annulation = :motif
                              WHERE id = :id AND statut NOT IN ('terminée', 'annulée')");
    $requete->execute(['mode_contact' => $mode_contact, 'motif' => $motif, 'id' => $commande['id']]);
    if ($requete->rowCount() == 0) {
        $pdo->rollBack();
        retour_erreur('La commande ' . $commande['numero_commande'] . ' vient d\'être clôturée.');
    }

    $requete = $pdo->prepare("UPDATE menu SET quantite_restante = quantite_restante + 1 WHERE id = :menu_id");
    $requete->execute(['menu_id' => $commande['menu_id']]);

    $modele_commandes->ajouter_suivi($commande['id'], 'annulée');

    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Annulation (employé) impossible : ' . $e->getMessage());
    retour_erreur('Une erreur technique empêche l\'annulation. Merci de réessayer.');
}

// 4. La copie MongoDB passe à « annulée » (exclue des statistiques)
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

// 5. Post/Redirect/Get
$_SESSION['message_admin_commandes'] = 'La commande ' . $commande['numero_commande'] . ' a été annulée (contact par ' . $mode_contact . ').';
header('Location: admin.php#commandes');
exit;