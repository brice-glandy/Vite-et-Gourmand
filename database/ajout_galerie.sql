SET NAMES utf8mb4;
-- Mise à jour du 16/11/2026 : ajout de la galerie d'images des menus.
-- À exécuter UNE SEULE FOIS sur une base déjà installée (la base en ligne).
-- Une nouvelle installation n'en a pas besoin : init.sql contient déjà cette table.

CREATE TABLE image_menu (
id INT AUTO_INCREMENT PRIMARY KEY,
menu_id INT NOT NULL,
chemin VARCHAR(255) NOT NULL,
texte_alternatif VARCHAR(255) NOT NULL,
FOREIGN KEY (menu_id) REFERENCES menu(id)
);

INSERT INTO image_menu (menu_id, chemin, texte_alternatif) VALUES
(1, 'images/burger-2.jpg', 'Salade de chèvre chaud'),
(1, 'images/burger-3.jpg', 'Mousse au chocolat'),
(2, 'images/brunch-2.jpg', 'Salade de fruits frais dans un bol en bois'),
(2, 'images/brunch-3.jpg', 'Table de brunch aux plats variés'),
(3, 'images/trad-2.jpg', 'Velouté de potiron'),
(3, 'images/trad-3.jpg', 'Tarte Tatin'),
(4, 'images/ocean-2.jpg', 'Carpaccio de saumon'),
(4, 'images/brunch-2.jpg', 'Salade de fruits frais dans un bol en bois'),
(5, 'images/vegetal-2.jpg', 'Houmous et crudités'),
(5, 'images/brunch-2.jpg', 'Salade de fruits frais dans un bol en bois'),
(6, 'images/italienne-2.jpg', 'Bruschetta tomate basilic'),
(6, 'images/italienne-3.jpg', 'Tiramisu');