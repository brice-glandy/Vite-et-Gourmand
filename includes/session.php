<?php
// Réglages de sécurité du cookie de session, AVANT de démarrer la session
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();