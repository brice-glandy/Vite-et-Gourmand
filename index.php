<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Vite & Gourmand, traiteur à Bordeaux</title>
    <meta name="description" content="Vite & Gourmand, traiteur à Bordeaux depuis 25 ans : des menus pour vos repas de fêtes et vos événements, livrés chez vous.">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>

    <main class="page-accueil">

        <!-- 1. Présentation de l'entreprise -->
        <section class="accueil-presentation" aria-labelledby="titre-presentation">
            <h1 id="titre-presentation">Vite & Gourmand, traiteur à Bordeaux depuis 25 ans</h1>
            <p>Noël, Pâques, anniversaire ou simple repas entre amis : Julie et José préparent pour vous des menus faits maison, adaptés à chaque occasion et livrés à l'heure de votre choix.</p>
            <a href="menus.php" class="bouton-principal">Découvrir nos menus</a>
        </section>

        <!-- 2. Le professionnalisme de l'équipe -->
        <section class="accueil-equipe" aria-labelledby="titre-equipe">
            <h2 id="titre-equipe">Une équipe passionnée à votre service</h2>
            <p>Depuis 25 ans, Julie et José mettent leur savoir-faire au service de vos événements, de la commande jusqu'à la livraison.</p>
            <ul class="liste-engagements">
                <li><strong>Des produits frais</strong>, sélectionnés auprès de producteurs locaux.</li>
                <li><strong>Un menu pour chaque occasion</strong> : Noël, Pâques, repas classique ou événement.</li>
                <li><strong>Le respect de vos besoins</strong> : menus végétariens et vegan, allergènes indiqués pour chaque plat.</li>
                <li><strong>Une livraison soignée</strong> à Bordeaux et dans ses environs.</li>
            </ul>
        </section>

        <!-- 3. Les avis clients validés (écrits en dur pour l'instant, lus en base au bloc 3) -->
        <section class="accueil-avis" aria-labelledby="titre-avis">
            <h2 id="titre-avis">Ils nous ont fait confiance</h2>
            <div class="grille-avis-accueil">
                <article class="carte-avis-accueil">
                    <p class="note-avis-accueil">Note : 5 sur 5</p>
                    <p class="texte-avis-accueil">« Le menu Burger Artisan a fait l'unanimité auprès de tous nos invités. »</p>
                    <p class="auteur-avis-accueil">Sophie L. – 15/09/2026</p>
                </article>
            </div>
        </section>

    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>