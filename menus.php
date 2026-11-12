<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
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
        <div class="groupe-filtre">
    <label>Filtre par Prix (€)</label>
        <div class="filtre-prix-inputs">
            <input type="number" id="prix-min" name="prix_min" placeholder="Min">
             <input type="number" id="prix-max" name="prix_max" placeholder="Max">
        </div>
    </div>

        <div class="groupe-filtre">
            <label for="filtre-theme">Filtre par Thème</label>
            <select id="filtre-theme" name="theme">
                <option value="">Sélectionner</option>
            </select>
        </div>

        <div class="groupe-filtre">
            <label for="filtre-regime">Filtre par Régime</label>
            <select id="filtre-regime" name="regime">
                <option value="">Sélectionner</option>
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
    
    <section class="grille-menus">
        <article class="carte-menu">
    <div class="carte-contenu">
        <h2>Burger Artisan</h2>
        <ul class="carte-caracteristiques">
            <li>4 personnes minimum</li>
            <li>19 € / personne</li>
        </ul>
        <p class="carte-description">Pain brioché fait maison garni d'un steak boucher, de fromage fondu. Avec ou sans frites.</p>
    </div>
    
    <div class="carte-visuel">
        <img src="images/burger.jpg" alt="Menu Burger Artisan">
        <a href="#" class="btn-detail">VOIR LE DETAIL</a>
    </div>
        </article>
         <article class="carte-menu">
    <div class="carte-contenu">
        <h2>Brunch Complet</h2>
        <ul class="carte-caracteristiques">
            <li>4 personnes minimum</li>
            <li>22 € / personne</li>
        </ul>
        <p class="carte-description">Assortiment sucré-salé mêlant des oeufs brouillés, de l'avocat frais et des pancakes moelleux.</p>
    </div>
    
    <div class="carte-visuel">
        <img src="images/brunch.jpg" alt="Menu Brunch">
        <a href="#" class="btn-detail">VOIR LE DETAIL</a>
    </div>
        </article>
         <article class="carte-menu">
    <div class="carte-contenu">
        <h2>Tradition Française</h2>
        <ul class="carte-caracteristiques">
            <li>8 personnes minimum</li>
            <li>26 € / personne</li>
        </ul>
        <p class="carte-description">Grand classique qui mets à l'honneur le terroir, viande mijotée et légumes de saison.</p>
    </div>
    
    <div class="carte-visuel">
        <img src="images/trad.jpg" alt="Menu Traditionnel">
        <a href="#" class="btn-detail">VOIR LE DETAIL</a>
    </div>
        </article>
         <article class="carte-menu">
    <div class="carte-contenu">
        <h2>Fraîcheur Océane</h2>
        <ul class="carte-caracteristiques">
            <li>4 personnes minimum</li>
            <li>22 € / personne</li>
        </ul>
        <p class="carte-description">Sélection raffinée de poissons grillés et de fruits de mer, et risotto au citron confit.</p>
    </div>
    
    <div class="carte-visuel">
        <img src="images/ocean.jpg" alt="Menu Fraîcheur Océane">
        <a href="#" class="btn-detail">VOIR LE DETAIL</a>
    </div>
        </article>
         <article class="carte-menu">
    <div class="carte-contenu">
        <h2>Plaisir Végétal</h2>
        <ul class="carte-caracteristiques">
            <li>4 personnes minimum</li>
            <li>16 € / personne</li>
        </ul>
        <p class="carte-description">Composition gourmande à base de céréales anciennes, de légumes croquants et de houmous maison.</p>
    </div>
    
    <div class="carte-visuel">
        <img src="images/vegetal.jpg" alt="Menu Plaisir Végétal">
        <a href="#" class="btn-detail">VOIR LE DETAIL</a>
    </div>
        </article>
         <article class="carte-menu">
    <div class="carte-contenu">
        <h2>Evasion Italienne</h2>
        <ul class="carte-caracteristiques">
            <li>8 personnes minimum</li>
            <li>21 € / personne</li>
        </ul>
        <p class="carte-description">Pâtes fraîches enrobées d'une sauce au pesto de basilic et de copeaux de parmesan.</p>
    </div>
    
    <div class="carte-visuel">
        <img src="images/italienne.jpg" alt="Menu Evasion Italienne">
        <a href="#" class="btn-detail">VOIR LE DETAIL</a>
    </div>
        </article>
        <!-- Les cartes des menus viendront ici -->
    </section>
</main>
<?php include_once 'includes/footer.php'; ?>
</body>
</html>