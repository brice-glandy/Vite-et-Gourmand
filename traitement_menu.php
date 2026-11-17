<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';
require_once 'includes/Menus.php';

// 1. Seulement en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php#menus');
    exit;
}

// 2. Petite fonction : lit un champ du formulaire, sans les espaces autour (texte vide s'il manque)
function lire_champ($nom) {
    if (isset($_POST[$nom]) && is_string($_POST[$nom])) {
        return trim($_POST[$nom]);
    }
    return '';
}

$id = lire_champ('id');
$titre = lire_champ('titre');
$description = lire_champ('description');
$theme_id = lire_champ('theme_id');
$regime_id = lire_champ('regime_id');
$minimum = lire_champ('nombre_personne_minimum');
$prix = lire_champ('prix_par_personne');
$stock = lire_champ('quantite_restante');
$conditions = lire_champ('conditions');
$image_url = lire_champ('image_url');

$modele_menus = new Menus($pdo);

// 2 bis. Modification : le numéro caché doit être celui d'un menu qui existe (sinon, retour à la rubrique)
if ($id != '' && (!ctype_digit($id) || !$modele_menus->trouver_par_id($id))) {
    header('Location: admin.php#menus');
    exit;
}

// 3. Vérifications côté serveur (le navigateur peut être contourné avec F12)
$erreurs = [];
if ($titre == '' || mb_strlen($titre) > 100) {
    $erreurs[] = 'Le titre est obligatoire (100 caractères au maximum).';
}
if ($description == '') {
    $erreurs[] = 'La description est obligatoire.';
}
if ($conditions == '') {
    $erreurs[] = 'Les conditions du menu sont obligatoires.';
}
// Le thème et le régime doivent exister en base (liste blanche lue dans la base)
$ids_themes = array_column($modele_menus->trouver_themes(), 'id');
if (!ctype_digit($theme_id) || !in_array((int) $theme_id, $ids_themes, true)) {
    $erreurs[] = 'Choisissez un thème dans la liste.';
}
$ids_regimes = array_column($modele_menus->trouver_regimes(), 'id');
if (!ctype_digit($regime_id) || !in_array((int) $regime_id, $ids_regimes, true)) {
    $erreurs[] = 'Choisissez un régime dans la liste.';
}
if (!ctype_digit($minimum) || (int) $minimum < 1) {
    $erreurs[] = 'Le nombre de personnes minimum doit être un nombre entier, au moins 1.';
}
if (!is_numeric($prix) || (float) $prix <= 0) {
    $erreurs[] = 'Le prix par personne doit être un nombre supérieur à 0.';
}
if (!ctype_digit($stock)) {
    $erreurs[] = 'Le stock doit être un nombre entier (0 ou plus).';
}
// L'image doit être l'un des fichiers du dossier images/ (liste blanche)
if (!in_array($image_url, glob('images/*.jpg'), true)) {
    $erreurs[] = 'Choisissez une image dans la liste.';
}

// 4. En cas d'erreur : retour au formulaire, avec les messages et ce qui avait été tapé
if (count($erreurs) > 0) {
    $_SESSION['erreurs_menu'] = $erreurs;
    $_SESSION['saisie_menu'] = [
        'id' => $id,
        'titre' => $titre,
        'description' => $description,
        'theme_id' => $theme_id,
        'regime_id' => $regime_id,
        'nombre_personne_minimum' => $minimum,
        'prix_par_personne' => $prix,
        'quantite_restante' => $stock,
        'conditions' => $conditions,
        'image_url' => $image_url
    ];
    if ($id == '') {
        header('Location: modifier_menu.php');
    } else {
        header('Location: modifier_menu.php?id=' . $id);
    }
    exit;
}

// 5. Tout est bon : les 9 valeurs, exactement avec les noms attendus par creer() et modifier()
$donnees = [
    'titre' => $titre,
    'description' => $description,
    'nombre_personne_minimum' => (int) $minimum,
    'prix_par_personne' => round((float) $prix, 2),
    'quantite_restante' => (int) $stock,
    'conditions' => $conditions,
    'image_url' => $image_url,
    'theme_id' => (int) $theme_id,
    'regime_id' => (int) $regime_id
];

// Sans numéro : c'est un nouveau menu. Avec un numéro : c'est une modification.
if ($id == '') {
    $modele_menus->creer($donnees);
    $_SESSION['message_menus'] = 'Le menu « ' . $titre . ' » a été ajouté.';
} else {
    $modele_menus->modifier((int) $id, $donnees);
    $_SESSION['message_menus'] = 'Le menu « ' . $titre . ' » a été modifié.';
}

// 6. Retour sur la rubrique Menus (Post/Redirect/Get)
header('Location: admin.php#menus');
exit;