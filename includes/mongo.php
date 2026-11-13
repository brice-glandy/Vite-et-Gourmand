<?php
// Connexion à MongoDB (base non relationnelle des statistiques)
// Même rôle que db.php pour MySQL
$mongo_hote = 'mongo';
$mongo_utilisateur = 'root';
$mongo_mot_de_passe = 'root';
$mongo_base = 'v_et_g';

$mongo = new MongoDB\Driver\Manager("mongodb://$mongo_utilisateur:$mongo_mot_de_passe@$mongo_hote:27017");