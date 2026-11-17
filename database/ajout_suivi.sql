SET NAMES utf8mb4;
-- Mise à jour du 17/11/2026 : suivi des commandes (historique des statuts avec date et heure).
-- À exécuter UNE SEULE FOIS sur une base déjà installée (la base en ligne).
-- Une nouvelle installation n'en a pas besoin : init.sql contient déjà cette table.

CREATE TABLE suivi_commande (
id INT AUTO_INCREMENT PRIMARY KEY,
commande_id INT NOT NULL,
statut VARCHAR(50) NOT NULL,
date_changement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (commande_id) REFERENCES commande(id)
);

-- Historique reconstitué des commandes déjà présentes (mêmes règles que dans init.sql)
INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'en attente', date_commande FROM commande;

INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'accepté', date_commande + INTERVAL 1 DAY
FROM commande WHERE statut NOT IN ('en attente', 'annulée');

INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'en préparation', TIMESTAMP(date_prestation - INTERVAL 1 DAY, '09:00:00')
FROM commande WHERE statut NOT IN ('en attente', 'accepté', 'annulée');

INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'en cours de livraison', TIMESTAMP(date_prestation, heure_livraison) - INTERVAL 1 HOUR
FROM commande WHERE statut IN ('en cours de livraison', 'livré', 'en attente du retour de matériel', 'terminée');

INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'livré', TIMESTAMP(date_prestation, heure_livraison)
FROM commande WHERE statut IN ('livré', 'en attente du retour de matériel', 'terminée');

INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'en attente du retour de matériel', TIMESTAMP(date_prestation + INTERVAL 1 DAY, '10:00:00')
FROM commande WHERE pret_materiel = TRUE AND statut IN ('en attente du retour de matériel', 'terminée');

INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'terminée', TIMESTAMP(date_prestation + INTERVAL IF(pret_materiel, 3, 1) DAY, '10:00:00')
FROM commande WHERE statut = 'terminée';

INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'annulée', date_commande + INTERVAL 2 DAY
FROM commande WHERE statut = 'annulée';