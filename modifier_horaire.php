<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';

// 1. Vérifier qu'un id est bien présent dans l'adresse
if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}
$id = (int) $_GET['id'];

// 2. Chercher ce jour précis dans la base
$sql = "SELECT id, jour,
        TIME_FORMAT(ouverture_matin, '%H:%i') AS ouverture_matin,
        TIME_FORMAT(fermeture_matin, '%H:%i') AS fermeture_matin,
        TIME_FORMAT(ouverture_soir, '%H:%i') AS ouverture_soir,
        TIME_FORMAT(fermeture_soir, '%H:%i') AS fermeture_soir,
        est_ferme
        FROM horaires WHERE id = :id";
$requete = $pdo->prepare($sql);
$requete->execute(['id' => $id]);
$horaire = $requete->fetch(PDO::FETCH_ASSOC);

// 3. Si ce jour n'existe pas, retour à l'admin
if (!$horaire) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier les horaires</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <main class="page-modifier-horaire">
        <h1>Modifier les horaires du <?php echo htmlspecialchars($horaire['jour']); ?></h1>
<?php if (isset($_GET['erreur'])) { ?>
    <p class="message-erreur" role="alert">Horaires incohérents : chaque ouverture doit avoir une fermeture, et la fermeture doit être après l'ouverture.</p>
<?php } ?>
        <form action="traitement_horaire.php" method="POST" class="formulaire-horaire">
            <input type="hidden" name="id" value="<?php echo $horaire['id']; ?>">

            <fieldset>
                <legend>Matin</legend>
                <label for="ouverture_matin">Ouverture</label>
                <input type="time" id="ouverture_matin" name="ouverture_matin" value="<?php echo $horaire['ouverture_matin']; ?>">
                <label for="fermeture_matin">Fermeture</label>
                <input type="time" id="fermeture_matin" name="fermeture_matin" value="<?php echo $horaire['fermeture_matin']; ?>">
                <label>
                    <input type="checkbox" name="matin_ferme" value="1" <?php if ($horaire['ouverture_matin'] == null) { echo 'checked'; } ?>>
                    Fermé le matin
                </label>
            </fieldset>

            <fieldset>
                <legend>Soir</legend>
                <label for="ouverture_soir">Ouverture</label>
                <input type="time" id="ouverture_soir" name="ouverture_soir" value="<?php echo $horaire['ouverture_soir']; ?>">
                <label for="fermeture_soir">Fermeture</label>
                <input type="time" id="fermeture_soir" name="fermeture_soir" value="<?php echo $horaire['fermeture_soir']; ?>">
                <label>
                <input type="checkbox" name="soir_ferme" value="1" <?php if ($horaire['ouverture_soir'] == null) { echo 'checked'; } ?>>
                Fermé le soir
                </label>
            </fieldset>

            <label>
                <input type="checkbox" name="est_ferme" value="1" <?php if ($horaire['est_ferme']) { echo 'checked'; } ?>>
                Fermé toute la journée
            </label>

            <div class="actions-horaires">
                <button type="submit" class="btn-valider">Enregistrer</button>
                <a href="admin.php" class="btn-refuser">Annuler</a>
            </div>
        </form>
    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>