<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/Reinitialisations.php';

// Le jeton vient du lien du mail : 64 caractères hexadécimaux, et encore valable
$jeton = (isset($_GET['jeton']) && is_string($_GET['jeton'])) ? $_GET['jeton'] : '';
$jeton_valable = false;
if (strlen($jeton) == 64 && ctype_xdigit($jeton)) {
    $modele_reinit = new Reinitialisations($pdo);
    $jeton_valable = ($modele_reinit->trouver_utilisateur($jeton) !== false);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <main class="page-connexion">
        <h1>Nouveau mot de passe</h1>

        <?php if (!$jeton_valable) { ?>
            <p class="message-erreur" role="alert">Ce lien n'est plus valable.</p>
            <p><a href="mot_de_passe_oublie.php">Demander un nouveau lien</a></p>
        <?php } else { ?>

            <?php if (isset($_SESSION['erreurs_nouveau_mdp'])) { ?>
                <div class="message-erreur" role="alert">
                    <ul>
                        <?php foreach ($_SESSION['erreurs_nouveau_mdp'] as $erreur) { ?>
                            <li><?php echo htmlspecialchars($erreur); ?></li>
                        <?php } ?>
                    </ul>
                </div>
                <?php unset($_SESSION['erreurs_nouveau_mdp']); ?>
            <?php } ?>

            <form action="traitement_nouveau_mot_de_passe.php" method="POST" class="formulaire-connexion">
                <input type="hidden" name="jeton" value="<?php echo htmlspecialchars($jeton); ?>">

                <label for="mot_de_passe">Nouveau mot de passe</label>
                <p id="aide-mot-de-passe" class="aide-champ">10 caractères minimum, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial.</p>
                <div class="champ-mot-de-passe">
                    <input type="password" id="mot_de_passe" name="mot_de_passe" autocomplete="new-password" minlength="10" aria-describedby="aide-mot-de-passe" required>
                    <button type="button" class="btn-oeil" aria-label="Afficher le mot de passe" aria-pressed="false"><svg class="icone-oeil" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/></svg></button>
                </div>

                <label for="confirmation">Confirmer le mot de passe</label>
                <div class="champ-mot-de-passe">
                    <input type="password" id="confirmation" name="confirmation" autocomplete="new-password" required>
                    <button type="button" class="btn-oeil" aria-label="Afficher le mot de passe" aria-pressed="false"><svg class="icone-oeil" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/></svg></button>
                </div>

                <button type="submit" class="btn-valider">Enregistrer le nouveau mot de passe</button>
            </form>
        <?php } ?>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
    <script src="js/mot_de_passe.js"></script>
</body>
</html>