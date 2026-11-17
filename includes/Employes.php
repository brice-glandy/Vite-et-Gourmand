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
        // Vrai si un compte (quel que soit son rôle) utilise déjà cet email
    public function email_existe($email) {
        $requete = $this->pdo->prepare("SELECT id FROM utilisateur WHERE email = :email");
        $requete->execute(['email' => $email]);
        return $requete->fetch() !== false;
    }

    // Crée un compte employé. Le rôle est écrit ici, en dur : l'application
    // ne peut créer QUE des employés, jamais un administrateur (demande de José).
    public function creer($email, $mot_de_passe) {
        $sql = "INSERT INTO utilisateur (role_id, email, mot_de_passe)
                VALUES ((SELECT id FROM role WHERE libelle = 'employe'), :email, :mot_de_passe)";
        $requete = $this->pdo->prepare($sql);
        $requete->execute([
            'email' => $email,
            'mot_de_passe' => password_hash($mot_de_passe, PASSWORD_DEFAULT)
        ]);
    }

    // Active (1) ou désactive (0) un compte, SEULEMENT si c'est un compte employé
    public function changer_actif($employe_id, $actif) {
        $sql = "UPDATE utilisateur SET actif = :actif
                WHERE id = :id AND role_id = (SELECT id FROM role WHERE libelle = 'employe')";
        $requete = $this->pdo->prepare($sql);
        $requete->execute(['actif' => $actif, 'id' => $employe_id]);
    }
}