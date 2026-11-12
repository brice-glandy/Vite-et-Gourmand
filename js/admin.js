// --- GESTION DU MENU BURGER MOBILE ---
const boutonBurger = document.querySelector('.menu-burger');
const menuNavigation = document.querySelector('.navigation-principale');

boutonBurger.addEventListener('click', function() {
    menuNavigation.classList.toggle('actif');
});
// -------------------------------------
// 1. Récupération des éléments HTML
// On sélectionne tous les liens du menu SAUF le bouton de déconnexion
const liensMenu = document.querySelectorAll('.menu-lateral a:not(.btn-deconnexion)');
const sectionsAdmin = document.querySelectorAll('.section-admin');

// 2. La fonction qui gère le changement d'onglet
function changerRubrique(event) {
    // Empêche le navigateur de faire un saut brusque vers l'ancre HTML
    event.preventDefault(); 

    // On récupère l'identifiant de la cible (exemple : "#commandes")
    const identifiantCible = this.getAttribute('href');

    // Étape A : On nettoie l'écran (on cache tout)
    sectionsAdmin.forEach(section => {
        section.style.display = 'none';
    });
    
    // Étape B : On éteint tous les boutons du menu
    liensMenu.forEach(lien => {
        lien.classList.remove('actif');
    });

    // Étape C : On allume uniquement ce qui a été cliqué
    document.querySelector(identifiantCible).style.display = 'block';
    this.classList.add('actif');
        // On écrit la rubrique dans l'adresse, pour la retrouver après un F5
    history.replaceState(null, '', identifiantCible);
}

// 3. Mise sur écoute de tous les liens du menu
liensMenu.forEach(lien => {
    lien.addEventListener('click', changerRubrique);
});

// 4. Initialisation : quelle rubrique afficher au chargement ?
// On lit l'ancre de l'adresse (exemple : "#horaires", ou "" s'il n'y en a pas)
let rubriqueDemandee = window.location.hash;
let sectionTrouvee = false;

// On parcourt toutes les rubriques : on affiche celle demandée, on cache les autres
sectionsAdmin.forEach(section => {
    if ('#' + section.id === rubriqueDemandee) {
        section.style.display = 'block';
        sectionTrouvee = true;
    } else {
        section.style.display = 'none';
    }
});

// Si l'ancre est absente ou inconnue, on affiche la première rubrique disponible
// (le tableau de bord pour l'administrateur, les commandes pour un employé)
if (!sectionTrouvee) {
    rubriqueDemandee = '#' + sectionsAdmin[0].id;
    sectionsAdmin[0].style.display = 'block';
}

// On allume dans le menu le lien qui correspond à la rubrique affichée
liensMenu.forEach(lien => {
    lien.classList.remove('actif');
    if (lien.getAttribute('href') === rubriqueDemandee) {
        lien.classList.add('actif');
    }
});
// --- GÉNÉRATION DU GRAPHIQUE DES STATISTIQUES ---

// --- GÉNÉRATION ET MISE À JOUR DU GRAPHIQUE ---

const conteneurGraphique = document.getElementById('graphique-ca');
let graphiqueCommandes; // Variable qui va stocker notre instance Chart.js

// 1. Notre base de données fictive pour le front-end
const baseDonneesMenus = {
    'burger': { titre: 'Burger Artisan', commandes: 45 },
    'brunch': { titre: 'Brunch Complet', commandes: 32 },
    'tradition': { titre: 'Tradition Française', commandes: 28 },
    'oceane': { titre: 'Fraicheur Océane', commandes: 15 },
    'vegetal': { titre: 'Plaisir Végétal', commandes: 10 },
    'italienne': { titre: 'Evasion Italienne', commandes: 22 }
};

