<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions légales - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <main class="page-legale">
        <h1>Mentions légales</h1>

        <p class="avertissement-examen">Ce site a été réalisé dans le cadre d'un examen (ECF du titre professionnel Développeur Web et Web Mobile, Studi). L'entreprise Vite &amp; Gourmand et ses coordonnées sont fictives. Aucune commande réelle n'est traitée.</p>

        <h2>Éditeur du site</h2>
        <p>Vite &amp; Gourmand, traiteur événementiel<br>
        18 Avenue Victor Hugo, 33000 Bordeaux<br>
        Téléphone : 06 12 34 56 78<br>
        Email : contact@viteetgourmand.fr<br>
        Directeurs de la publication : Julie et José, gérants.</p>

        <h2>Hébergement</h2>
        <p>Le site est hébergé par alwaysdata, SARL dont le siège social est situé 91 rue du Faubourg Saint-Honoré, 75008 Paris (téléphone : 01 84 16 23 49).<br>
        Les statistiques de commandes sont stockées sur MongoDB Atlas, dans un centre de données situé à Paris.</p>

        <h2>Données personnelles</h2>
        <p>Lors de la création d'un compte et d'une commande, nous enregistrons vos nom, prénom, adresse email, numéro de téléphone, adresse postale et le détail de vos commandes. Ces données servent uniquement à gérer votre compte, préparer et livrer vos commandes, et vous informer de leur suivi.</p>
        <p>Votre mot de passe n'est jamais stocké en clair : seule une empreinte chiffrée est conservée.</p>
        <p>Conformément au Règlement général sur la protection des données (RGPD), vous pouvez consulter et modifier vos informations depuis votre espace client. Pour toute autre demande (copie ou suppression de vos données), écrivez-nous depuis la <a href="contacts.php">page Contact</a>.</p>

        <h2>Cookies</h2>
        <p>Le site utilise un seul cookie : le cookie de session, indispensable pour rester connecté. Il est supprimé à la fermeture du navigateur. Aucun cookie publicitaire ou de mesure d'audience n'est utilisé.</p>

        <h2>Crédits des photos</h2>
        <p>Photos de couverture des menus : Unsplash (licence Unsplash).<br>
        Photos des galeries : Pexels (licence Pexels) — Valeria Boltneva, Sara Free, Casa Lalla Takerkoust, Vincent Rivaud, Katrin Bolovtsova, Maria Orlova, Nadin Sh, Daka, Airam Dato-on.</p>
    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>