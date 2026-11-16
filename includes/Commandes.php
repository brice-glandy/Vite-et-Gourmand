<?php
// Modèle des commandes : les requêtes SQL qui concernent les commandes, réunies au même endroit.
class Commandes {
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
}