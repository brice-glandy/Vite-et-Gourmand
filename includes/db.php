<?php
// Connexion à MySQL (MariaDB chez alwaysdata).
// Les identifiants viennent de config.php, propre à chaque machine.
require_once __DIR__ . '/config.php';

try {
    $pdo = new PDO("mysql:host=$config_db_hote;dbname=$config_db_nom;charset=utf8mb4", $config_db_utilisateur, $config_db_mot_de_passe);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Le détail technique va dans le journal du serveur, jamais à l'écran du visiteur
    error_log('Connexion MySQL impossible : ' . $e->getMessage());
    http_response_code(503);
    die("Le site est momentanément indisponible. Merci de réessayer dans quelques instants.");
}
?>