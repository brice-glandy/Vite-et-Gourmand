<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/Menus.php';

// 1. Le numéro du menu arrive dans l'adresse : detail_menu.php?id=3
$id = 0;
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = (int) $_GET['id'];
}

// 2. On demande ce menu au modèle (false s'il n'existe pas)
$menu = false;
if ($id > 0) {
    $modele_menus = new Menus($pdo);
    $menu = $modele_menus->trouver_par_id($id);
}

// 3. Menu introuvable : code 404 « page introuvable »
if (!$menu) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if ($menu) { echo htmlspecialchars($menu['titre']) . ' - '; } ?>Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include_once 'includes/header.php'; ?>
<main class="page-detail-menu">
<?php if (!$menu) { ?>
    <h1>Menu introuvable</h1>
    <p>Ce menu n'existe pas ou n'est plus proposé.</p>
    <a href="menus.php">Retour à tous les menus</a>
<?php } else { ?>
    <h1><?php echo htmlspecialchars($menu['titre']); ?></h1>
<?php } ?>
</main>
<?php include_once 'includes/footer.php'; ?>
</body>
</html>