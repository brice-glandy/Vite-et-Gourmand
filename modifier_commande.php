<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/Commandes.php';
require_once 'includes/Menus.php';

// Réservé aux clients connectés
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'utilisateur') {
    header('Location: connexion.php');
    exit;
}

// La commande doit appartenir à ce client ET être encore en attente
$modele_commandes = new Commandes($pdo);
$commande = false;
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $commande = $modele_commandes->trouver_du_client((int) $_GET['id'], $_SESSION['utilisateur_id']);
}
if (!$commande || $commande['statut'] != 'en attente') {
    $_SESSION['erreur_commandes'] = 'Cette commande ne peut plus être modifiée.';
    header('Location: espace_client.php#titre-commandes');
    exit;
}

// Le menu (fixe), ses plats possibles, et les plats déjà choisis
$modele_menus = new Menus($pdo);
$menu = $modele_menus->trouver_par_id($commande['menu_id']);
$plats_possibles = $modele_menus->trouver_plats($commande['menu_id']);
$ids_choisis = array_column($modele_commandes->trouver_plats_choisis($commande['id']), 'id');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier ma commande - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include_once 'includes/header.php'; ?>
<main class="page-espace-client">
    <h1>Modifier la commande <?php echo htmlspecialchars($commande['numero_commande']); ?></h1>

    <?php if (isset($_SESSION['erreurs_modification'])) { ?>
        <div class="message-erreur" role="alert">
            <ul>
                <?php foreach ($_SESSION['erreurs_modification'] as $erreur) { ?>
                    <li><?php echo htmlspecialchars($erreur); ?></li>
                <?php } ?>
            </ul>
        </div>
        <?php unset($_SESSION['erreurs_modification']); ?>
    <?php } ?>

    <p><strong>Menu :</strong> <?php echo htmlspecialchars($menu['titre']); ?> (le menu ne peut pas être changé)</p>

    <form action="traitement_modification.php" method="POST" class="formulaire-connexion">
        <input type="hidden" name="commande_id" value="<?php echo $commande['id']; ?>">

        <?php foreach (['Entrée', 'Plat', 'Dessert'] as $type) { ?>
            <?php
            // Les plats possibles de ce type, pour ce menu
            $plats_du_type = [];
            foreach ($plats_possibles as $un_plat) {
                if ($un_plat['type_plat'] == $type) {
                    $plats_du_type[] = $un_plat;
                }
            }
            ?>
            <?php if (count($plats_du_type) > 0) { ?>
                <fieldset>
                    <legend><?php echo $type; ?></legend>
                    <div class="options-menu">
                        <?php foreach ($plats_du_type as $un_plat) { ?>
                            <label>
                                <input type="radio" name="plats[<?php echo $type; ?>]" value="<?php echo $un_plat['id']; ?>"
                                    <?php if (in_array($un_plat['id'], $ids_choisis)) { echo 'checked'; } ?> required>
                                <?php echo htmlspecialchars($un_plat['titre_plat']); ?>
                            </label>
                        <?php } ?>
                    </div>
                </fieldset>
            <?php } ?>
        <?php } ?>

        <label for="nombre-personnes">Nombre de personnes (minimum <?php echo $menu['nombre_personne_minimum']; ?>)</label>
        <input type="number" id="nombre-personnes" name="nombre-personnes" min="<?php echo $menu['nombre_personne_minimum']; ?>" value="<?php echo $commande['nombre_personne']; ?>" required>

        <label for="date-prestation">Date de la prestation</label>
        <input type="date" id="date-prestation" name="date-prestation" value="<?php echo $commande['date_prestation']; ?>" required>

        <label for="heure-souhaitee">Heure souhaitée</label>
        <input type="time" id="heure-souhaitee" name="heure-souhaitee" value="<?php echo substr($commande['heure_livraison'], 0, 5); ?>" required>

        <label for="adresse">Adresse complète (rue, complément, code postal)</label>
        <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($commande['adresse_livraison']); ?>" required>

        <label for="ville">Ville / commune</label>
        <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($commande['ville_livraison']); ?>" required>

        <label for="distance">Distance depuis Bordeaux en km (seulement hors Bordeaux)</label>
        <input type="number" id="distance" name="distance" min="1" max="100" step="1">

        <p>Le prix sera recalculé à l'enregistrement, selon le nombre de personnes et la livraison.</p>

        <button type="submit" class="btn-valider">Enregistrer les modifications</button>
        <a href="espace_client.php" class="lien-secondaire">Retour à mon espace</a>
    </form>
</main>
<?php include_once 'includes/footer.php'; ?>
</body>
</html>