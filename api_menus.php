<?php
// API des menus : renvoie en JSON les menus qui correspondent aux filtres.
// Elle est publique : les menus sont visibles, connecté ou non.
require_once 'includes/db.php';
require_once 'includes/Menus.php';

// La réponse sera du JSON, pas une page HTML
header('Content-Type: application/json');

// Petite fonction pour répondre une erreur et arrêter le script (comme dans statistiques.php)
function repondre_erreur($code, $message) {
    http_response_code($code);
    echo json_encode(['succes' => false, 'message' => $message]);
    exit;
}

// Chaque filtre est facultatif : une case vide veut dire « pas de filtre ».
$filtres = [];

// Les prix : des nombres positifs (les centimes sont acceptés)
foreach (['prix_min', 'prix_max'] as $nom) {
    if (isset($_GET[$nom]) && $_GET[$nom] !== '') {
        if (!is_numeric($_GET[$nom]) || $_GET[$nom] < 0) {
            repondre_erreur(400, 'Prix invalide');
        }
        $filtres[$nom] = (float) $_GET[$nom];
    }
}

// Le thème, le régime et les invités : des nombres entiers à partir de 1
foreach (['theme', 'regime', 'personnes'] as $nom) {
    if (isset($_GET[$nom]) && $_GET[$nom] !== '') {
        if (!ctype_digit($_GET[$nom]) || (int) $_GET[$nom] < 1) {
            repondre_erreur(400, 'Filtre invalide');
        }
        $filtres[$nom] = (int) $_GET[$nom];
    }
}

// Une fourchette à l'envers n'a pas de sens
if (isset($filtres['prix_min']) && isset($filtres['prix_max']) && $filtres['prix_min'] > $filtres['prix_max']) {
    repondre_erreur(400, 'Le prix minimum doit être inférieur au prix maximum');
}

$modele_menus = new Menus($pdo);
$menus = $modele_menus->trouver_filtres($filtres);

echo json_encode(['succes' => true, 'menus' => $menus]);