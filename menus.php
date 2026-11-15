<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
// Tous les menus, dans l'ordre de leur création
$sql = "SELECT id, titre, description, nombre_personne_minimum, prix_par_personne, image_url
        FROM menu
        ORDER BY id";
$requete = $pdo->query($sql);
$menus = $requete->fetchAll(PDO::FETCH_ASSOC);
// Les thèmes et les régimes, pour remplir les listes des filtres
$requete = $pdo->query("SELECT id, libelle FROM theme ORDER BY libelle");
$themes = $requete->fetchAll(PDO::FETCH_ASSOC);

$requete = $pdo->query("SELECT id, libelle FROM regime ORDER BY libelle");
$regimes = $requete->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Menus - Vite & Gourmand</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
   <?php include_once 'includes/header.php'; ?>
    <main class="conteneur-principal">
  <aside class="zone-filtres">
    <form action="#" method="GET">
        <fieldset class="groupe-filtre">
            <legend>Filtre par Prix (€ / personne)</legend>
            <div class="filtre-prix-inputs">
                <input type="number" id="prix-min" name="prix_min" placeholder="Min" min="0" aria-label="Prix minimum par personne">
                <input type="number" id="prix-max" name="prix_max" placeholder="Max" min="0" aria-label="Prix maximum par personne">
            </div>
        </fieldset> 

        <div class="groupe-filtre">
            <label for="filtre-theme">Filtre par Thème</label>
            <select id="filtre-theme" name="theme">
                <option value="">Tous</option>
                <?php foreach ($themes as $un_theme) { ?>
                    <option value="<?php echo $un_theme['id']; ?>"><?php echo htmlspecialchars($un_theme['libelle']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="groupe-filtre">
            <label for="filtre-regime">Filtre par Régime</label>
            <select id="filtre-regime" name="regime">
                <option value="">Tous</option>
                <?php foreach ($regimes as $un_regime) { ?>
                    <option value="<?php echo $un_regime['id']; ?>"><?php echo htmlspecialchars($un_regime['libelle']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="groupe-filtre">
            <label for="filtre-personnes">Nombre de personnes minimum</label>
            <input type="number" id="filtre-personnes" name="personnes" min="1">
        </div>

        <div class="actions-filtres">
            <button type="submit" class="btn-valider">Valider</button>
            <button type="reset" class="btn-effacer">Effacer</button>
        </div>
    </form>
</aside>
    
    <section class="grille-menus" aria-labelledby="titre-menus">
        <h1 id="titre-menus">Nos menus</h1>
       <?php foreach ($menus as $un_menu) { ?>
    <article class="carte-menu">
        <div class="carte-contenu">
            <h2><?php echo htmlspecialchars($un_menu['titre']); ?></h2>
            <ul class="carte-caracteristiques">
                <li><?php echo $un_menu['nombre_personne_minimum']; ?> personnes minimum</li>
                <li><?php echo number_format($un_menu['prix_par_personne'], 2, ',', ' '); ?> € / personne</li>
            </ul>
            <p class="carte-description"><?php echo htmlspecialchars($un_menu['description']); ?></p>
        </div>
        <div class="carte-visuel">
            <img src="<?php echo htmlspecialchars($un_menu['image_url']); ?>" alt="Menu <?php echo htmlspecialchars($un_menu['titre']); ?>">
            <a href="#" class="btn-detail">VOIR LE DETAIL</a>
        </div>
    </article>
<?php } ?>
    </section>
</main>
<?php include_once 'includes/footer.php'; ?>
</body>
</html>