const donneesMenus = {
    'burger': { prix: 15, minPersonnes: 2 },
    'brunch': { prix: 22, minPersonnes: 4 },
    'tradition': { prix: 18, minPersonnes: 2 },
    'oceane': { prix: 20, minPersonnes: 2 },
    'vegetal': { prix: 16, minPersonnes: 2 },
    'italienne': { prix: 17, minPersonnes: 2 }
};

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

    // Gestion de l'affichage du champ kilométrage
    if (nomVille !== '' && nomVille !== 'bordeaux') {
        blocDistance.style.display = 'flex';
    } else {
        blocDistance.style.display = 'none';
        distanceKm = 0; // On force à zéro si c'est Bordeaux
    }

    if (menuSelectionne && nombrePersonnes > 0) {
        let identifiantMenu = menuSelectionne.value;
        let prixUnitaire = donneesMenus[identifiantMenu].prix;
        let minimumRequis = donneesMenus[identifiantMenu].minPersonnes;
        
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
        affichagePrixUnitaire.value = prixUnitaire.toFixed(2) + " €";
        affichageFrais.value = fraisDePort.toFixed(2) + " €";
        affichageReduction.value = montantReduction.toFixed(2) + " €";
        affichageTotal.value = totalNet.toFixed(2) + " €";
    }
}

// Les écouteurs d'événements pour rendre le formulaire réactif
boutonsRadioMenus.forEach(bouton => {
    bouton.addEventListener('change', calculerFacture);
});
champQuantite.addEventListener('input', calculerFacture);
champVille.addEventListener('input', calculerFacture);
champDistance.addEventListener('input', calculerFacture);