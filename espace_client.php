<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/Commandes.php';

// Espace réservé aux clients connectés
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'utilisateur') {
    header('Location: connexion.php');
    exit;
}

// Les commandes de CE client (l'id vient de la session, jamais de l'adresse)
$modele_commandes = new Commandes($pdo);
$commandes = $modele_commandes->trouver_par_client($_SESSION['utilisateur_id']);
// Les informations du compte, pour préremplir le formulaire
$requete = $pdo->prepare("SELECT nom, prenom, email, telephone, adresse_postale, ville FROM utilisateur WHERE id = :id");
$requete->execute(['id' => $_SESSION['utilisateur_id']]);
$client = $requete->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include_once 'includes/header.php'; ?>
<main class="page-espace-client">
    <h1>Mon espace</h1>

    <section aria-labelledby="titre-commandes">
        <h2 id="titre-commandes">Mes commandes</h2>

        <?php if (count($commandes) == 0) { ?>
            <p>Vous n'avez pas encore passé de commande.</p>
            <a href="menus.php" class="bouton-principal">Découvrir nos menus</a>
        <?php } ?>

        <?php foreach ($commandes as $une_commande) { ?>
            <article class="carte-commande">
                <h3><?php echo htmlspecialchars($une_commande['numero_commande']); ?> - <?php echo htmlspecialchars($une_commande['menu_titre']); ?></h3>
                <p><span class="badge-statut statut-commande"><?php echo htmlspecialchars($une_commande['statut']); ?></span></p>
                <ul>
                    <li>Commandée le <?php echo date('d/m/Y à H\hi', strtotime($une_commande['date_commande'])); ?></li>
                    <li>Prestation le <?php echo date('d/m/Y', strtotime($une_commande['date_prestation'])); ?> à <?php echo substr($une_commande['heure_livraison'], 0, 5); ?></li>
                    <li>Adresse : <?php echo htmlspecialchars($une_commande['adresse_livraison'] . ' ' . $une_commande['ville_livraison']); ?></li>
                    <li>Nombre de personnes : <?php echo $une_commande['nombre_personne']; ?></li>
                    <li>Prix du menu : <?php echo number_format($une_commande['prix_menu'], 2, ',', ' '); ?> €</li>
                    <li>Livraison : <?php echo number_format($une_commande['prix_livraison'], 2, ',', ' '); ?> €</li>
                    <li><strong>Total : <?php echo number_format($une_commande['prix_total'], 2, ',', ' '); ?> €</strong></li>
                </ul>
            </article>
        <?php } ?>
    </section>
        <section aria-labelledby="titre-infos">
        <h2 id="titre-infos">Mes informations</h2>

        <?php if (isset($_SESSION['message_profil'])) { ?>
            <p class="message-succes" role="status"><?php echo htmlspecialchars($_SESSION['message_profil']); ?></p>
            <?php unset($_SESSION['message_profil']); ?>
        <?php } ?>
        <?php if (isset($_SESSION['erreurs_profil'])) { ?>
            <div class="message-erreur" role="alert">
                <ul>
                    <?php foreach ($_SESSION['erreurs_profil'] as $erreur) { ?>
                        <li><?php echo htmlspecialchars($erreur); ?></li>
                    <?php } ?>
                </ul>
            </div>
            <?php unset($_SESSION['erreurs_profil']); ?>
        <?php } ?>

        <form action="traitement_profil.php" method="POST" class="formulaire-connexion">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" autocomplete="family-name" value="<?php echo htmlspecialchars($client['nom']); ?>" required>

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" autocomplete="given-name" value="<?php echo htmlspecialchars($client['prenom']); ?>" required>

            <label for="telephone">Téléphone mobile (GSM)</label>
            <input type="tel" id="telephone" name="telephone" autocomplete="tel" value="<?php echo htmlspecialchars($client['telephone']); ?>" required>

            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" autocomplete="email" value="<?php echo htmlspecialchars($client['email']); ?>" required>

            <label for="adresse_postale">Adresse postale</label>
            <input type="text" id="adresse_postale" name="adresse_postale" autocomplete="street-address" value="<?php echo htmlspecialchars($client['adresse_postale']); ?>" required>

            <label for="ville">Ville</label>
            <input type="text" id="ville" name="ville" autocomplete="address-level2" value="<?php echo htmlspecialchars($client['ville']); ?>" required>

            <button type="submit" class="btn-valider">Enregistrer mes informations</button>
        </form>
    </section>
</main>
<?php include_once 'includes/footer.php'; ?>
</body>
</html>