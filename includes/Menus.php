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
        // Les menus qui correspondent aux filtres choisis.
    // $filtres contient des valeurs déjà vérifiées, par exemple ['prix_max' => 20].
    public function trouver_filtres($filtres) {
        $conditions = [];
        $parametres = [];

        if (isset($filtres['prix_min'])) {
            $conditions[] = "prix_par_personne >= :prix_min";
            $parametres['prix_min'] = $filtres['prix_min'];
        }
        if (isset($filtres['prix_max'])) {
            $conditions[] = "prix_par_personne <= :prix_max";
            $parametres['prix_max'] = $filtres['prix_max'];
        }
        if (isset($filtres['theme'])) {
            $conditions[] = "theme_id = :theme";
            $parametres['theme'] = $filtres['theme'];
        }
        if (isset($filtres['regime'])) {
            $conditions[] = "regime_id = :regime";
            $parametres['regime'] = $filtres['regime'];
        }
                if (isset($filtres['personnes'])) {
            $conditions[] = "nombre_personne_minimum <= :personnes";
            $parametres['personnes'] = $filtres['personnes'];
        }
        $sql = "SELECT id, titre, description, nombre_personne_minimum, prix_par_personne, image_url
                FROM menu";
        if (count($conditions) > 0) {
            $sql = $sql . " WHERE " . implode(" AND ", $conditions);
        }
        $sql = $sql . " ORDER BY id";

        $requete = $this->pdo->prepare($sql);
        $requete->execute($parametres);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}