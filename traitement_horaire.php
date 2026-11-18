<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';

// 1. Ce script n'accepte que les envois de formulaire (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

// 2. Petite fonction : un champ vide devient NULL
function heureOuNull($valeur) {
    if ($valeur == '') {
        return null;
    }
    return $valeur;
}

// 3. Récupération des données envoyées par le formulaire
$id = (int) $_POST['id'];
$ouverture_matin = heureOuNull($_POST['ouverture_matin']);
$fermeture_matin = heureOuNull($_POST['fermeture_matin']);
$ouverture_soir = heureOuNull($_POST['ouverture_soir']);
$fermeture_soir = heureOuNull($_POST['fermeture_soir']);

// 3 bis. Les cases "Fermé le matin" et "Fermé le soir"
if (isset($_POST['matin_ferme'])) {
    $ouverture_matin = null;
    $fermeture_matin = null;
}
if (isset($_POST['soir_ferme'])) {
    $ouverture_soir = null;
    $fermeture_soir = null;
}

// 4. La case "Fermé toute la journée"
if (isset($_POST['est_ferme'])) {
    $est_ferme = 1;
    $ouverture_matin = null;
    $fermeture_matin = null;
    $ouverture_soir = null;
    $fermeture_soir = null;
} else {
    $est_ferme = 0;
}

// 5. Vérification de la cohérence des horaires
$erreur = false;

// a) Une ouverture sans fermeture, ou l'inverse
if ($ouverture_matin != null && $fermeture_matin == null) { $erreur = true; }
if ($ouverture_matin == null && $fermeture_matin != null) { $erreur = true; }
if ($ouverture_soir != null && $fermeture_soir == null) { $erreur = true; }
if ($ouverture_soir == null && $fermeture_soir != null) { $erreur = true; }

// b) Une fermeture avant (ou égale à) l'ouverture
if ($ouverture_matin != null && $fermeture_matin != null && $ouverture_matin >= $fermeture_matin) { $erreur = true; }
if ($ouverture_soir != null && $fermeture_soir != null && $ouverture_soir >= $fermeture_soir) { $erreur = true; }

if ($erreur) {
    header('Location: modifier_horaire.php?id=' . $id . '&erreur=1');
    exit;
}

// 6. Mise à jour en base avec une requête préparée
$sql = "UPDATE horaires
        SET ouverture_matin = :ouverture_matin,
            fermeture_matin = :fermeture_matin,
            ouverture_soir = :ouverture_soir,
            fermeture_soir = :fermeture_soir,
            est_ferme = :est_ferme
        WHERE id = :id";
$requete = $pdo->prepare($sql);
$requete->execute([
    'ouverture_matin' => $ouverture_matin,
    'fermeture_matin' => $fermeture_matin,
    'ouverture_soir' => $ouverture_soir,
    'fermeture_soir' => $fermeture_soir,
    'est_ferme' => $est_ferme,
    'id' => $id
]);

// 7. Retour à l'administration, sur l'onglet Horaires
header('Location: admin.php#horaires');
exit;