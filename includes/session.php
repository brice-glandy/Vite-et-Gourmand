<?php
// Réglages de sécurité du cookie de session, AVANT de démarrer la session
require_once __DIR__ . '/config.php';

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => $config_cookie_secure
]);

session_start();