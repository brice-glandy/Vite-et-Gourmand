<?php
// Modèle des commandes : les requêtes SQL qui concernent les commandes, réunies au même endroit.
class Commandes {
        // Les statuts possibles, dans l'ordre de la vie d'une commande (énoncé, page 8)
    const STATUTS = ['en attente', 'accepté', 'en préparation', 'en cours de livraison', 'livré',
                     'en attente du retour de matériel', 'terminée', 'annulée'];
        // Les statuts que l'employé peut choisir (énoncé, page 8) : ni « en attente », ni « annulée »
    const STATUTS_MISE_A_JOUR = ['accepté', 'en préparation', 'en cours de livraison', 'livré',
                                 'en attente du retour de matériel', 'terminée'];                 
    // La connexion à la base, gardée dans l'objet
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Les commandes d'un client, de la plus récente à la plus ancienne, avec le titre du menu
    public function trouver_par_client($utilisateur_id) {
        $sql = "SELECT commande.*, menu.titre AS menu_titre
                FROM commande
                JOIN menu ON commande.menu_id = menu.id
                WHERE commande.utilisateur_id = :utilisateur_id
                ORDER BY commande.date_commande DESC";
        $requete = $this->pdo->prepare($sql);
        $requete->execute(['utilisateur_id' => $utilisateur_id]);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
        // Une commande précise, SEULEMENT si elle appartient à ce client (false sinon)
    public function trouver_du_client($commande_id, $utilisateur_id) {
        $sql = "SELECT * FROM commande WHERE id = :id AND utilisateur_id = :utilisateur_id";
        $requete = $this->pdo->prepare($sql);
        $requete->execute(['id' => $commande_id, 'utilisateur_id' => $utilisateur_id]);
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
        // Les plats choisis pour une commande, dans l'ordre du repas
    public function trouver_plats_choisis($commande_id) {
        $sql = "SELECT plat.id, plat.titre_plat, plat.type_plat
                FROM commande_plat
                JOIN plat ON plat.id = commande_plat.plat_id
                WHERE commande_plat.commande_id = :commande_id
                ORDER BY FIELD(plat.type_plat, 'Entrée', 'Plat', 'Dessert')";
        $requete = $this->pdo->prepare($sql);
        $requete->execute(['commande_id' => $commande_id]);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
        // Le prix d'une commande, calculé à partir des données de la base (jamais à partir du navigateur).
    // Renvoie les trois montants : prix du menu, livraison et total.
    public function calculer_prix($menu, $nombre_personnes, $a_bordeaux, $distance) {
        $prix_menu = $menu['prix_par_personne'] * $nombre_personnes;
        // Réduction de 10 % à partir du minimum + 5 personnes
        if ($nombre_personnes >= $menu['nombre_personne_minimum'] + 5) {
            $prix_menu = $prix_menu * 0.9;
        }
        $prix_menu = round($prix_menu, 2);

        // Livraison : gratuite à Bordeaux, sinon 5 € + 0,59 € par kilomètre
        $prix_livraison = 0;
        if (!$a_bordeaux) {
            $prix_livraison = round(5 + 0.59 * $distance, 2);
        }

        return [
            'prix_menu' => $prix_menu,
            'prix_livraison' => $prix_livraison,
            'prix_total' => round($prix_menu + $prix_livraison, 2)
        ];
    }
        // Ajoute une étape au suivi d'une commande (la date et l'heure sont mises par MySQL)
    public function ajouter_suivi($commande_id, $statut) {
        $requete = $this->pdo->prepare("INSERT INTO suivi_commande (commande_id, statut) VALUES (:commande_id, :statut)");
        $requete->execute(['commande_id' => $commande_id, 'statut' => $statut]);
    }
        // Toutes les commandes (espace employé), avec le client et le menu.
    // Les deux filtres sont facultatifs : une valeur vide veut dire « pas de filtre ».
    public function trouver_toutes($statut, $client) {
        $conditions = [];
        $parametres = [];
        if ($statut !== '') {
            $conditions[] = "commande.statut = :statut";
            $parametres['statut'] = $statut;
        }
        if ($client !== '') {
            $conditions[] = "(utilisateur.nom LIKE :client1 OR utilisateur.prenom LIKE :client2 OR utilisateur.email LIKE :client3)";
            $parametres['client1'] = '%' . $client . '%';
            $parametres['client2'] = '%' . $client . '%';
            $parametres['client3'] = '%' . $client . '%';
        }

        $sql = "SELECT commande.*, menu.titre AS menu_titre,
                       utilisateur.nom, utilisateur.prenom, utilisateur.email, utilisateur.telephone
                FROM commande
                JOIN menu ON commande.menu_id = menu.id
                JOIN utilisateur ON commande.utilisateur_id = utilisateur.id";
        if (count($conditions) > 0) {
            $sql = $sql . " WHERE " . implode(" AND ", $conditions);
        }
        $sql = $sql . " ORDER BY commande.date_prestation DESC";

        $requete = $this->pdo->prepare($sql);
        $requete->execute($parametres);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
        // Une commande précise (espace employé), avec l'email et le prénom du client
    public function trouver_par_id($commande_id) {
        $sql = "SELECT commande.*, utilisateur.email, utilisateur.prenom
                FROM commande
                JOIN utilisateur ON commande.utilisateur_id = utilisateur.id
                WHERE commande.id = :id";
        $requete = $this->pdo->prepare($sql);
        $requete->execute(['id' => $commande_id]);
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
}