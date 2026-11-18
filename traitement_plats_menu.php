<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';
require_once 'includes/Menus.php';
require_once 'includes/Plats.php';

// 1. Seulement en POST, pour un menu qui existe
if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_POST['menu_id']) || !ctype_digit($_POST['menu_id'])) {
    header('Location: admin.php#menus');
    exit;
}
$menu_id = (int) $_POST['menu_id'];
$modele_menus = new Menus($pdo);
$menu = $modele_menus->trouver_par_id($menu_id);
if (!$menu) {
    header('Location: admin.php#menus');
    exit;
}

// 2. Le type de chaque plat existant, par numéro : par exemple [1 => 'Entrée', 2 => 'Plat', ...]
$modele_plats = new Plats($pdo);
$types_des_plats = array_column($modele_plats->trouver_tous(), 'type_plat', 'id');

$erreurs = [];

// 3. Les plats proposés : chacun doit exister (liste blanche)
$proposes = [];
if (isset($_POST['proposes']) && is_array($_POST['proposes'])) {
    foreach ($_POST['proposes'] as $plat_id) {
        if (is_string($plat_id) && ctype_digit($plat_id) && isset($types_des_plats[(int) $plat_id])) {
            $proposes[] = (int) $plat_id;
        } else {
            $erreurs[] = 'Un plat reçu n\'existe pas.';
        }
    }
}
$proposes = array_unique($proposes);
if (count($proposes) == 0) {
    $erreurs[] = 'Proposez au moins un plat.';
}

// 4. Pour chaque type : si des plats de ce type sont proposés, exactement un par défaut, proposé et du bon type
$recu_defauts = (isset($_POST['defauts']) && is_array($_POST['defauts'])) ? $_POST['defauts'] : [];
$defauts = [];
$defauts_saisis = [];
foreach (Plats::TYPES as $type) {
    // Combien de plats proposés sont de ce type ?
    $nombre_du_type = 0;
    foreach ($proposes as $plat_id) {
        if ($types_des_plats[$plat_id] == $type) {
            $nombre_du_type++;
        }
    }
    $defaut = (isset($recu_defauts[$type]) && is_string($recu_defauts[$type]) && ctype_digit($recu_defauts[$type])) ? (int) $recu_defauts[$type] : 0;
    if ($defaut != 0) {
        $defauts_saisis[] = $defaut;
    }
    if ($nombre_du_type == 0) {
        if ($defaut != 0) {
            $erreurs[] = $type . ' : le plat par défaut doit aussi être coché comme proposé.';
        }
    } elseif ($defaut == 0) {
        $erreurs[] = $type . ' : choisissez le plat servi par défaut.';
    } elseif (!in_array($defaut, $proposes, true) || $types_des_plats[$defaut] != $type) {
        $erreurs[] = $type . ' : le plat par défaut doit aussi être coché comme proposé.';
    } else {
        $defauts[] = $defaut;
    }
}

// 5. En cas d'erreur : retour au formulaire avec les messages et les cases telles qu'elles étaient
if (count($erreurs) > 0) {
    $_SESSION['erreurs_plats_menu'] = array_unique($erreurs);
      $_SESSION['saisie_plats_menu'] = ['proposes' => $proposes, 'defauts' => $defauts_saisis];
    header('Location: plats_menu.php?id=' . $menu_id);
    exit;
}

// 6. Enregistrement (transaction dans la méthode)
try {
    $modele_menus->enregistrer_plats($menu_id, $proposes, $defauts);
    $_SESSION['message_menus'] = 'Les plats du menu « ' . $menu['titre'] . ' » ont été enregistrés.';
} catch (PDOException $e) {
    error_log('Plats du menu ' . $menu_id . ' : ' . $e->getMessage());
    $_SESSION['message_menus'] = 'Les plats du menu « ' . $menu['titre'] . ' » n\'ont pas pu être enregistrés. Réessayez dans un instant.';
}

// 7. Retour sur la rubrique Menus (Post/Redirect/Get)
header('Location: admin.php#menus');
exit;