if (conteneurGraphique) {
    // 2. Initialisation du graphique à vide
    graphiqueCommandes = new Chart(conteneurGraphique, {
        type: 'bar',
        data: {
            labels: [], // Sera rempli par la fonction
            datasets: [{
                label: 'Nombre de commandes',
                data: [], // Sera rempli par la fonction
                backgroundColor: '#4A7B59',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }, // On cache la légende inutile ici
            scales: { y: { beginAtZero: true, ticks: { stepSize: 10 } } }
        }
    });

    const checkboxesMenus = document.querySelectorAll('input[name="filtre-menu"]');
    const checkboxTous = document.querySelector('input[value="tous"]');

    // 3. Fonction centrale qui met à jour le dessin
    function actualiserGraphique() {
        const nouveauxLabels = [];
        const nouvellesDonnees = [];

        // On boucle sur toutes les cases, et on lit celles qui sont cochées
        checkboxesMenus.forEach(caseCochee => {
            if (caseCochee.checked) {
                let cle = caseCochee.value;
                // Si la clé existe dans notre fausse base de données
                if (baseDonneesMenus[cle]) {
                    nouveauxLabels.push(baseDonneesMenus[cle].titre);
                    nouvellesDonnees.push(baseDonneesMenus[cle].commandes);
                }
            }
        });

        // On injecte les nouvelles données dans le graphique et on lance l'animation
        graphiqueCommandes.data.labels = nouveauxLabels;
        graphiqueCommandes.data.datasets[0].data = nouvellesDonnees;
        graphiqueCommandes.update();
    }

    // 4. Gestion intelligente de la case "Tous les menus"
    checkboxesMenus.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            
            // Si l'utilisateur clique spécifiquement sur "Tous les menus"
            if (this.value === 'tous') {
                let etat = this.checked;
                checkboxesMenus.forEach(cb => cb.checked = etat);
            } 
            // Si l'utilisateur décoche un menu classique, on décoche "Tous" automatiquement
            else if (!this.checked && checkboxTous) {
                checkboxTous.checked = false;
            }

            // On redessine le graphique après chaque clic
            actualiserGraphique();
        });
    });

    // 5. Premier affichage au chargement de la page
    actualiserGraphique();
}
// --- MODÉRATION DES AVIS AVEC FETCH ---

// 1. On récupère tous les boutons Valider et Refuser des cartes d'avis
const boutonsAvis = document.querySelectorAll('.actions-avis button');

// 2. La fonction appelée à chaque clic sur l'un de ces boutons
async function changerStatutAvis() {
    // On lit les informations rangées dans les attributs data-* du bouton cliqué
    const idAvis = this.dataset.id;
    const nouveauStatut = this.dataset.statut;

    // On prépare les données à envoyer, comme un formulaire invisible
    const donnees = new FormData();
    donnees.append('id', idAvis);
    donnees.append('statut', nouveauStatut);

    try {
        // On envoie la demande au serveur et on attend sa réponse
        const reponse = await fetch('traitement_avis.php', {
            method: 'POST',
            body: donnees
        });

        // On transforme la réponse JSON en objet JavaScript
        const resultat = await reponse.json();

        if (resultat.succes) {
            // On retrouve le badge de la bonne carte grâce à son id (ex : #avis-5)
            const badge = document.querySelector('#avis-' + idAvis + ' .badge-statut');

            // On met à jour le texte du badge
            badge.textContent = resultat.statut;

            // On retire l'ancienne couleur, puis on met la nouvelle
            badge.classList.remove('statut-valide', 'statut-attente', 'statut-refuse');
            if (resultat.statut === 'validé') {
                badge.classList.add('statut-valide');
            } else {
                badge.classList.add('statut-refuse');
            }
        } else {
            // Le serveur a refusé la demande : on affiche son message
            alert('Erreur : ' + resultat.message);
        }
    } catch (erreur) {
        // Le serveur n'a pas pu être contacté du tout
        alert('Impossible de contacter le serveur. Réessayez plus tard.');
    }
}

// 3. Mise sur écoute de chaque bouton
boutonsAvis.forEach(bouton => {
    bouton.addEventListener('click', changerStatutAvis);
});