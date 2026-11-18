<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';
require_once 'includes/Menus.php';
require_once 'includes/Plats.php';

// 1. Le menu : numéro valide et menu existant
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: admin.php#menus');
    exit;
}
$modele_menus = new Menus($pdo);
$menu = $modele_menus->trouver_par_id($_GET['id']);
if (!$menu) {
    header('Location: admin.php#menus');
    exit;
}

// 2. Tous les plats, et ceux déjà associés à ce menu
$modele_plats = new Plats($pdo);
$plats = $modele_plats->trouver_tous();
$associations = $modele_menus->trouver_associations_plats($menu['id']);
$ids_proposes = array_keys($associations);              // tous les plats du menu
$ids_defauts = array_keys(array_filter($associations)); // seulement ceux qui valent true

// 3. Après une erreur, on remet les cases exactement comme l'employé les avait laissées
if (isset($_SESSION['saisie_plats_menu'])) {
    $ids_proposes = $_SESSION['saisie_plats_menu']['proposes'];
    $ids_defauts = $_SESSION['saisie_plats_menu']['defauts'];
    unset($_SESSION['saisie_plats_menu']);
}

$titre_page = 'Plats du menu ' . $menu['titre'];
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
        <p>Cochez les plats proposés par ce menu. Pour chaque type proposé, choisissez le plat servi par défaut.</p>
        <?php if (isset($_SESSION['message_menus'])) { ?>
            <p class="message-succes" role="status"><?php echo htmlspecialchars($_SESSION['message_menus']); ?></p>
            <?php unset($_SESSION['message_menus']); ?>
        <?php } ?>
        <?php if (isset($_SESSION['erreurs_plats_menu'])) { ?>
            <div class="message-erreur" role="alert">
                <ul>
                    <?php foreach ($_SESSION['erreurs_plats_menu'] as $erreur) { ?>
                        <li><?php echo htmlspecialchars($erreur); ?></li>
                    <?php } ?>
                </ul>
            </div>
            <?php unset($_SESSION['erreurs_plats_menu']); ?>
        <?php } ?>

        <form action="traitement_plats_menu.php" method="POST" class="formulaire-horaire">
            <input type="hidden" name="menu_id" value="<?php echo $menu['id']; ?>">

            <?php foreach (Plats::TYPES as $type) { ?>
                <fieldset>
                    <legend><?php echo $type; ?>s</legend>
                    <?php foreach ($plats as $un_plat) { ?>
                        <?php if ($un_plat['type_plat'] == $type) { ?>
                            <div class="ligne-plat-menu">
                                <label class="choix-defaut">
                                    <input type="radio" name="defauts[<?php echo $type; ?>]" value="<?php echo $un_plat['id']; ?>" <?php if (in_array($un_plat['id'], $ids_defauts)) { echo 'checked'; } ?>>
                                    par défaut<span class="texte-cache"> : <?php echo htmlspecialchars($un_plat['titre_plat']); ?></span>
                                </label>
                                <label>
                                    <input type="checkbox" name="proposes[]" value="<?php echo $un_plat['id']; ?>" <?php if (in_array($un_plat['id'], $ids_proposes)) { echo 'checked'; } ?>>
                                    <?php echo htmlspecialchars($un_plat['titre_plat']); ?>
                                </label>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </fieldset>
            <?php } ?>

            <div class="actions-horaires">
                <button type="submit" class="btn-valider">Enregistrer</button>
                <a href="modifier_menu.php?id=<?php echo $menu['id']; ?>" class="btn-refuser">Annuler</a>
            </div>
        </form>
    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>