<?php
// Modèle des menus : les requêtes SQL qui concernent les menus, réunies au même endroit.
class Menus {
    // La connexion à la base, gardée dans l'objet
    private $pdo;

    // Le constructeur : il s'exécute une seule fois, au moment du "new Menus($pdo)"
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Tous les menus, dans l'ordre de leur création
    public function trouver_tous() {
        $sql = "SELECT id, titre, description, nombre_personne_minimum, prix_par_personne, image_url
                FROM menu
                ORDER BY id";
        $requete = $this->pdo->query($sql);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}