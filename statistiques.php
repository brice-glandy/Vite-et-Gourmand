<?php
require_once 'includes/session.php';
require_once 'includes/mongo.php';

// La réponse sera du JSON, pas une page HTML
header('Content-Type: application/json');
// Petite fonction pour répondre une erreur et arrêter le script
function repondre_erreur($code, $message) {
    http_response_code($code);
    echo json_encode(['succes' => false, 'message' => $message]);
    exit;
}
// 0. Accès réservé à l'administrateur : sinon, code 403 "accès interdit"
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'administrateur') {
    repondre_erreur(403, 'Accès réservé à l\'administrateur');
}
// Filtre de base : on écarte toujours les commandes annulées
$filtre = ['statut' => ['$ne' => 'annulée']];
// Filtre par menus (facultatif), reçu sous la forme "menus=1,3,6"
if (!empty($_GET['menus'])) {
    $menus_choisis = [];
    foreach (explode(',', $_GET['menus']) as $morceau) {
        $id = (int) $morceau;
        if ($id > 0) {
            $menus_choisis[] = $id;
        }
    }
    if (count($menus_choisis) == 0) {
        repondre_erreur(400, 'Filtre des menus invalide');
    }
    $filtre['menu_id'] = ['$in' => $menus_choisis];
}
// 1. Le calcul demandé à MongoDB, étape par étape
$etapes = [
    // On écarte les commandes annulées
    ['$match' => $filtre],
    // On regroupe par menu : on compte les commandes et on additionne les prix
    ['$group' => [
        '_id' => '$menu_id',
        'titre' => ['$first' => '$menu_titre'],
        'nombre_commandes' => ['$sum' => 1],
        'chiffre_affaires' => ['$sum' => '$prix_menu']
    ]],
    // On trie par titre, pour un ordre stable dans le graphique
    ['$sort' => ['titre' => 1]]
];

$commande = new MongoDB\Driver\Command([
    'aggregate' => 'commandes',
    'pipeline' => $etapes,
    'cursor' => new stdClass()
]);

// 2. On envoie le calcul à MongoDB ; si la base ne répond pas, erreur 500
try {
    $curseur = $mongo->executeCommand($mongo_base, $commande);
} catch (MongoDB\Driver\Exception\Exception $e) {
    repondre_erreur(500, 'Statistiques indisponibles pour le moment');
}

// 3. On met le résultat en forme pour le JavaScript
$statistiques = [];
foreach ($curseur as $ligne) {
    $statistiques[] = [
        'menu_id' => $ligne->_id,
        'titre' => $ligne->titre,
        'nombre_commandes' => $ligne->nombre_commandes,
        'chiffre_affaires' => round($ligne->chiffre_affaires, 2)
    ];
}

echo json_encode(['succes' => true, 'statistiques' => $statistiques]);