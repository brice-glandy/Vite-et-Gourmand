<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <main class="page-connexion">
        <h1>Mot de passe oublié</h1>

        <?php if (isset($_SESSION['message_oubli'])) { ?>
            <p class="message-succes" role="status"><?php echo htmlspecialchars($_SESSION['message_oubli']); ?></p>
            <?php unset($_SESSION['message_oubli']); ?>
        <?php } ?>
        <?php if (isset($_SESSION['erreur_oubli'])) { ?>
            <p class="message-erreur" role="alert"><?php echo htmlspecialchars($_SESSION['erreur_oubli']); ?></p>
            <?php unset($_SESSION['erreur_oubli']); ?>
        <?php } ?>

        <form action="traitement_mot_de_passe_oublie.php" method="POST" class="formulaire-connexion">
            <p>Indiquez l'adresse email de votre compte : nous vous enverrons un lien pour choisir un nouveau mot de passe.</p>
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" autocomplete="email" required>
            <button type="submit" class="btn-valider">Recevoir le lien</button>
            <a href="connexion.php" class="lien-secondaire">Retour à la connexion</a>
        </form>
    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>