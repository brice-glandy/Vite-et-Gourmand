<?php
// Modèle des plats : les requêtes SQL qui concernent les plats et leurs allergènes.
class Plats {
    // Les types de plats, dans l'ordre du repas
    const TYPES = ['Entrée', 'Plat', 'Dessert'];

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Tous les plats, dans l'ordre du repas puis par titre, chacun avec la liste de ses allergènes
    public function trouver_tous() {
        $sql = "SELECT plat.id, plat.titre_plat, plat.type_plat,
                       GROUP_CONCAT(allergene.libelle ORDER BY allergene.libelle SEPARATOR ', ') AS allergenes
                FROM plat
                LEFT JOIN plat_allergene ON plat_allergene.plat_id = plat.id
                LEFT JOIN allergene ON plat_allergene.allergene_id = allergene.id
                GROUP BY plat.id, plat.titre_plat, plat.type_plat
                ORDER BY FIELD(plat.type_plat, 'Entrée', 'Plat', 'Dessert'), plat.titre_plat";
        $requete = $this->pdo->query($sql);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}