<?php
// Modèle des employés : les requêtes SQL sur les comptes de l'équipe (rôle « employe »).
class Employes {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Tous les comptes employés (jamais les administrateurs ni les clients)
    public function trouver_tous() {
        $sql = "SELECT id, email, nom, prenom, actif
                FROM utilisateur
                WHERE role_id = (SELECT id FROM role WHERE libelle = 'employe')
                ORDER BY email";
        $requete = $this->pdo->query($sql);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}