<?php
// Modèle des réinitialisations de mot de passe : les jetons envoyés par mail.
// La base ne garde que l'empreinte du jeton (comme pour les mots de passe).
class Reinitialisations {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Crée un jeton valable 1 heure pour cet utilisateur, et le renvoie (en clair, pour le lien du mail)
    public function creer_jeton($utilisateur_id) {
        // Les anciennes demandes de cet utilisateur ne servent plus
        $this->pdo->prepare("DELETE FROM reinitialisation_mot_de_passe WHERE utilisateur_id = :id")->execute(['id' => $utilisateur_id]);

        $jeton = bin2hex(random_bytes(32));
        $requete = $this->pdo->prepare("INSERT INTO reinitialisation_mot_de_passe (utilisateur_id, jeton_hash, date_expiration)
                                        VALUES (:id, :jeton_hash, NOW() + INTERVAL 1 HOUR)");
        $requete->execute(['id' => $utilisateur_id, 'jeton_hash' => hash('sha256', $jeton)]);
        return $jeton;
    }

    // Le numéro de l'utilisateur à qui appartient ce jeton, s'il est encore valable (sinon false)
    public function trouver_utilisateur($jeton) {
        $requete = $this->pdo->prepare("SELECT utilisateur_id FROM reinitialisation_mot_de_passe
                                        WHERE jeton_hash = :jeton_hash AND date_expiration > NOW()");
        $requete->execute(['jeton_hash' => hash('sha256', $jeton)]);
        return $requete->fetchColumn();
    }

    // Change le mot de passe et efface les jetons de l'utilisateur (tout ou rien)
    public function changer_mot_de_passe($utilisateur_id, $mot_de_passe) {
        $this->pdo->beginTransaction();
        try {
            $requete = $this->pdo->prepare("UPDATE utilisateur SET mot_de_passe = :mot_de_passe WHERE id = :id");
            $requete->execute(['mot_de_passe' => password_hash($mot_de_passe, PASSWORD_DEFAULT), 'id' => $utilisateur_id]);
            $this->pdo->prepare("DELETE FROM reinitialisation_mot_de_passe WHERE utilisateur_id = :id")->execute(['id' => $utilisateur_id]);
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}