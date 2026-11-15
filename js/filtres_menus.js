// Filtres de la page Menus : mise à jour sans recharger la page (AJAX avec fetch)
const formulaireFiltres = document.getElementById('formulaire-filtres');

formulaireFiltres.addEventListener('submit', async function (event) {
    // On empêche le navigateur de recharger la page
    event.preventDefault();

    // On transforme les champs du formulaire en paramètres d'adresse : prix_min=...&theme=...
    const parametres = new URLSearchParams(new FormData(formulaireFiltres));

    // On interroge l'API, puis on lit sa réponse JSON
    const reponse = await fetch('api_menus.php?' + parametres.toString());
    const resultat = await reponse.json();
    console.log(resultat);
});