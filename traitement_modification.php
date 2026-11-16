<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/Commandes.php';
require_once 'includes/Menus.php';

// 1. Réservé aux clients connectés, et seulement en POST
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'utilisateur') {
    header('Location: connexion.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: espace_client.php');
    exit;
}

// 2. La commande doit appartenir à ce client ET être encore en attente
$modele_commandes = new Commandes($pdo);
$commande = false;
if (isset($_POST['commande_id']) && ctype_digit($_POST['commande_id'])) {
    $commande = $modele_commandes->trouver_du_client((int) $_POST['commande_id'], $_SESSION['utilisateur_id']);
}
if (!$commande || $commande['statut'] != 'en attente') {
    $_SESSION['erreur_commandes'] = 'Cette commande ne peut plus être modifiée.';
    header('Location: espace_client.php#titre-commandes');
    exit;
}

$modele_menus = new Menus($pdo);
$menu = $modele_menus->trouver_par_id($commande['menu_id']);
$erreurs = [];

// 3. Les plats : pour chaque type, le plat choisi doit faire partie des plats possibles DE CE MENU
$plats_possibles = $modele_menus->trouver_plats($menu['id']);
$plats_retenus = [];
foreach (['Entrée', 'Plat', 'Dessert'] as $type) {
    // Les numéros des plats possibles pour ce type
    $ids_possibles = [];
    foreach ($plats_possibles as $un_plat) {
        if ($un_plat['type_plat'] == $type) {
            $ids_possibles[] = (int) $un_plat['id'];
        }
    }
    if (count($ids_possibles) == 0) {
        continue; // ce menu n'a pas de plat de ce type (le Brunch n'a pas d'entrée)
    }
    $choix = 0;
    if (isset($_POST['plats'][$type]) && is_string($_POST['plats'][$type]) && ctype_digit($_POST['plats'][$type])) {
        $choix = (int) $_POST['plats'][$type];
    }
    if (in_array($choix, $ids_possibles)) {
        $plats_retenus[] = $choix;
    } else {
        $erreurs[] = 'Le choix pour « ' . $type . ' » n\'est pas valable pour ce menu.';
    }
}

// 4. Le nombre de personnes : au moins le minimum du menu
$nombre_personnes = 0;
if (isset($_POST['nombre-personnes']) && ctype_digit($_POST['nombre-personnes'])) {
    $nombre_personnes = (int) $_POST['nombre-personnes'];
}
if ($nombre_personnes < $menu['nombre_personne_minimum']) {
    $erreurs[] = 'Ce menu se commande pour au moins ' . $menu['nombre_personne_minimum'] . ' personnes.';
}

// 5. La date (au plus tôt demain) et l'heure
$date_prestation = isset($_POST['date-prestation']) ? $_POST['date-prestation'] : '';
$date = DateTime::createFromFormat('Y-m-d', $date_prestation);
if (!$date || $date->format('Y-m-d') !== $date_prestation) {
    $erreurs[] = 'La date de la prestation est invalide.';
} elseif ($date_prestation <= date('Y-m-d')) {
    $erreurs[] = 'La prestation doit avoir lieu au plus tôt demain.';
}
$heure = isset($_POST['heure-souhaitee']) ? $_POST['heure-souhaitee'] : '';
$heure_lue = DateTime::createFromFormat('H:i', $heure);
if (!$heure_lue || $heure_lue->format('H:i') !== $heure) {
    $erreurs[] = 'L\'heure souhaitée est invalide.';
}

// 6. L'adresse, la ville et la distance (hors Bordeaux, de 1 à 100 km)
$adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
$ville = isset($_POST['ville']) ? trim($_POST['ville']) : '';
if ($adresse === '' || $ville === '') {
    $erreurs[] = 'L\'adresse et la ville de la prestation sont obligatoires.';
}
$a_bordeaux = (mb_strtolower($ville) === 'bordeaux');
$distance = 0;
if (!$a_bordeaux) {
    if (isset($_POST['distance']) && ctype_digit($_POST['distance'])) {
        $distance = (int) $_POST['distance'];
    }
    if ($distance < 1 || $distance > 100) {
        $erreurs[] = 'Hors Bordeaux, indiquez une distance entre 1 et 100 km.';
    }
}

