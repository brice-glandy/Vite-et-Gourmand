<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';
require_once 'includes/Plats.php';

// 1. Seulement en POST, avec un numéro de plat
if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_POST['plat_id']) || !ctype_digit($_POST['plat_id'])) {
    header('Location: admin.php#plats');
    exit;
}

$id = (int) $_POST['plat_id'];
$modele_plats = new Plats($pdo);

// 2. Le plat doit exister
$plat = $modele_plats->trouver_par_id($id);
if (!$plat) {
    header('Location: admin.php#plats');
    exit;
}

// 3. Utilisé par un menu ou une commande : on le garde, et on explique pourquoi
if ($modele_plats->est_utilise($id)) {
    $_SESSION['message_plats'] = 'Le plat « ' . $plat['titre_plat'] . ' » est proposé par un menu ou figure dans une commande : il est conservé. Pour ne plus le proposer, retirez-le des menus concernés.';
    header('Location: admin.php#plats');
    exit;
}

// 4. Utilisé nulle part : on l'efface vraiment
try {
    $modele_plats->supprimer($id);
    $_SESSION['message_plats'] = 'Le plat « ' . $plat['titre_plat'] . ' » a été supprimé.';
} catch (PDOException $e) {
    error_log('Suppression du plat ' . $id . ' impossible : ' . $e->getMessage());
    $_SESSION['message_plats'] = 'Le plat « ' . $plat['titre_plat'] . ' » n\'a pas pu être supprimé. Réessayez dans un instant.';
}

// 5. Retour sur la rubrique Plats (Post/Redirect/Get)
header('Location: admin.php#plats');
exit;