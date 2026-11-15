// Filtres de la page Menus : mise à jour sans recharger la page (AJAX avec fetch)
const formulaireFiltres = document.getElementById('formulaire-filtres');
// Fabrique la carte d'un menu : même structure que la carte écrite en PHP dans menus.php
function creerCarte(menu) {
    const article = document.createElement('article');
    article.className = 'carte-menu';

    // Partie texte : titre, caractéristiques, description
    const contenu = document.createElement('div');
    contenu.className = 'carte-contenu';

    const titre = document.createElement('h2');
    titre.textContent = menu.titre;

    const liste = document.createElement('ul');
    liste.className = 'carte-caracteristiques';
    const ligneMinimum = document.createElement('li');
    ligneMinimum.textContent = menu.nombre_personne_minimum + ' personnes minimum';
    const lignePrix = document.createElement('li');
    lignePrix.textContent = Number(menu.prix_par_personne).toFixed(2).replace('.', ',') + ' € / personne';
    liste.append(ligneMinimum, lignePrix);

    const description = document.createElement('p');
    description.className = 'carte-description';
    description.textContent = menu.description;

    contenu.append(titre, liste, description);

    // Partie visuelle : image et lien vers le détail
    const visuel = document.createElement('div');
    visuel.className = 'carte-visuel';

    const image = document.createElement('img');
    image.src = menu.image_url;
    image.alt = 'Menu ' + menu.titre;

    const lien = document.createElement('a');
    lien.href = '#';
    lien.className = 'btn-detail';
    lien.textContent = 'Voir le détail';
    const texteCache = document.createElement('span');
    texteCache.className = 'texte-cache';
    texteCache.textContent = ' du menu ' + menu.titre;
    lien.append(texteCache);

    visuel.append(image, lien);
    article.append(contenu, visuel);
    return article;
}
formulaireFiltres.addEventListener('submit', async function (event) {
    // On empêche le navigateur de recharger la page
    event.preventDefault();

    // On transforme les champs du formulaire en paramètres d'adresse : prix_min=...&theme=...
    const parametres = new URLSearchParams(new FormData(formulaireFiltres));

    // On interroge l'API, puis on lit sa réponse JSON
    const reponse = await fetch('api_menus.php?' + parametres.toString());
    const resultat = await reponse.json();
        // On retire les anciennes cartes (le titre « Nos menus » reste en place)
    document.querySelectorAll('.carte-menu').forEach(function (carte) {
        carte.remove();
    });

    // On ajoute une carte par menu reçu
    const grille = document.querySelector('.grille-menus');
    resultat.menus.forEach(function (menu) {
        grille.appendChild(creerCarte(menu));
    });
});