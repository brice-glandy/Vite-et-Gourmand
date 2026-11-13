// --- MENU BURGER (commun à toutes les pages) ---
const boutonBurger = document.querySelector('.menu-burger');
const menuNavigation = document.querySelector('.navigation-principale');

boutonBurger.addEventListener('click', function () {
    // On ouvre ou on ferme le menu
    menuNavigation.classList.toggle('actif');

    // On indique aux lecteurs d'écran si le menu est ouvert ou fermé
    if (menuNavigation.classList.contains('actif')) {
        boutonBurger.setAttribute('aria-expanded', 'true');
        boutonBurger.setAttribute('aria-label', 'Fermer le menu');
    } else {
        boutonBurger.setAttribute('aria-expanded', 'false');
        boutonBurger.setAttribute('aria-label', 'Ouvrir le menu');
    }
});