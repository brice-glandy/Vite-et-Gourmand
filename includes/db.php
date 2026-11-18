<?php
// Connexion à MySQL (MariaDB chez alwaysdata).
// Les identifiants viennent de config.php, propre à chaque machine.
require_once __DIR__ . '/config.php';
// Tout le site fonctionne à l'heure française (le passage à l'heure d'été est géré automatiquement)
date_default_timezone_set('Europe/Paris');

try {
    $pdo = new PDO("mysql:host=$config_db_hote;dbname=$config_db_nom;charset=utf8mb4", $config_db_utilisateur, $config_db_mot_de_passe);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // MySQL utilise le même décalage horaire que PHP (+01:00 en hiver, +02:00 en été)
    $pdo->exec("SET time_zone = '" . date('P') . "'");
} catch (PDOException $e) {
    // Le détail technique va dans le journal du serveur, jamais à l'écran du visiteur
    error_log('Connexion MySQL impossible : ' . $e->getMessage());
    http_response_code(503);
    die("Le site est momentanément indisponible. Merci de réessayer dans quelques instants.");
}
?>