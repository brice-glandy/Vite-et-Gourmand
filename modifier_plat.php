<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';
require_once 'includes/Plats.php';

$modele_plats = new Plats($pdo);
$allergenes = $modele_plats->trouver_allergenes();

// Les valeurs du formulaire : vides pour un nouveau plat
$plat = [
    'id' => '',
    'titre_plat' => '',
    'type_plat' => ''
];
$allergenes_coches = [];
$titre_page = 'Ajouter un plat';

// Modification : si un numéro est dans l'adresse, on charge ce plat et ses allergènes
if (isset($_GET['id'])) {
    if (!ctype_digit($_GET['id'])) {
        header('Location: admin.php#plats');
        exit;
    }
    $plat_trouve = $modele_plats->trouver_par_id($_GET['id']);
    if (!$plat_trouve) {
        header('Location: admin.php#plats');
        exit;
    }
    $plat = $plat_trouve;
    $allergenes_coches = $modele_plats->trouver_ids_allergenes($plat['id']);
    $titre_page = 'Modifier le plat ' . $plat['titre_plat'];
}

// Après une erreur, on remet ce que l'employé avait saisi
if (isset($_SESSION['saisie_plat'])) {
    $plat = $_SESSION['saisie_plat']['plat'];
    $allergenes_coches = $_SESSION['saisie_plat']['allergenes'];
    unset($_SESSION['saisie_plat']);
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

        <?php if (isset($_SESSION['erreurs_plat'])) { ?>
            <div class="message-erreur" role="alert">
                <ul>
                    <?php foreach ($_SESSION['erreurs_plat'] as $erreur) { ?>
                        <li><?php echo htmlspecialchars($erreur); ?></li>
                    <?php } ?>
                </ul>
            </div>
            <?php unset($_SESSION['erreurs_plat']); ?>
        <?php } ?>

        <form action="traitement_plat.php" method="POST" class="formulaire-horaire">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($plat['id']); ?>">

            <label for="titre_plat">Nom du plat</label>
            <input type="text" id="titre_plat" name="titre_plat" maxlength="100" value="<?php echo htmlspecialchars($plat['titre_plat']); ?>" required>

            <label for="type_plat">Type</label>
            <select id="type_plat" name="type_plat" required>
                <option value="">Choisir un type</option>
                <?php foreach (Plats::TYPES as $type) { ?>
                    <option value="<?php echo $type; ?>" <?php if ($type == $plat['type_plat']) { echo 'selected'; } ?>><?php echo $type; ?></option>
                <?php } ?>
            </select>

            <fieldset>
                <legend>Allergènes (cochez tous ceux que contient le plat)</legend>
                <?php foreach ($allergenes as $un_allergene) { ?>
                    <label>
                        <input type="checkbox" name="allergenes[]" value="<?php echo $un_allergene['id']; ?>" <?php if (in_array($un_allergene['id'], $allergenes_coches)) { echo 'checked'; } ?>>
                        <?php echo htmlspecialchars($un_allergene['libelle']); ?>
                    </label>
                <?php } ?>
            </fieldset>

            <div class="actions-horaires">
                <button type="submit" class="btn-valider">Enregistrer</button>
                <a href="admin.php#plats" class="btn-refuser">Annuler</a>
            </div>
        </form>
    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>