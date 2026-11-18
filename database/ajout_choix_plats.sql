SET NAMES utf8mb4;
-- Mise à jour du 16/11/2026 : choix des plats dans une commande.
-- À exécuter UNE SEULE FOIS sur une base déjà installée (la base en ligne).
-- Une nouvelle installation n'en a pas besoin : init.sql contient déjà ces changements.

-- 1. Les plats d'origine de chaque menu deviennent les choix par défaut
ALTER TABLE menu_plat ADD par_defaut BOOLEAN NOT NULL DEFAULT FALSE;
UPDATE menu_plat SET par_defaut = TRUE;

-- 2. Les alternatives proposées au client (même régime, même prix de menu)
INSERT INTO menu_plat (menu_id, plat_id) VALUES
(1, 13), (1, 5),
(2, 8),
(3, 9), (3, 3),
(4, 6), (4, 8),
(5, 13),
(6, 1), (6, 3);

-- 3. Les plats choisis pour chaque commande
CREATE TABLE commande_plat (
commande_id INT NOT NULL,
plat_id INT NOT NULL,
PRIMARY KEY (commande_id, plat_id),
FOREIGN KEY (commande_id) REFERENCES commande(id),
FOREIGN KEY (plat_id) REFERENCES plat(id)
);

-- 4. Les commandes déjà passées (y compris celles passées en ligne) reçoivent les plats par défaut
INSERT INTO commande_plat (commande_id, plat_id)
SELECT commande.id, menu_plat.plat_id
FROM commande
JOIN menu_plat ON menu_plat.menu_id = commande.menu_id
WHERE menu_plat.par_defaut = TRUE;