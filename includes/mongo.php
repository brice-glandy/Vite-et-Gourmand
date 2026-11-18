<?php
// Connexion à MongoDB (base non relationnelle des statistiques)
// Même rôle que db.php pour MySQL. Les identifiants viennent de config.php.
require_once __DIR__ . '/config.php';

$mongo_base = $config_mongo_base;
$mongo = new MongoDB\Driver\Manager($config_mongo_adresse);