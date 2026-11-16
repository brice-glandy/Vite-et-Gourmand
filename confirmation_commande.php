<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Cette page n'a de sens que juste après une commande
if (!isset($_SESSION['commande_confirmee'])) {
    header('Location: menus.php');
    exit;
}
$numero = $_SESSION['commande_confirmee'];
unset($_SESSION['commande_confirmee']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande enregistrée - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include_once 'includes/header.php'; ?>
<main class="page-confirmation">
    <h1>Merci pour votre commande !</h1>
    <p>Votre commande <strong><?php echo htmlspecialchars($numero); ?></strong> est bien enregistrée.</p>
    <p>Un mail de confirmation vient de vous être envoyé. Notre équipe va maintenant valider votre commande.</p>
    <a href="menus.php" class="bouton-principal">Retour aux menus</a>
</main>
<?php include_once 'includes/footer.php'; ?>
</body>
</html>