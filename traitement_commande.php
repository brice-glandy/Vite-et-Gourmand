<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/Menus.php';

// 1. Réservé aux clients connectés, et seulement par l'envoi du formulaire (POST)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'utilisateur') {
    header('Location: connexion.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: menus.php');
    exit;
}

$erreurs = [];

// 2. Le menu : il doit exister et avoir encore du stock
$menu = false;
if (isset($_POST['menu']) && ctype_digit($_POST['menu'])) {
    $modele_menus = new Menus($pdo);
    $menu = $modele_menus->trouver_par_id((int) $_POST['menu']);
}
if (!$menu) {
    $erreurs[] = 'Veuillez choisir un menu.';
} elseif ($menu['quantite_restante'] <= 0) {
    $erreurs[] = 'Ce menu n\'est plus disponible pour le moment.';
}

// 3. Le nombre de personnes : au moins le minimum du menu
$nombre_personnes = 0;
if (isset($_POST['nombre-personnes']) && ctype_digit($_POST['nombre-personnes'])) {
    $nombre_personnes = (int) $_POST['nombre-personnes'];
}
if ($menu && $nombre_personnes < $menu['nombre_personne_minimum']) {
    $erreurs[] = 'Ce menu se commande pour au moins ' . $menu['nombre_personne_minimum'] . ' personnes.';
}

// 4. La date (format AAAA-MM-JJ) : au plus tôt demain
$date_prestation = isset($_POST['date-prestation']) ? $_POST['date-prestation'] : '';
$date = DateTime::createFromFormat('Y-m-d', $date_prestation);
if (!$date || $date->format('Y-m-d') !== $date_prestation) {
    $erreurs[] = 'La date de la prestation est invalide.';
} elseif ($date_prestation <= date('Y-m-d')) {
    $erreurs[] = 'La prestation doit avoir lieu au plus tôt demain.';
}

// 5. L'heure (format HH:MM)
$heure = isset($_POST['heure-souhaitee']) ? $_POST['heure-souhaitee'] : '';
$heure_lue = DateTime::createFromFormat('H:i', $heure);
if (!$heure_lue || $heure_lue->format('H:i') !== $heure) {
    $erreurs[] = 'L\'heure souhaitée est invalide.';
}

// 6. L'adresse de la prestation
$adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
$complement = isset($_POST['complement']) ? trim($_POST['complement']) : '';
$code_postal = isset($_POST['code-postal']) ? trim($_POST['code-postal']) : '';
$ville = isset($_POST['ville']) ? trim($_POST['ville']) : '';
if ($adresse === '' || $ville === '') {
    $erreurs[] = 'L\'adresse et la ville de la prestation sont obligatoires.';
}
if (!preg_match('/^[0-9]{5}$/', $code_postal)) {
    $erreurs[] = 'Le code postal doit contenir 5 chiffres.';
}

// 7. La distance : seulement hors Bordeaux, entre 1 et 100 km
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

// 8. S'il y a des erreurs : retour au formulaire, avec le menu toujours choisi
if (count($erreurs) > 0) {
    $_SESSION['erreurs_commande'] = $erreurs;
    $retour = 'commandes.php';
    if ($menu) {
        $retour = 'commandes.php?menu=' . $menu['id'];
    }
    header('Location: ' . $retour);
    exit;
}

// 9. Le prix est RECALCULÉ ici : on ne fait jamais confiance au prix affiché par le navigateur
$prix_menu = $menu['prix_par_personne'] * $nombre_personnes;
if ($nombre_personnes >= $menu['nombre_personne_minimum'] + 5) {
    $prix_menu = $prix_menu * 0.9;
}
$prix_menu = round($prix_menu, 2);

$prix_livraison = 0;
if (!$a_bordeaux) {
    $prix_livraison = round(5 + 0.59 * $distance, 2);
}
$prix_total = round($prix_menu + $prix_livraison, 2);

// 10. Le numéro de commande : l'année + 6 caractères tirés au hasard
$numero = 'CMD-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));

// L'adresse complète, avec le complément et le code postal
$adresse_complete = $adresse;
if ($complement !== '') {
    $adresse_complete = $adresse_complete . ', ' . $complement;
}
$adresse_complete = $adresse_complete . ', ' . $code_postal;

// 11. Enregistrement : stock ET commande dans une même transaction (tout ou rien)
try {
    $pdo->beginTransaction();

    // a. On retire 1 au stock, seulement s'il en reste
    $requete = $pdo->prepare("UPDATE menu SET quantite_restante = quantite_restante - 1
                              WHERE id = :id AND quantite_restante > 0");
    $requete->execute(['id' => $menu['id']]);
    if ($requete->rowCount() == 0) {
        // Un autre client a pris la dernière commande possible entre-temps
        $pdo->rollBack();
        $_SESSION['erreurs_commande'] = ['Ce menu vient d\'être épuisé, désolé.'];
        header('Location: commandes.php?menu=' . $menu['id']);
        exit;
    }

    // b. On enregistre la commande, avec les prix calculés par le serveur
    $sql = "INSERT INTO commande (numero_commande, utilisateur_id, menu_id, date_prestation, heure_livraison,
                                  adresse_livraison, ville_livraison, nombre_personne, prix_menu, prix_livraison, prix_total)
            VALUES (:numero, :utilisateur_id, :menu_id, :date_prestation, :heure,
                    :adresse, :ville, :nombre, :prix_menu, :prix_livraison, :prix_total)";
    $requete = $pdo->prepare($sql);
    $requete->execute([
        'numero' => $numero,
        'utilisateur_id' => $_SESSION['utilisateur_id'],
        'menu_id' => $menu['id'],
        'date_prestation' => $date_prestation,
        'heure' => $heure,
        'adresse' => $adresse_complete,
        'ville' => $ville,
        'nombre' => $nombre_personnes,
        'prix_menu' => $prix_menu,
        'prix_livraison' => $prix_livraison,
        'prix_total' => $prix_total
    ]);

    // c. Tout s'est bien passé : on valide les deux opérations d'un coup
    $pdo->commit();
} catch (PDOException $e) {
    // Une erreur : on annule tout ce qui avait commencé
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Commande impossible : ' . $e->getMessage());
    $_SESSION['erreurs_commande'] = ['Une erreur technique empêche l\'enregistrement. Merci de réessayer.'];
    header('Location: commandes.php?menu=' . $menu['id']);
    exit;
}
// 12. Copie dans MongoDB pour les statistiques (même structure que init_mongo.js).
// MySQL reste la référence : si MongoDB ne répond pas, la commande reste valable.
try {
    require_once 'includes/mongo.php';
    $ecriture = new MongoDB\Driver\BulkWrite();
    $ecriture->insert([
        'numero_commande' => $numero,
        'menu_id' => (int) $menu['id'],
        'menu_titre' => $menu['titre'],
        'date_commande' => new MongoDB\BSON\UTCDateTime(),
        'nombre_personne' => $nombre_personnes,
        'prix_menu' => $prix_menu,
        'prix_total' => $prix_total,
        'statut' => 'en attente'
    ]);
    $mongo->executeBulkWrite($mongo_base . '.commandes', $ecriture);
} catch (MongoDB\Driver\Exception\Exception $e) {
    error_log('Copie MongoDB impossible pour ' . $numero . ' : ' . $e->getMessage());
}
// Test provisoire (remplacé plus tard par la page de confirmation)
echo 'Commande enregistrée : ' . $numero;
exit;