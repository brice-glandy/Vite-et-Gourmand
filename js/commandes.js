const boutonsRadioMenus = document.querySelectorAll('input[name="menu"]');
const champQuantite = document.getElementById('nombre-personnes');
const champVille = document.getElementById('ville');
const champDistance = document.getElementById('distance');
const blocDistance = document.getElementById('bloc-distance');

const affichagePrixUnitaire = document.getElementById('prix-unitaire');
const affichageFrais = document.getElementById('frais-livraison');
const affichageReduction = document.getElementById('reduction');
const affichageTotal = document.getElementById('total');

function calculerFacture() {
    let menuSelectionne = document.querySelector('input[name="menu"]:checked');
    let nombrePersonnes = parseInt(champQuantite.value) || 0;
    let nomVille = champVille.value.trim().toLowerCase();
    let distanceKm = parseFloat(champDistance.value) || 0;
    // Le nombre de personnes ne peut pas descendre sous le minimum du menu choisi
    if (menuSelectionne) {
        champQuantite.min = menuSelectionne.dataset.minimum;
    }

    // Gestion de l'affichage du champ kilométrage
    if (nomVille !== '' && nomVille !== 'bordeaux') {
        blocDistance.style.display = 'flex';
    } else {
        blocDistance.style.display = 'none';
        distanceKm = 0; // On force à zéro si c'est Bordeaux
    }

    if (menuSelectionne && nombrePersonnes > 0) {
        // Le prix et le minimum sont lus dans les attributs data-prix et data-minimum du menu coché
        let prixUnitaire = parseFloat(menuSelectionne.dataset.prix);
        let minimumRequis = parseInt(menuSelectionne.dataset.minimum);
        
        let sousTotal = prixUnitaire * nombrePersonnes;
        
        // Calcul des frais de livraison selon le cahier des charges
        let fraisDePort = 0;
        if (nomVille !== '' && nomVille !== 'bordeaux') {
            fraisDePort = 5 + (0.59 * distanceKm);
        }

        // Calcul de la réduction de 10%
        let montantReduction = 0;
        if (nombrePersonnes >= (minimumRequis + 5)) {
            montantReduction = sousTotal * 0.10;
        }

        let totalNet = sousTotal + fraisDePort - montantReduction;

        // Mise à jour de l'interface avec formatage à 2 décimales
        affichagePrixUnitaire.value = prixUnitaire.toFixed(2).replace('.', ',') + " €";
        affichageFrais.value = fraisDePort.toFixed(2).replace('.', ',') + " €";
        affichageReduction.value = montantReduction.toFixed(2).replace('.', ',') + " €";
        affichageTotal.value = totalNet.toFixed(2).replace('.', ',') + " €"; 
    }
}

// Les écouteurs d'événements pour rendre le formulaire réactif
boutonsRadioMenus.forEach(bouton => {
    bouton.addEventListener('change', calculerFacture);
});
champQuantite.addEventListener('input', calculerFacture);
champVille.addEventListener('input', calculerFacture);
champDistance.addEventListener('input', calculerFacture);
// Calcul dès l'ouverture de la page, si un menu est déjà coché (venu de la page détail)
calculerFacture();