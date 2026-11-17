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

// 4. (étape G2b : MongoDB et mails)

// 5. Post/Redirect/Get : retour à la rubrique Commandes
$_SESSION['message_admin_commandes'] = 'La commande ' . $commande['numero_commande'] . ' est maintenant « ' . $nouveau_statut . ' ».';
header('Location: admin.php#commandes');
exit;