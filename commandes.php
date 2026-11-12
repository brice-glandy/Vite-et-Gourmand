<?php require_once 'includes/db.php'; ?>
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
                    <input type="text" id="nom" name="nom" required>
                </div>
                <div class="champ-saisie">
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>
                <div class="champ-saisie">
                    <label for="email">Adresse mail :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="champ-saisie">
                    <label for="telephone">Numéro de téléphone :</label>
                    <input type="tel" id="telephone" name="telephone" required>
                </div>
               
             <div class="champ-choix-menu">
            <p>Choix du menu :</p>
                <div class="options-menu">
                    <label><input type="radio" name="menu" value="burger" required> Burger Artisan</label>
                    <label><input type="radio" name="menu" value="brunch"> Brunch Complet</label>
                    <label><input type="radio" name="menu" value="tradition"> Tradition Française</label>
                    <label><input type="radio" name="menu" value="oceane"> Fraicheur Océane</label>
                    <label><input type="radio" name="menu" value="vegetal"> Végétal Equilibre</label>
                    <label><input type="radio" name="menu" value="italienne"> Evasion Italienne</label>
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