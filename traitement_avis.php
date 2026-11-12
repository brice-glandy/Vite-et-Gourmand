<?php
require_once 'includes/db.php';

// On prévient le navigateur : la réponse sera du JSON, pas une page HTML
header('Content-Type: application/json');

// 1. On n'accepte que les envois en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['succes' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// 2. On vérifie que les deux informations sont bien arrivées
if (!isset($_POST['id']) || !isset($_POST['statut'])) {
    http_response_code(400);
    echo json_encode(['succes' => false, 'message' => 'Données manquantes']);
    exit;
}

$id = (int) $_POST['id'];
$statut = $_POST['statut'];

// 3. Liste blanche : seuls ces deux statuts sont acceptés
$statuts_autorises = ['validé', 'refusé'];
if (!in_array($statut, $statuts_autorises)) {
    http_response_code(400);
    echo json_encode(['succes' => false, 'message' => 'Statut invalide']);
    exit;
}

// 4. Mise à jour en base avec une requête préparée
$sql = "UPDATE avis SET statut = :statut WHERE id = :id";
$requete = $pdo->prepare($sql);
$requete->execute([
    'statut' => $statut,
    'id' => $id
]);

// 5. Réponse au JavaScript : tout s'est bien passé
echo json_encode(['succes' => true, 'statut' => $statut]);