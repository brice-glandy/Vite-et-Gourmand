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
        // Tous les menus pour l'administration, avec le nom de leur thème et de leur régime
    public function trouver_tous_admin() {
        $sql = "SELECT menu.id, menu.titre, menu.prix_par_personne, menu.nombre_personne_minimum,
                       menu.quantite_restante, theme.libelle AS theme, regime.libelle AS regime
                FROM menu
                JOIN theme ON menu.theme_id = theme.id
                JOIN regime ON menu.regime_id = regime.id
                ORDER BY menu.id";
        $requete = $this->pdo->query($sql);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
        // La liste des thèmes, pour les listes déroulantes
    public function trouver_themes() {
        $requete = $this->pdo->query("SELECT id, libelle FROM theme ORDER BY libelle");
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    // La liste des régimes, pour les listes déroulantes
    public function trouver_regimes() {
        $requete = $this->pdo->query("SELECT id, libelle FROM regime ORDER BY libelle");
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
        // Enregistre un nouveau menu. $donnees contient les 9 valeurs déjà vérifiées.
    public function creer($donnees) {
        $sql = "INSERT INTO menu (titre, description, nombre_personne_minimum, prix_par_personne,
                                  quantite_restante, conditions, image_url, theme_id, regime_id)
                VALUES (:titre, :description, :nombre_personne_minimum, :prix_par_personne,
                        :quantite_restante, :conditions, :image_url, :theme_id, :regime_id)";
        $requete = $this->pdo->prepare($sql);
        $requete->execute($donnees);
        return (int) $this->pdo->lastInsertId();
    }
        // Modifie un menu existant. $donnees contient les mêmes 9 valeurs que pour creer().
    public function modifier($id, $donnees) {
        $sql = "UPDATE menu
                SET titre = :titre,
                    description = :description,
                    nombre_personne_minimum = :nombre_personne_minimum,
                    prix_par_personne = :prix_par_personne,
                    quantite_restante = :quantite_restante,
                    conditions = :conditions,
                    image_url = :image_url,
                    theme_id = :theme_id,
                    regime_id = :regime_id
                WHERE id = :id";
        $donnees['id'] = $id;
        $requete = $this->pdo->prepare($sql);
        $requete->execute($donnees);
    }
        // Vrai si ce menu a déjà été commandé au moins une fois
    public function est_commande($id) {
        $requete = $this->pdo->prepare("SELECT COUNT(*) FROM commande WHERE menu_id = :id");
        $requete->execute(['id' => $id]);
        return $requete->fetchColumn() > 0;
    }

    // Retire un menu de la vente sans l'effacer : plus aucune commande possible
    public function retirer_de_la_vente($id) {
        $requete = $this->pdo->prepare("UPDATE menu SET quantite_restante = 0 WHERE id = :id");
        $requete->execute(['id' => $id]);
    }

    // Efface un menu jamais commandé, avec ses plats associés et ses photos de galerie (tout ou rien)
    public function supprimer($id) {
        $this->pdo->beginTransaction();
        try {
            $parametres = ['id' => $id];
            $this->pdo->prepare("DELETE FROM menu_plat WHERE menu_id = :id")->execute($parametres);
            $this->pdo->prepare("DELETE FROM image_menu WHERE menu_id = :id")->execute($parametres);
            $this->pdo->prepare("DELETE FROM menu WHERE id = :id")->execute($parametres);
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
        // Les plats associés à un menu : pour chaque numéro de plat, vrai s'il est le plat par défaut
    // Exemple : [1 => true, 13 => false] (plat 1 par défaut, plat 13 proposé au choix)
    public function trouver_associations_plats($menu_id) {
        $requete = $this->pdo->prepare("SELECT plat_id, par_defaut FROM menu_plat WHERE menu_id = :menu_id");
        $requete->execute(['menu_id' => $menu_id]);
        $associations = [];
        foreach ($requete->fetchAll(PDO::FETCH_ASSOC) as $ligne) {
            $associations[$ligne['plat_id']] = (bool) $ligne['par_defaut'];
        }
        return $associations;
    }
    // Remplace les plats d'un menu (tout ou rien).
    // $proposes : numéros des plats proposés ; $defauts : numéros des plats par défaut (un par type proposé)
    public function enregistrer_plats($menu_id, $proposes, $defauts) {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("DELETE FROM menu_plat WHERE menu_id = :menu_id")->execute(['menu_id' => $menu_id]);
            $requete = $this->pdo->prepare("INSERT INTO menu_plat (menu_id, plat_id, par_defaut) VALUES (:menu_id, :plat_id, :par_defaut)");
            foreach ($proposes as $plat_id) {
                $requete->execute([
                    'menu_id' => $menu_id,
                    'plat_id' => $plat_id,
                    'par_defaut' => in_array($plat_id, $defauts, true) ? 1 : 0
                ]);
            }
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}