// 7. S'il y a des erreurs : retour à la page de modification
if (count($erreurs) > 0) {
    $_SESSION['erreurs_modification'] = $erreurs;
    header('Location: modifier_commande.php?id=' . $commande['id']);
    exit;
}

// 8. Le nouveau prix, par la même méthode que la commande
$prix = $modele_commandes->calculer_prix($menu, $nombre_personnes, $a_bordeaux, $distance);

// 9. Enregistrement : commande ET plats dans une même transaction
try {
    $pdo->beginTransaction();

    // a. On relit le statut en « verrouillant » la ligne, pour qu'un employé ne l'accepte pas au même moment
    $requete = $pdo->prepare("SELECT statut FROM commande WHERE id = :id FOR UPDATE");
    $requete->execute(['id' => $commande['id']]);
    if ($requete->fetchColumn() != 'en attente') {
        $pdo->rollBack();
        $_SESSION['erreur_commandes'] = 'La commande vient d\'être prise en charge : contactez-nous pour la modifier.';
        header('Location: espace_client.php#titre-commandes');
        exit;
    }

    // b. La commande elle-même (le menu ne change pas)
    $sql = "UPDATE commande
            SET date_prestation = :date_prestation, heure_livraison = :heure,
                adresse_livraison = :adresse, ville_livraison = :ville, nombre_personne = :nombre,
                prix_menu = :prix_menu, prix_livraison = :prix_livraison, prix_total = :prix_total
            WHERE id = :id";
    $requete = $pdo->prepare($sql);
    $requete->execute([
        'date_prestation' => $date_prestation,
        'heure' => $heure,
        'adresse' => $adresse,
        'ville' => $ville,
        'nombre' => $nombre_personnes,
        'prix_menu' => $prix['prix_menu'],
        'prix_livraison' => $prix['prix_livraison'],
        'prix_total' => $prix['prix_total'],
        'id' => $commande['id']
    ]);

    // c. Les plats : on efface les anciens choix, puis on enregistre les nouveaux
    $requete = $pdo->prepare("DELETE FROM commande_plat WHERE commande_id = :id");
    $requete->execute(['id' => $commande['id']]);
    $requete = $pdo->prepare("INSERT INTO commande_plat (commande_id, plat_id) VALUES (:commande_id, :plat_id)");
    foreach ($plats_retenus as $plat_id) {
        $requete->execute(['commande_id' => $commande['id'], 'plat_id' => $plat_id]);
    }

    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Modification impossible : ' . $e->getMessage());
    $_SESSION['erreurs_modification'] = ['Une erreur technique empêche l\'enregistrement. Merci de réessayer.'];
    header('Location: modifier_commande.php?id=' . $commande['id']);
    exit;
}

// 10. La copie MongoDB suit : nombre de personnes et prix (pour les statistiques)
try {
    require_once 'includes/mongo.php';
    $ecriture = new MongoDB\Driver\BulkWrite();
    $ecriture->update(
        ['numero_commande' => $commande['numero_commande']],
        ['$set' => [
            'nombre_personne' => $nombre_personnes,
            'prix_menu' => $prix['prix_menu'],
            'prix_total' => $prix['prix_total']
        ]]
    );
    $mongo->executeBulkWrite($mongo_base . '.commandes', $ecriture);
} catch (MongoDB\Driver\Exception\Exception $e) {
    error_log('Mise à jour MongoDB impossible pour ' . $commande['numero_commande'] . ' : ' . $e->getMessage());
}

// 11. Post/Redirect/Get : retour à l'espace client
$_SESSION['message_commandes'] = 'La commande ' . $commande['numero_commande'] . ' a bien été modifiée.';
header('Location: espace_client.php#titre-commandes');
exit;