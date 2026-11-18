<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
// Après une erreur, on remet ce que le visiteur avait tapé
$saisie = ['titre' => '', 'description' => '', 'email' => ''];
if (isset($_SESSION['saisie_contact'])) {
    $saisie = $_SESSION['saisie_contact'];
    unset($_SESSION['saisie_contact']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <main class="page-modifier-horaire">
        <h1>Nous contacter</h1>
        <p>Une question, une demande particulière ? Écrivez-nous : nous vous répondrons par mail.</p>

        <?php if (isset($_SESSION['message_contact'])) { ?>
            <p class="message-succes" role="status"><?php echo htmlspecialchars($_SESSION['message_contact']); ?></p>
            <?php unset($_SESSION['message_contact']); ?>
        <?php } ?>

        <?php if (isset($_SESSION['erreurs_contact'])) { ?>
            <div class="message-erreur" role="alert">
                <ul>
                    <?php foreach ($_SESSION['erreurs_contact'] as $erreur) { ?>
                        <li><?php echo htmlspecialchars($erreur); ?></li>
                    <?php } ?>
                </ul>
            </div>
            <?php unset($_SESSION['erreurs_contact']); ?>
        <?php } ?>

        <form action="traitement_contact.php" method="POST" class="formulaire-horaire">
            <label for="titre">Titre de votre demande</label>
            <input type="text" id="titre" name="titre" maxlength="100" value="<?php echo htmlspecialchars($saisie['titre']); ?>" required>

            <label for="description">Votre message</label>
            <textarea id="description" name="description" rows="6" maxlength="2000" required><?php echo htmlspecialchars($saisie['description']); ?></textarea>

            <label for="email">Votre adresse email (pour vous répondre)</label>
            <input type="email" id="email" name="email" autocomplete="email" maxlength="255" value="<?php echo htmlspecialchars($saisie['email']); ?>" required>

            <div class="actions-horaires">
                <button type="submit" class="btn-valider">Envoyer</button>
            </div>
        </form>
    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>