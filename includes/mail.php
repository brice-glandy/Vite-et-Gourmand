<?php
// Envoi des mails du site.
// Pour l'instant, le mail est SIMULÉ : il est écrit dans le journal du serveur
// (en local : docker compose logs web). Pour un vrai envoi, seule cette fonction changera.
function envoyer_mail($destinataire, $sujet, $message) {
    error_log('MAIL SIMULÉ | À : ' . $destinataire . ' | Sujet : ' . $sujet . ' | ' . $message);
    return true;
}