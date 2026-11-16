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
        $sql = "SELECT id, titre, description, nombre_personne_minimum, prix_par_personne, image_url, quantite_restante
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
        // Un menu précis, avec le nom de son thème et de son régime.
    // Renvoie false si aucun menu ne porte ce numéro.
    public function trouver_par_id($id) {
        $sql = "SELECT menu.*, theme.libelle AS theme, regime.libelle AS regime
                FROM menu
                JOIN theme ON menu.theme_id = theme.id
                JOIN regime ON menu.regime_id = regime.id
                WHERE menu.id = :id";
        $requete = $this->pdo->prepare($sql);
        $requete->execute(['id' => $id]);
        return $requete->fetch(PDO::FETCH_ASSOC);
    }
        // Les plats d'un menu, chacun avec la liste de ses allergènes (texte vide s'il n'en a aucun)
    public function trouver_plats($menu_id) {
        $sql = "SELECT plat.id, plat.titre_plat, plat.type_plat, plat.photo_url,
                       GROUP_CONCAT(allergene.libelle ORDER BY allergene.libelle SEPARATOR ', ') AS allergenes
                FROM menu_plat
                JOIN plat ON menu_plat.plat_id = plat.id
                LEFT JOIN plat_allergene ON plat_allergene.plat_id = plat.id
                LEFT JOIN allergene ON plat_allergene.allergene_id = allergene.id
                WHERE menu_plat.menu_id = :menu_id
                GROUP BY plat.id, plat.titre_plat, plat.type_plat, plat.photo_url
                ORDER BY plat.titre_plat";
        $requete = $this->pdo->prepare($sql);
        $requete->execute(['menu_id' => $menu_id]);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
        // Les photos de la galerie d'un menu
    public function trouver_images($menu_id) {
        $sql = "SELECT chemin, texte_alternatif FROM image_menu WHERE menu_id = :menu_id ORDER BY id";
        $requete = $this->pdo->prepare($sql);
        $requete->execute(['menu_id' => $menu_id]);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}