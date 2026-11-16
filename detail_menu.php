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
    $plats = $modele_menus->trouver_plats($id);
    $images = $modele_menus->trouver_images($id);
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
           <div class="galerie-menu">
        <img src="<?php echo htmlspecialchars($menu['image_url']); ?>" alt="Menu <?php echo htmlspecialchars($menu['titre']); ?>">
        <?php foreach ($images as $une_image) { ?>
            <img src="<?php echo htmlspecialchars($une_image['chemin']); ?>" alt="<?php echo htmlspecialchars($une_image['texte_alternatif']); ?>">
        <?php } ?>
    </div>

    <p class="description-detail-menu"><?php echo htmlspecialchars($menu['description']); ?></p>

    <ul class="infos-detail-menu">
        <li><strong>Thème :</strong> <?php echo htmlspecialchars($menu['theme']); ?></li>
        <li><strong>Régime :</strong> <?php echo htmlspecialchars($menu['regime']); ?></li>
        <li><strong>Minimum :</strong> <?php echo $menu['nombre_personne_minimum']; ?> personnes</li>
        <li><strong>Prix :</strong> <?php echo number_format($menu['prix_par_personne'], 2, ',', ' '); ?> € / personne</li>
        <li><strong>Stock disponible :</strong>
            <?php if ($menu['quantite_restante'] > 0) { ?>
                <?php echo $menu['quantite_restante']; ?> commande(s) possible(s)
            <?php } else { ?>
                plus disponible pour le moment
            <?php } ?>
        </li>
    </ul>
    <section class="plats-menu" aria-labelledby="titre-plats">
        <h2 id="titre-plats">Les plats</h2>
        <?php foreach (['Entrée', 'Plat', 'Dessert'] as $type) { ?>
            <?php
            // On garde seulement les plats de ce type
            $plats_du_type = [];
            foreach ($plats as $un_plat) {
                if ($un_plat['type_plat'] == $type) {
                    $plats_du_type[] = $un_plat;
                }
            }
            ?>
            <?php if (count($plats_du_type) > 0) { ?>
                <h3><?php echo $type; ?><?php if (count($plats_du_type) > 1) { echo ' (au choix)'; } ?></h3>
                <ul>
                    <?php foreach ($plats_du_type as $un_plat) { ?>
                        <li>
                            <?php echo htmlspecialchars($un_plat['titre_plat']); ?><br>
                            <small>Allergènes :
                                <?php if (!empty($un_plat['allergenes'])) { echo htmlspecialchars($un_plat['allergenes']); } else { echo 'aucun allergène signalé'; } ?>
                            </small>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        <?php } ?>
    </section>
    <section class="conditions-menu" aria-labelledby="titre-conditions">
        <h2 id="titre-conditions">Conditions de ce menu</h2>
        <?php if (!empty($menu['conditions'])) { ?>
            <p><?php echo nl2br(htmlspecialchars($menu['conditions'])); ?></p>
        <?php } else { ?>
            <p>Aucune condition particulière.</p>
        <?php } ?>
    </section>
        <?php if ($menu['quantite_restante'] > 0 && (!isset($_SESSION['role']) || $_SESSION['role'] == 'utilisateur')) { ?>
        <a href="commander.php?menu=<?php echo $menu['id']; ?>" class="bouton-principal">Commander ce menu</a>
    <?php } ?>
<?php } ?>
</main>
<?php include_once 'includes/footer.php'; ?>
</body>
</html>