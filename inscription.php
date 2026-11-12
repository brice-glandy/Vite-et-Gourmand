<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// On récupère les erreurs et la saisie laissées par traitement_inscription.php, puis on les efface
$erreurs = [];
$saisie = [];
if (isset($_SESSION['erreurs_inscription'])) {
    $erreurs = $_SESSION['erreurs_inscription'];
    unset($_SESSION['erreurs_inscription']);
}
if (isset($_SESSION['saisie_inscription'])) {
    $saisie = $_SESSION['saisie_inscription'];
    unset($_SESSION['saisie_inscription']);
}

// Petite fonction : renvoie la valeur déjà saisie d'un champ, sécurisée pour l'affichage
function valeurSaisie($saisie, $champ) {
    if (isset($saisie[$champ])) {
        return htmlspecialchars($saisie[$champ]);
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <main class="page-connexion">
        <h1>Créer un compte</h1>
<?php if (count($erreurs) > 0) { ?>
    <div class="message-erreur" role="alert">
        <p>Le compte n'a pas pu être créé :</p>
        <ul>
            <?php foreach ($erreurs as $erreur) { ?>
                <li><?php echo htmlspecialchars($erreur); ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>
        <form action="traitement_inscription.php" method="POST" class="formulaire-connexion">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" autocomplete="family-name" value="<?php echo valeurSaisie($saisie, 'nom'); ?>"required>

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" autocomplete="given-name" value="<?php echo valeurSaisie($saisie, 'prenom'); ?>" required>

            <label for="telephone">Téléphone mobile (GSM)</label>
            <input type="tel" id="telephone" name="telephone" autocomplete="tel" value="<?php echo valeurSaisie($saisie, 'telephone'); ?>" required>

            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" autocomplete="email" value="<?php echo valeurSaisie($saisie, 'email'); ?>" required>

            <label for="adresse_postale">Adresse postale</label>
            <input type="text" id="adresse_postale" name="adresse_postale" autocomplete="street-address" value="<?php echo valeurSaisie($saisie, 'adresse_postale'); ?>" required>

            <label for="ville">Ville</label>
            <input type="text" id="ville" name="ville" autocomplete="address-level2" value="<?php echo valeurSaisie($saisie, 'ville'); ?>" required>

            <label for="mot_de_passe">Mot de passe</label>
            <p id="aide-mot-de-passe" class="aide-champ">10 caractères minimum, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial.</p>
            <div class="champ-mot-de-passe">
                <input type="password" id="mot_de_passe" name="mot_de_passe" autocomplete="new-password" minlength="10" aria-describedby="aide-mot-de-passe" required>
                <button type="button" class="btn-oeil" aria-label="Afficher le mot de passe" aria-pressed="false">👁️</button>
            </div>

            <label for="confirmation">Confirmer le mot de passe</label>
            <div class="champ-mot-de-passe">
                <input type="password" id="confirmation" name="confirmation" autocomplete="new-password" required>
                <button type="button" class="btn-oeil" aria-label="Afficher le mot de passe" aria-pressed="false">👁️</button>
            </div>

            <label class="case-rgpd">
                <input type="checkbox" name="rgpd" value="1" required>
                <span>J'accepte que mes données soient utilisées pour gérer mon compte et mes commandes, conformément aux <a href="mentions_legales.php">mentions légales</a>.</span>
            </label>

            <button type="submit" class="btn-valider">Créer mon compte</button>
        </form>

        <p class="texte-inscription">Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
    </main>

    <?php include_once 'includes/footer.php'; ?>
    <script src="js/mot_de_passe.js"></script>
</body>
</html>