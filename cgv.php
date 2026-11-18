<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conditions générales de vente - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <main class="page-legale">
        <h1>Conditions générales de vente</h1>

        <p class="avertissement-examen">Ce site a été réalisé dans le cadre d'un examen (ECF Studi). L'entreprise Vite &amp; Gourmand est fictive : ces conditions servent de démonstration.</p>

        <h2>1. Objet</h2>
        <p>Les présentes conditions s'appliquent à toutes les commandes de menus passées sur le site de Vite &amp; Gourmand, traiteur à Bordeaux. Passer une commande vaut acceptation de ces conditions.</p>

        <h2>2. Commande</h2>
        <p>La commande se fait depuis la page d'un menu, avec un compte client. Chaque menu indique un nombre minimum de personnes et ses conditions propres (par exemple un délai de commande ou une conservation au frais) : elles sont affichées avant la commande et doivent être respectées.</p>
        <p>Une fois la commande enregistrée, le client reçoit un mail de confirmation.</p>

        <h2>3. Prix</h2>
        <p>Les prix sont indiqués en euros, par personne. Le détail du prix (menu et livraison) est affiché avant la validation.</p>
        <p>Une réduction de 10 % sur le prix du menu est appliquée lorsque la commande compte au moins 5 personnes de plus que le minimum indiqué pour ce menu.</p>
        <p>La livraison est offerte à Bordeaux. En dehors de Bordeaux, elle est facturée 5 €, majorés de 0,59 € par kilomètre parcouru.</p>
        <p>Le site ne propose pas de paiement en ligne.</p>

        <h2>4. Modification et annulation</h2>
        <p>Tant que la commande n'a pas été acceptée par notre équipe, le client peut la modifier (tout sauf le choix du menu) ou l'annuler depuis son espace client.</p>
        <p>Après son acceptation, toute modification ou annulation passe par notre équipe, qui contacte le client par téléphone ou par mail et en indique le motif.</p>

        <h2>5. Suivi de la commande</h2>
        <p>Une fois la commande acceptée, le client suit chaque étape depuis son espace client (acceptée, en préparation, en cours de livraison, livrée, terminée), avec la date et l'heure de chaque étape.</p>

        <h2>6. Prêt de matériel</h2>
        <p>Certaines prestations comprennent le prêt de matériel. Lorsque la commande passe à l'étape « en attente du retour de matériel », le client reçoit un mail l'invitant à prendre contact avec nous pour organiser la restitution.</p>
        <p><strong>Si le matériel n'est pas restitué dans un délai de 10 jours ouvrés, des frais de 600 € seront facturés au client.</strong></p>

        <h2>7. Avis</h2>
        <p>Une fois sa commande terminée, le client peut donner une note et un commentaire. Les avis sont publiés sur la page d'accueil après validation par notre équipe.</p>

        <h2>8. Données personnelles</h2>
        <p>Le traitement des données personnelles est décrit dans les <a href="mentions_legales.php">mentions légales</a>.</p>

        <h2>9. Contact</h2>
        <p>Pour toute question sur une commande : <a href="contacts.php">page Contact</a>, ou par téléphone au 06 12 34 56 78.</p>
    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>