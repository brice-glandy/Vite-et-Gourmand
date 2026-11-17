<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';
require_once 'includes/Menus.php';

// 1. Seulement en POST, avec un numéro de menu
if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_POST['menu_id']) || !ctype_digit($_POST['menu_id'])) {
    header('Location: admin.php#menus');
    exit;
}

$id = (int) $_POST['menu_id'];
$modele_menus = new Menus($pdo);

// 2. Le menu doit exister
$menu = $modele_menus->trouver_par_id($id);
if (!$menu) {
    header('Location: admin.php#menus');
    exit;
}

// 3. Déjà commandé : on le garde pour l'historique, mais on le retire de la vente
if ($modele_menus->est_commande($id)) {
    $modele_menus->retirer_de_la_vente($id);
    $_SESSION['message_menus'] = 'Le menu « ' . $menu['titre'] . ' » a déjà été commandé : il est conservé pour l\'historique, mais retiré de la vente (stock mis à 0).';
    header('Location: admin.php#menus');
    exit;
}

// 4. Jamais commandé : on l'efface vraiment
try {
    $modele_menus->supprimer($id);
    $_SESSION['message_menus'] = 'Le menu « ' . $menu['titre'] . ' » a été supprimé.';
} catch (PDOException $e) {
    // Le détail technique va dans le journal, un message simple pour l'employé
    error_log('Suppression du menu ' . $id . ' impossible : ' . $e->getMessage());
    $_SESSION['message_menus'] = 'Le menu « ' . $menu['titre'] . ' » n\'a pas pu être supprimé. Réessayez dans un instant.';
}

// 5. Retour sur la rubrique Menus (Post/Redirect/Get)
header('Location: admin.php#menus');
exit;