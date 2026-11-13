<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
// Les avis validés, du plus récent au plus ancien (6 au maximum)
$sql = "SELECT avis.note, avis.commentaire, avis.date_publication, utilisateur.prenom, utilisateur.nom
        FROM avis
        JOIN utilisateur ON avis.utilisateur_id = utilisateur.id
        WHERE avis.statut = 'validé'
        ORDER BY avis.date_publication DESC
        LIMIT 6";
$requete = $pdo->query($sql);
$avis_valides = $requete->fetchAll(PDO::FETCH_ASSOC);
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
            <?php if (count($avis_valides) == 0) { ?>
                <p>Aucun avis pour le moment.</p>
            <?php } else { ?>
                <div class="grille-avis-accueil">
                    <?php foreach ($avis_valides as $un_avis) { ?>
                        <article class="carte-avis-accueil">
                            <p class="note-avis-accueil">Note : <?php echo $un_avis['note']; ?> sur 5</p>
                            <p class="texte-avis-accueil">« <?php echo htmlspecialchars($un_avis['commentaire']); ?> »</p>
                            <p class="auteur-avis-accueil"><?php echo htmlspecialchars($un_avis['prenom'] . ' ' . mb_substr($un_avis['nom'], 0, 1) . '.'); ?> – <?php echo date('d/m/Y', strtotime($un_avis['date_publication'])); ?></p>
                        </article>
                    <?php } ?>
                </div>
            <?php } ?>  
        </section>

    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>