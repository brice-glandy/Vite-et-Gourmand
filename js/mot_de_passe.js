// --- AFFICHER / MASQUER LE MOT DE PASSE ---

// 1. On récupère tous les boutons "œil" de la page
const boutonsOeil = document.querySelectorAll('.btn-oeil');

// 2. Pour chaque bouton, on écoute le clic
boutonsOeil.forEach(bouton => {
    bouton.addEventListener('click', function() {
        // Le champ mot de passe est l'élément juste avant le bouton
        const champ = this.previousElementSibling;

        if (champ.type === 'password') {
            // On affiche le texte en clair
            champ.type = 'text';
            this.setAttribute('aria-label', 'Masquer le mot de passe');
            this.setAttribute('aria-pressed', 'true');
        } else {
            // On masque à nouveau
            champ.type = 'password';
            this.setAttribute('aria-label', 'Afficher le mot de passe');
            this.setAttribute('aria-pressed', 'false');
        }
    });
});