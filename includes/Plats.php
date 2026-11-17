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
        // Un plat précis (false s'il n'existe pas)
    public function trouver_par_id($id) {
        $requete = $this->pdo->prepare("SELECT id, titre_plat, type_plat FROM plat WHERE id = :id");
        $requete->execute(['id' => $id]);
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    // Tous les allergènes, pour les cases à cocher du formulaire
    public function trouver_allergenes() {
        $requete = $this->pdo->query("SELECT id, libelle FROM allergene ORDER BY libelle");
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    // Les numéros des allergènes d'un plat, par exemple [1, 5] pour Gluten et Lait
    public function trouver_ids_allergenes($plat_id) {
        $requete = $this->pdo->prepare("SELECT allergene_id FROM plat_allergene WHERE plat_id = :plat_id");
        $requete->execute(['plat_id' => $plat_id]);
        return $requete->fetchAll(PDO::FETCH_COLUMN);
    }
        // Enregistre un nouveau plat et ses allergènes (tout ou rien)
    public function creer($titre, $type, $allergenes) {
        $this->pdo->beginTransaction();
        try {
            $requete = $this->pdo->prepare("INSERT INTO plat (titre_plat, type_plat) VALUES (:titre, :type)");
            $requete->execute(['titre' => $titre, 'type' => $type]);
            $plat_id = $this->pdo->lastInsertId();
            $this->enregistrer_allergenes($plat_id, $allergenes);
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Modifie un plat et remplace ses allergènes (tout ou rien)
    public function modifier($id, $titre, $type, $allergenes) {
        $this->pdo->beginTransaction();
        try {
            $requete = $this->pdo->prepare("UPDATE plat SET titre_plat = :titre, type_plat = :type WHERE id = :id");
            $requete->execute(['titre' => $titre, 'type' => $type, 'id' => $id]);
            $this->pdo->prepare("DELETE FROM plat_allergene WHERE plat_id = :id")->execute(['id' => $id]);
            $this->enregistrer_allergenes($id, $allergenes);
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Écrit une ligne dans plat_allergene pour chaque allergène coché
    private function enregistrer_allergenes($plat_id, $allergenes) {
        $requete = $this->pdo->prepare("INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (:plat_id, :allergene_id)");
        foreach ($allergenes as $allergene_id) {
            $requete->execute(['plat_id' => $plat_id, 'allergene_id' => $allergene_id]);
        }
    }
}