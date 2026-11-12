<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <main class="page-connexion">
        <h1>Connexion</h1>

       <?php if (isset($_GET['erreur'])) { ?>
    <?php if ($_GET['erreur'] == 'inactif') { ?>
        <p class="message-erreur" role="alert">Ce compte a été désactivé. Veuillez contacter l'administrateur.</p>
    <?php } else { ?>
        <p class="message-erreur" role="alert">Email ou mot de passe incorrect.</p>
    <?php } ?>
<?php } ?>

        <form action="traitement_connexion.php" method="POST" class="formulaire-connexion">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" autocomplete="email" required>

            <label for="mot_de_passe">Mot de passe</label>
            <div class="champ-mot-de-passe">
    <input type="password" id="mot_de_passe" name="mot_de_passe" autocomplete="current-password" required>
    <button type="button" class="btn-oeil" aria-label="Afficher le mot de passe" aria-pressed="false">👁️</button>
</div>

            <button type="submit" class="btn-valider">Se connecter</button>
            <a href="#" class="lien-secondaire">Mot de passe oublié ?</a>
        </form>

        <p class="texte-inscription">Pas encore de compte ? <a href="inscription.php">Créer un compte</a></p>
    </main>

    <?php include_once 'includes/footer.php'; ?>
    <script src="js/mot_de_passe.js"></script>
</body>
</html>