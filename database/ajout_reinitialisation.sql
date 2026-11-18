SET NAMES utf8mb4;
-- Mise à jour du 18/11/2026 : réinitialisation des mots de passe (mot de passe oublié).
-- À exécuter UNE SEULE FOIS sur une base déjà installée (la base en ligne).
-- Une nouvelle installation n'en a pas besoin : init.sql contient déjà cette table.

CREATE TABLE reinitialisation_mot_de_passe (
id INT AUTO_INCREMENT PRIMARY KEY,
utilisateur_id INT NOT NULL,
jeton_hash CHAR(64) NOT NULL,
date_expiration DATETIME NOT NULL,
FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
);