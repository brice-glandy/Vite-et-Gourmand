<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';
require_once 'includes/Plats.php';

// 1. Seulement en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php#plats');
    exit;
}

$modele_plats = new Plats($pdo);

$id = (isset($_POST['id']) && is_string($_POST['id'])) ? trim($_POST['id']) : '';
$titre = (isset($_POST['titre_plat']) && is_string($_POST['titre_plat'])) ? trim($_POST['titre_plat']) : '';
$type = (isset($_POST['type_plat']) && is_string($_POST['type_plat'])) ? $_POST['type_plat'] : '';

// 2. Modification : le numéro caché doit être celui d'un plat qui existe
if ($id != '' && (!ctype_digit($id) || !$modele_plats->trouver_par_id($id))) {
    header('Location: admin.php#plats');
    exit;
}

// 3. Vérifications côté serveur
$erreurs = [];
if ($titre == '' || mb_strlen($titre) > 100) {
    $erreurs[] = 'Le nom du plat est obligatoire (100 caractères au maximum).';
}
if (!in_array($type, Plats::TYPES, true)) {
    $erreurs[] = 'Choisissez un type dans la liste.';
}

// Les allergènes cochés : chacun doit exister en base (liste blanche lue dans la base)
$ids_existants = array_column($modele_plats->trouver_allergenes(), 'id');
$allergenes = [];
if (isset($_POST['allergenes']) && is_array($_POST['allergenes'])) {
    foreach ($_POST['allergenes'] as $allergene_id) {
        if (is_string($allergene_id) && ctype_digit($allergene_id) && in_array((int) $allergene_id, $ids_existants, true)) {
            $allergenes[] = (int) $allergene_id;
        } else {
            $erreurs[] = 'Un allergène reçu n\'existe pas.';
        }
    }
}
// Un même allergène envoyé deux fois (avec F12) n'est gardé qu'une fois
$allergenes = array_unique($allergenes);

// 4. En cas d'erreur : retour au formulaire avec les messages et la saisie
if (count($erreurs) > 0) {
    $_SESSION['erreurs_plat'] = $erreurs;
    $_SESSION['saisie_plat'] = [
        'plat' => ['id' => $id, 'titre_plat' => $titre, 'type_plat' => $type],
        'allergenes' => $allergenes
    ];
    if ($id == '') {
        header('Location: modifier_plat.php');
    } else {
        header('Location: modifier_plat.php?id=' . $id);
    }
    exit;
}

// 5. Enregistrement : sans numéro, un nouveau plat ; avec un numéro, une modification
try {
    if ($id == '') {
        $modele_plats->creer($titre, $type, $allergenes);
        $_SESSION['message_plats'] = 'Le plat « ' . $titre . ' » a été ajouté.';
    } else {
        $modele_plats->modifier((int) $id, $titre, $type, $allergenes);
        $_SESSION['message_plats'] = 'Le plat « ' . $titre . ' » a été modifié.';
    }
} catch (PDOException $e) {
    error_log('Enregistrement du plat impossible : ' . $e->getMessage());
    $_SESSION['message_plats'] = 'Le plat n\'a pas pu être enregistré. Réessayez dans un instant.';
}

// 6. Retour sur la rubrique Plats (Post/Redirect/Get)
header('Location: admin.php#plats');
exit;