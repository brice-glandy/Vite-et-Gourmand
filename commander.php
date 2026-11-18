<?php
// Point de passage du bouton « Commander » : il décide où envoyer la personne.
require_once 'includes/session.php';

// 1. Le numéro du menu choisi, vérifié comme ailleurs
if (!isset($_GET['menu']) || !ctype_digit($_GET['menu'])) {
    header('Location: menus.php');
    exit;
}
$menu_id = (int) $_GET['menu'];

// 2. Client connecté : directement la page de commande, avec le menu déjà choisi
if (isset($_SESSION['role']) && $_SESSION['role'] == 'utilisateur') {
    header('Location: commandes.php?menu=' . $menu_id);
    exit;
}

// 3. Employé ou administrateur connecté : ils ne commandent pas, retour au détail
if (isset($_SESSION['role'])) {
    header('Location: detail_menu.php?id=' . $menu_id);
    exit;
}

// 4. Visiteur : on note le menu dans la session (la note au dos du badge), puis connexion
$_SESSION['menu_a_commander'] = $menu_id;
header('Location: connexion.php?commande=1');
exit;