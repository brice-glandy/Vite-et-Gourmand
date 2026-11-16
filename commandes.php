<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

// Page réservée aux clients connectés (rôle utilisateur)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'utilisateur') {
    header('Location: connexion.php');
    exit;
}

// Les informations du client, lues dans son compte
$sql = "SELECT nom, prenom, email, telephone FROM utilisateur WHERE id = :id";
$requete = $pdo->prepare($sql);
$requete->execute(['id' => $_SESSION['utilisateur_id']]);
$client = $requete->fetch(PDO::FETCH_ASSOC);
// La liste des menus, lue en base
require_once 'includes/Menus.php';
$modele_menus = new Menus($pdo);
$menus = $modele_menus->trouver_tous();

// Le menu choisi sur la page détail (commandes.php?menu=3), 0 si aucun
$menu_choisi = 0;
if (isset($_GET['menu']) && ctype_digit($_GET['menu'])) {
    $menu_choisi = (int) $_GET['menu'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande</title>
    <link rel="stylesheet" href="css/style.css">
  </head>
<body>
   <?php include_once 'includes/header.php'; ?>  
    <main class="page-commande">
    <h1>Détails de votre commande</h1>
        <form action="#" method="POST" id="formulaire-commande">
            <div class="colonne-gauche">
                <div class="champ-saisie">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($client['nom']); ?>" readonly>
                </div>
                <div class="champ-saisie">
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($client['prenom']); ?>" readonly>
                </div>
                <div class="champ-saisie">
                    <label for="email">Adresse mail :</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>" readonly>
                </div>
                <div class="champ-saisie">
                    <label for="telephone">Numéro de téléphone :</label>
                    <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($client['telephone']); ?>" readonly>
                </div>
               
            <div class="champ-choix-menu">
            <p>Choix du menu :</p>
            <div class="options-menu">
                <?php foreach ($menus as $un_menu) { ?>
                    <label>
                        <input type="radio" name="menu" value="<?php echo $un_menu['id']; ?>"
                            data-prix="<?php echo $un_menu['prix_par_personne']; ?>"
                            data-minimum="<?php echo $un_menu['nombre_personne_minimum']; ?>"
                            <?php if ($un_menu['id'] == $menu_choisi) { echo 'checked'; } ?>
                            <?php if ($un_menu['quantite_restante'] <= 0) { echo 'disabled'; } ?>
                            required>
                        <?php echo htmlspecialchars($un_menu['titre']); ?>
                        (<?php echo number_format($un_menu['prix_par_personne'], 2, ',', ' '); ?> € / pers., <?php echo $un_menu['nombre_personne_minimum']; ?> pers. minimum)
                    </label>
                <?php } ?>
            </div>
                <div class="champ-saisie">
                    <label for="nombre-personnes">Nombre de personnes :</label>
                    <input type="number" id="nombre-personnes" name="nombre-personnes" min="1" required>
                </div>
            </div>
        </div>
            <div class="colonne-droite">
                 <div class="champ-saisie">
                    <label for="date-prestation">Date de la prestation :</label>
                    <input type="date" id="date-prestation" name="date-prestation" required>
                </div>
                <div class="champ-saisie">
                    <label for="heure-souhaitee">Heure souhaitée :</label>
                    <input type="time" id="heure-souhaitee" name="heure-souhaitee" required>
                </div>
                <div class="champ-saisie">
                    <label for="adresse">Adresse de la prestation :</label>
                    <input type="text" id="adresse" name="adresse" required>
                </div>
                <div class="champ-saisie">
                    <label for="complement">Complément d'adresse :</label>
                    <input type="text" id="complement" name="complement">
                </div>
                <div class="champ-saisie">
                    <label for="code-postal">Code postal :</label>
                    <input type="text" id="code-postal" name="code-postal" required>
                </div>
                <div class="champ-saisie">
                    <label for="ville">Ville / commune :</label>
                    <input type="text" id="ville" name="ville" required>
                </div>
                <div class="champ-saisie" id="bloc-distance" style="display: none;">
                    <label for="distance">Distance depuis Bordeaux (en km) :</label>
                    <input type="number" id="distance" name="distance" min="1" step="1">
                </div>
            <div class="zone-recap">
            <p class="titre-recap">Votre commande :</p>
                <div class="ligne-recap">
                    <label for="prix-unitaire">Prix du menu par personne :</label>
                    <input type="text" id="prix-unitaire" name="prix-unitaire" readonly value="0 €">
                </div>
                <div class="ligne-recap">
                    <label for="frais-livraison">Frais de livraison :</label>
                    <input type="text" id="frais-livraison" name="frais-livraison" readonly value="0 €">
                </div>
                <div class="ligne-recap">
                    <label for="reduction">Réduction :</label>
                    <input type="text" id="reduction" name="reduction" readonly value="0 €">
                </div>
                <div class="ligne-recap total-recap">
                    <label for="total">Total :</label>
                    <input type="text" id="total" name="total" readonly value="0 €">
                </div>
            </div>
           
        <button type="submit" class="bouton-payer">Payer</button>
        </div>
    </form>
    </main>
   <?php include_once 'includes/footer.php'; ?>
<script src="js/commandes.js"></script>
</body>
</html>