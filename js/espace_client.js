// Demande de confirmation avant d'annuler une commande (l'annulation est définitive)
document.querySelectorAll('.form-annulation').forEach(function (formulaire) {
    formulaire.addEventListener('submit', function (event) {
        const numero = formulaire.querySelector('button').dataset.numero;
        if (!confirm('Voulez-vous vraiment annuler la commande ' + numero + ' ?')) {
            event.preventDefault();
        }
    });
});