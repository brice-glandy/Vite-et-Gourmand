<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';
require_once 'includes/Menus.php';

$modele_menus = new Menus($pdo);
$themes = $modele_menus->trouver_themes();
$regimes = $modele_menus->trouver_regimes();

// Les photos disponibles dans le dossier images/ (par exemple "images/burger.jpg")
$images = glob('images/*.jpg');

// Les valeurs du formulaire : vides pour un nouveau menu
$menu = [
    'id' => '',
    'titre' => '',
    'description' => '',
    'theme_id' => '',
    'regime_id' => '',
    'nombre_personne_minimum' => '',
    'prix_par_personne' => '',
    'quantite_restante' => '',
    'conditions' => '',
    'image_url' => ''
];
$titre_page = 'Ajouter un menu';
// Modification : si un numéro est dans l'adresse, on charge ce menu à la place des valeurs vides
if (isset($_GET['id'])) {
    if (!ctype_digit($_GET['id'])) {
        header('Location: admin.php#menus');
        exit;
    }
    $menu_trouve = $modele_menus->trouver_par_id($_GET['id']);
    if (!$menu_trouve) {
        header('Location: admin.php#menus');
        exit;
    }
    $menu = $menu_trouve;
    $titre_page = 'Modifier le menu ' . $menu['titre'];
}
// Après une erreur, on remet ce que l'employé avait tapé (rien n'est perdu)
if (isset($_SESSION['saisie_menu'])) {
    $menu = $_SESSION['saisie_menu'];
    unset($_SESSION['saisie_menu']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titre_page); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <main class="page-modifier-horaire">
        <h1><?php echo htmlspecialchars($titre_page); ?></h1>

        <?php if (isset($_SESSION['erreurs_menu'])) { ?>
            <div class="message-erreur" role="alert">
                <ul>
                    <?php foreach ($_SESSION['erreurs_menu'] as $erreur) { ?>
                        <li><?php echo htmlspecialchars($erreur); ?></li>
                    <?php } ?>
                </ul>
            </div>
            <?php unset($_SESSION['erreurs_menu']); ?>
        <?php } ?>
            <?php if ($menu['id'] != '') { ?>
            <p class="lien-plats-menu">
                <a href="plats_menu.php?id=<?php echo $menu['id']; ?>" class="btn-modifier">Choisir les plats de ce menu</a><br>
                Enregistrez d'abord vos changements ci-dessous : ils seraient perdus en quittant la page.
            </p>
        <?php } ?>
        <form action="traitement_menu.php" method="POST" class="formulaire-horaire formulaire-menu">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($menu['id']); ?>">

            <label for="titre">Titre</label>
            <input type="text" id="titre" name="titre" maxlength="100" value="<?php echo htmlspecialchars($menu['titre']); ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($menu['description']); ?></textarea>

            <label for="theme_id">Thème</label>
            <select id="theme_id" name="theme_id" required>
                <option value="">Choisir un thème</option>
                <?php foreach ($themes as $un_theme) { ?>
                    <option value="<?php echo $un_theme['id']; ?>" <?php if ($un_theme['id'] == $menu['theme_id']) { echo 'selected'; } ?>><?php echo htmlspecialchars($un_theme['libelle']); ?></option>
                <?php } ?>
            </select>

            <label for="regime_id">Régime</label>
            <select id="regime_id" name="regime_id" required>
                <option value="">Choisir un régime</option>
                <?php foreach ($regimes as $un_regime) { ?>
                    <option value="<?php echo $un_regime['id']; ?>" <?php if ($un_regime['id'] == $menu['regime_id']) { echo 'selected'; } ?>><?php echo htmlspecialchars($un_regime['libelle']); ?></option>
                <?php } ?>
            </select>

            <label for="nombre_personne_minimum">Nombre de personnes minimum</label>
            <input type="number" id="nombre_personne_minimum" name="nombre_personne_minimum" min="1" value="<?php echo htmlspecialchars($menu['nombre_personne_minimum']); ?>" required>

            <label for="prix_par_personne">Prix par personne (€)</label>
            <input type="number" id="prix_par_personne" name="prix_par_personne" min="0.01" step="0.01" value="<?php echo htmlspecialchars($menu['prix_par_personne']); ?>" required>

            <label for="quantite_restante">Stock (nombre de commandes possibles)</label>
            <input type="number" id="quantite_restante" name="quantite_restante" min="0" value="<?php echo htmlspecialchars($menu['quantite_restante']); ?>" required>

            <label for="conditions">Conditions du menu</label>
            <textarea id="conditions" name="conditions" rows="3" required><?php echo htmlspecialchars($menu['conditions']); ?></textarea>

            <label for="image_url">Image de couverture</label>
            <select id="image_url" name="image_url" required>
                <option value="">Choisir une image</option>
                <?php foreach ($images as $une_image) { ?>
                    <option value="<?php echo htmlspecialchars($une_image); ?>" <?php if ($une_image == $menu['image_url']) { echo 'selected'; } ?>><?php echo htmlspecialchars(basename($une_image)); ?></option>
                <?php } ?>
            </select>

            <div class="actions-horaires">
                <button type="submit" class="btn-valider">Enregistrer</button>
                <a href="admin.php#menus" class="btn-refuser">Annuler</a>
            </div>
        </form>
    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>