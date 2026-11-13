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
// --- TABLEAU DE BORD : GRAPHIQUE ET CHIFFRE D'AFFAIRES ---

const conteneurGraphique = document.getElementById('graphique-ca');

// Le tableau de bord n'existe que pour l'administrateur : sans lui, on ne fait rien
if (conteneurGraphique) {

    // 1. Les zones de la page que l'on va remplir
    const listeChiffreAffaires = document.getElementById('liste-chiffre-affaires');
    const totalChiffreAffaires = document.getElementById('total-chiffre-affaires');
    const messageStatistiques = document.getElementById('message-statistiques');
    const casesMenus = document.querySelectorAll('input[name="filtre-menu"]');
    const caseTous = document.querySelector('input[name="filtre-menu"][value="tous"]');
    const champDateDebut = document.getElementById('date-debut');
    const champDateFin = document.getElementById('date-fin');
    // 2. Création du graphique, vide au départ
    const graphiqueCommandes = new Chart(conteneurGraphique, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Nombre de commandes',
                data: [],
                backgroundColor: '#4A7B59',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    // 3. Écrire un montant à la française : 1 234,56 €
    function formaterMontant(montant) {
        return montant.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' });
    }

    // 4. Mettre à jour le graphique, la liste et le total avec les données reçues
    function afficherStatistiques(statistiques) {
        const titres = [];
        const nombres = [];
        let total = 0;

        // On vide la liste avant de la remplir à nouveau
        listeChiffreAffaires.innerHTML = '';

        statistiques.forEach(ligne => {
            titres.push(ligne.titre);
            nombres.push(ligne.nombre_commandes);
            total = total + ligne.chiffre_affaires;

            // "commande" au singulier, "commandes" au pluriel
            let mot = 'commandes';
            if (ligne.nombre_commandes === 1) {
                mot = 'commande';
            }

            // Une ligne de la liste : le nom à gauche, le montant à droite
            const element = document.createElement('li');

            const texte = document.createElement('span');
            texte.textContent = ligne.titre + ' (' + ligne.nombre_commandes + ' ' + mot + ')';

            const montant = document.createElement('span');
            montant.className = 'montant-chiffre-affaires';
            montant.textContent = formaterMontant(ligne.chiffre_affaires);

            element.appendChild(texte);
            element.appendChild(montant);
            listeChiffreAffaires.appendChild(element);
        });

        // On donne les nouvelles données au graphique et on le redessine
        graphiqueCommandes.data.labels = titres;
        graphiqueCommandes.data.datasets[0].data = nombres;
        graphiqueCommandes.update();

        totalChiffreAffaires.textContent = formaterMontant(total);

        // Message annoncé aux lecteurs d'écran (zone role="status")
        if (statistiques.length === 0) {
            messageStatistiques.textContent = 'Aucune commande pour ces critères.';
        } else {
            messageStatistiques.textContent = 'Statistiques mises à jour : ' + statistiques.length + ' menu(s), total ' + formaterMontant(total) + '.';
        }
    }
    // Lire les identifiants des menus cochés (sans la case "Tous les menus")
    function lireMenusCoches() {
        const menusCoches = [];
        casesMenus.forEach(caseMenu => {
            if (caseMenu.checked && caseMenu.value !== 'tous') {
                menusCoches.push(caseMenu.value);
            }
        });
        return menusCoches;
    }
    // 5. Demander les statistiques au serveur
    async function chargerStatistiques() {
                const menusCoches = lireMenusCoches();

        // Aucun menu coché : on ne demande rien au serveur
        if (menusCoches.length === 0) {
            afficherStatistiques([]);
            messageStatistiques.textContent = 'Cochez au moins un menu pour afficher les statistiques.';
            return;
        }

        // On prépare les filtres à placer dans l'adresse
        const parametres = new URLSearchParams();
        parametres.append('menus', menusCoches.join(','));
                // Les dates ne sont ajoutées que si elles sont remplies
        if (champDateDebut.value !== '') {
            parametres.append('debut', champDateDebut.value);
        }
        if (champDateFin.value !== '') {
            parametres.append('fin', champDateFin.value);
        }
        try {
            const reponse = await fetch('statistiques.php?' + parametres.toString());
            const resultat = await reponse.json();

            if (resultat.succes) {
                afficherStatistiques(resultat.statistiques);
                    } else {
                // Le serveur a refusé la demande : on vide l'affichage et on montre son message
                afficherStatistiques([]);
                messageStatistiques.textContent = 'Erreur : ' + resultat.message;
            }
        } catch (erreur) {
            // Le serveur n'a pas pu être contacté du tout
            afficherStatistiques([]);
            messageStatistiques.textContent = 'Impossible de contacter le serveur. Réessayez plus tard.';
        }
    }
    // Les cases à cocher : chaque changement met à jour les statistiques
    casesMenus.forEach(caseMenu => {
        caseMenu.addEventListener('change', function () {

            if (caseMenu.value === 'tous') {
                // "Tous les menus" coche ou décoche toutes les cases d'un coup
                casesMenus.forEach(autreCase => {
                    autreCase.checked = caseMenu.checked;
                });
            } else {
                // Un menu a changé : "Tous" n'est coché que si tous les menus le sont
                const nombreDeMenus = casesMenus.length - 1;
                if (lireMenusCoches().length === nombreDeMenus) {
                    caseTous.checked = true;
                } else {
                    caseTous.checked = false;
                }
            }

            chargerStatistiques();
        });
    });
        // Les dates : chaque changement met à jour les statistiques
    champDateDebut.addEventListener('change', chargerStatistiques);
    champDateFin.addEventListener('change', chargerStatistiques);
    // 6. Premier affichage au chargement de la page
    chargerStatistiques();
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