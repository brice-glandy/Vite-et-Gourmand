<?php
// Envoi des mails du site, par une seule fonction.
// Selon le réglage $config_mail_mode de config.php :
//  - 'mailpit' : vrai envoi vers Mailpit, la fausse boîte de réception de Docker (en local) ;
//  - 'journal' (ou réglage absent) : le mail est seulement écrit dans le journal du serveur (en ligne),
//    pour ne jamais écrire à de vraies personnes avec les adresses de démonstration.
require_once __DIR__ . '/config.php';

function envoyer_mail($destinataire, $sujet, $message) {
    global $config_mail_mode;

    if (isset($config_mail_mode) && $config_mail_mode == 'mailpit') {
        $entetes = "From: Vite & Gourmand <contact@viteetgourmand.fr>\r\n"
                 . "Content-Type: text/plain; charset=UTF-8";
        return mail($destinataire, mb_encode_mimeheader($sujet, 'UTF-8'), $message, $entetes);
    }

    error_log('MAIL SIMULÉ | À : ' . $destinataire . ' | Sujet : ' . $sujet . ' | ' . $message);
    return true;
}