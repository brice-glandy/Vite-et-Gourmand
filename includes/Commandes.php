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
}