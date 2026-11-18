SET NAMES utf8mb4;
-- Pas de "USE" : la base est choisie par la commande qui exécute ce script
-- (MYSQL_DATABASE dans Docker, nom de la base dans la commande d'import en ligne).
CREATE TABLE role (
id INT AUTO_INCREMENT PRIMARY KEY,
libelle VARCHAR(50) NOT NULL
);

INSERT INTO role (libelle) VALUES
('administrateur'),
('employe'),
('utilisateur');

CREATE TABLE utilisateur (
id INT AUTO_INCREMENT PRIMARY KEY,
role_id INT NOT NULL,
email VARCHAR(255) NOT NULL UNIQUE,
mot_de_passe VARCHAR(255) NOT NULL,
nom VARCHAR(100),
prenom VARCHAR(100),
telephone VARCHAR(20),
adresse_postale VARCHAR(255),
ville VARCHAR(100),
pays VARCHAR(100) DEFAULT 'France',
actif BOOLEAN NOT NULL DEFAULT TRUE,
FOREIGN KEY (role_id) REFERENCES role(id)
);
CREATE TABLE theme (
id INT AUTO_INCREMENT PRIMARY KEY,
libelle VARCHAR(50) NOT NULL
);

INSERT INTO theme (libelle) VALUES
('Classique'),
('Noël'),
('Pâques'),
('Événement');

CREATE TABLE regime (
id INT AUTO_INCREMENT PRIMARY KEY,
libelle VARCHAR(50) NOT NULL
);

INSERT INTO regime (libelle) VALUES
('Classique'),
('Végétarien'),
('Vegan');

CREATE TABLE menu (
id INT AUTO_INCREMENT PRIMARY KEY,
titre VARCHAR(100) NOT NULL,
description TEXT NOT NULL,
nombre_personne_minimum INT NOT NULL,
prix_par_personne DECIMAL(10,2) NOT NULL,
quantite_restante INT NOT NULL DEFAULT 0,
conditions TEXT,
image_url VARCHAR(255),
theme_id INT NOT NULL,
regime_id INT NOT NULL,
FOREIGN KEY (theme_id) REFERENCES theme(id),
FOREIGN KEY (regime_id) REFERENCES regime(id)
);

INSERT INTO menu (titre, description, nombre_personne_minimum, prix_par_personne, quantite_restante, conditions, image_url, theme_id, regime_id) VALUES
('Burger Artisan', 'Pain brioché fait maison garni d''un steak boucher, de fromage fondu. Avec ou sans frites.', 4, 19.00, 10, 'Commande au moins 3 jours avant la prestation.', 'images/burger.jpg', 1, 1),
('Brunch Complet', 'Assortiment sucré-salé mêlant des oeufs brouillés, de l''avocat frais et des pancakes moelleux.', 4, 22.00, 8, 'Commande au moins 3 jours avant la prestation.', 'images/brunch.jpg', 3, 1),
('Tradition Française', 'Grand classique qui met à l''honneur le terroir, viande mijotée et légumes de saison.', 8, 26.00, 5, 'Commande au moins 2 semaines avant la prestation. À conserver au réfrigérateur entre 0 et 4 °C.', 'images/trad.jpg', 2, 1),
('Fraîcheur Océane', 'Sélection raffinée de poissons grillés et de fruits de mer, et risotto au citron confit.', 4, 22.00, 6, 'Commande au moins 1 semaine avant. Produits de la mer à consommer le jour même.', 'images/ocean.jpg', 4, 1),
('Plaisir Végétal', 'Composition gourmande à base de céréales anciennes, de légumes croquants et de houmous maison.', 4, 16.00, 12, 'Commande au moins 3 jours avant la prestation.', 'images/vegetal.jpg', 1, 3),
('Evasion Italienne', 'Pâtes fraîches enrobées d''une sauce au pesto de basilic et de copeaux de parmesan.', 8, 21.00, 7, 'Commande au moins 1 semaine avant la prestation.', 'images/italienne.jpg', 1, 2);
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
CREATE TABLE plat (
id INT AUTO_INCREMENT PRIMARY KEY,
titre_plat VARCHAR(100) NOT NULL,
type_plat VARCHAR(20) NOT NULL,
photo_url VARCHAR(255)
);

CREATE TABLE allergene (
id INT AUTO_INCREMENT PRIMARY KEY,
libelle VARCHAR(50) NOT NULL
);

CREATE TABLE menu_plat (
menu_id INT NOT NULL,
plat_id INT NOT NULL,
par_defaut BOOLEAN NOT NULL DEFAULT FALSE,
PRIMARY KEY (menu_id, plat_id),
FOREIGN KEY (menu_id) REFERENCES menu(id),
FOREIGN KEY (plat_id) REFERENCES plat(id)
);

CREATE TABLE plat_allergene (
plat_id INT NOT NULL,
allergene_id INT NOT NULL,
PRIMARY KEY (plat_id, allergene_id),
FOREIGN KEY (plat_id) REFERENCES plat(id),
FOREIGN KEY (allergene_id) REFERENCES allergene(id)
);

INSERT INTO plat (titre_plat, type_plat) VALUES
('Salade de chèvre chaud', 'Entrée'),
('Burger boucher et frites', 'Plat'),
('Mousse au chocolat', 'Dessert'),
('Pancakes et oeufs brouillés', 'Plat'),
('Salade de fruits frais', 'Dessert'),
('Velouté de potiron', 'Entrée'),
('Boeuf bourguignon', 'Plat'),
('Tarte Tatin', 'Dessert'),
('Carpaccio de saumon', 'Entrée'),
('Poissons grillés et risotto au citron', 'Plat'),
('Houmous et crudités', 'Entrée'),
('Bowl de céréales anciennes', 'Plat'),
('Bruschetta tomate basilic', 'Entrée'),
('Pâtes fraîches au pesto', 'Plat'),
('Tiramisu', 'Dessert');

INSERT INTO allergene (libelle) VALUES
('Gluten'),
('Crustacés'),
('Oeufs'),
('Poissons'),
('Lait'),
('Fruits à coque'),
('Moutarde'),
('Sésame'),
('Céleri');

INSERT INTO menu_plat (menu_id, plat_id) VALUES
(1, 1), (1, 2), (1, 3),
(2, 4), (2, 5),
(3, 6), (3, 7), (3, 8),
(4, 9), (4, 10), (4, 5),
(5, 11), (5, 12), (5, 5),
(6, 13), (6, 14), (6, 15);
-- Les plats ci-dessus sont ceux d'origine : ils deviennent les choix par défaut
UPDATE menu_plat SET par_defaut = TRUE;

-- Les alternatives proposées au client (même régime, même prix de menu)
INSERT INTO menu_plat (menu_id, plat_id) VALUES
(1, 13), (1, 5),
(2, 8),
(3, 9), (3, 3),
(4, 6), (4, 8),
(5, 13),
(6, 1), (6, 3);

INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(1, 1), (1, 5),
(2, 1), (2, 5), (2, 7),
(3, 3), (3, 5),
(4, 1), (4, 3), (4, 5),
(6, 5),
(7, 9),
(8, 1), (8, 3), (8, 5),
(9, 4),
(10, 2), (10, 4), (10, 5),
(11, 8),
(12, 1),
(13, 1),
(14, 1), (14, 3), (14, 5), (14, 6),
(15, 1), (15, 3), (15, 5);
CREATE TABLE commande (
id INT AUTO_INCREMENT PRIMARY KEY,
numero_commande VARCHAR(50) NOT NULL UNIQUE,
utilisateur_id INT NOT NULL,
menu_id INT NOT NULL,
date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
date_prestation DATE NOT NULL,
heure_livraison TIME NOT NULL,
adresse_livraison VARCHAR(255) NOT NULL,
ville_livraison VARCHAR(100) NOT NULL,
nombre_personne INT NOT NULL,
prix_menu DECIMAL(10,2) NOT NULL,
prix_livraison DECIMAL(10,2) NOT NULL DEFAULT 0,
prix_total DECIMAL(10,2) NOT NULL,
statut VARCHAR(50) NOT NULL DEFAULT 'en attente',
pret_materiel BOOLEAN NOT NULL DEFAULT FALSE,
restitution_materiel BOOLEAN NOT NULL DEFAULT FALSE,
mode_contact VARCHAR(20),
motif_annulation TEXT,
FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id),
FOREIGN KEY (menu_id) REFERENCES menu(id)
);

INSERT INTO utilisateur (role_id, email, mot_de_passe, nom, prenom, telephone, adresse_postale, ville) VALUES
(1, 'jose@viteetgourmand.fr', '$2y$10$88ZH8iEyLxvp401u1jZcdOPQL2yeS7bfFeWAtBf8RDy5lClM.YmOC', NULL, 'José', NULL, NULL, NULL),
(2, 'julie@viteetgourmand.fr', '$2y$10$2BREdii0af4nnr32YXLFGu3ju9XcJZtjYR.o4iP/cXyH06wfrjPK.', NULL, 'Julie', NULL, NULL, NULL),
(3, 'marie.dupont@mail.fr', '$2y$10$xFnzwxkLo5Bp2vy2KoMbE.o.DiGUG7elHzh/bB0oo6FqHUSGnJvki', 'Dupont', 'Marie', '0601020304', '12 rue Sainte-Catherine', 'Bordeaux'),
(3, 'thomas.martin@mail.fr', '$2y$10$w5BD8vjWShde8Mc6xYIeAeAJtK6Dka4oVxLHz5a1FHHahiOvB07US', 'Martin', 'Thomas', '0605060708', '8 avenue de la Marne', 'Mérignac');

INSERT INTO commande (numero_commande, utilisateur_id, menu_id, date_commande, date_prestation, heure_livraison, adresse_livraison, ville_livraison, nombre_personne, prix_menu, prix_livraison, prix_total, statut, pret_materiel, restitution_materiel) VALUES
('CMD-2026-0001', 3, 1, '2026-10-02 10:15:00', '2026-10-10', '12:00:00', '12 rue Sainte-Catherine', 'Bordeaux', 6, 114.00, 0.00, 114.00, 'terminée', FALSE, FALSE),
('CMD-2026-0002', 4, 3, '2026-10-20 14:30:00', '2026-11-01', '19:30:00', '8 avenue de la Marne', 'Mérignac', 14, 327.60, 12.08, 339.68, 'en attente du retour de matériel', TRUE, FALSE),
('CMD-2026-0003', 3, 4, '2026-11-05 09:00:00', '2026-11-14', '12:30:00', '12 rue Sainte-Catherine', 'Bordeaux', 4, 88.00, 0.00, 88.00, 'en préparation', FALSE, FALSE),
('CMD-2026-0004', 4, 6, '2026-11-10 18:45:00', '2026-11-28', '20:00:00', '8 avenue de la Marne', 'Mérignac', 10, 210.00, 12.08, 222.08, 'en attente', FALSE, FALSE);
CREATE TABLE horaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jour VARCHAR(20) NOT NULL,
    ouverture_matin TIME,
    fermeture_matin TIME,
    ouverture_soir TIME,
    fermeture_soir TIME,
    est_ferme BOOLEAN DEFAULT FALSE
);

INSERT INTO horaires (jour, ouverture_matin, fermeture_matin, ouverture_soir, fermeture_soir, est_ferme) VALUES
('Lundi', NULL, NULL, NULL, NULL, TRUE),
('Mardi', '08:00:00', '14:30:00', '17:00:00', '22:30:00', FALSE),
('Mercredi', '08:00:00', '14:30:00', '17:00:00', '22:30:00', FALSE),
('Jeudi', '08:00:00', '14:30:00', '17:00:00', '22:30:00', FALSE),
('Vendredi', '08:00:00', '14:30:00', '17:00:00', '22:30:00', FALSE),
('Samedi', '08:00:00', '14:30:00', '17:00:00', '22:30:00', FALSE),
('Dimanche', '08:00:00', '14:30:00', NULL, NULL, FALSE);

INSERT INTO utilisateur (role_id, email, mot_de_passe, nom, prenom, telephone, adresse_postale, ville) VALUES
(3, 'sophie.laurent@mail.fr', '$2y$10$P9uo6TFV24ljlqVBK/DtfuKh0qj2rmi.HYsuVLDsGbbOwyuk/NJNS', 'Laurent', 'Sophie', '0611223344', '5 cours de l''Intendance', 'Bordeaux'),
(3, 'daniele.durand@mail.fr', '$2y$10$ne4a68OG4x2cL1499bIxMOScOyfFjoTFEdUFxCp9MmFnZo.Ae..V6', 'Durand', 'Danièle', '0622334455', '14 avenue Jean Jaurès', 'Pessac'),
(3, 'christine.thomas@mail.fr', '$2y$10$QMDHUkRviAB.bHW2RQqrD.D5Fx5JVtUW1CYXK7kncU3THYj0nqOLe', 'Thomas', 'Christine', '0633445566', '3 place des Quinconces', 'Bordeaux'),
(3, 'marc.dubois@mail.fr', '$2y$10$7V.dEBdw.xtEJMtK7sHNWeBkJgBgqqP99l7EaFB73IjH1.hHeFc4O', 'Dubois', 'Marc', '0644556677', '22 rue Pasteur', 'Talence');

INSERT INTO commande (numero_commande, utilisateur_id, menu_id, date_commande, date_prestation, heure_livraison, adresse_livraison, ville_livraison, nombre_personne, prix_menu, prix_livraison, prix_total, statut, pret_materiel, restitution_materiel) VALUES
('CMD-2026-0005', 5, 1, '2026-09-05 11:00:00', '2026-09-13', '19:00:00', '5 cours de l''Intendance', 'Bordeaux', 8, 152.00, 0.00, 152.00, 'terminée', FALSE, FALSE),
('CMD-2026-0006', 6, 6, '2026-09-12 16:20:00', '2026-09-20', '12:30:00', '14 avenue Jean Jaurès', 'Pessac', 8, 168.00, 10.31, 178.31, 'terminée', FALSE, FALSE),
('CMD-2026-0007', 7, 3, '2026-09-25 10:00:00', '2026-10-04', '12:00:00', '3 place des Quinconces', 'Bordeaux', 10, 260.00, 0.00, 260.00, 'terminée', TRUE, TRUE),
('CMD-2026-0008', 8, 4, '2026-10-08 09:30:00', '2026-10-18', '19:30:00', '22 rue Pasteur', 'Talence', 9, 178.20, 8.54, 186.74, 'terminée', FALSE, FALSE);
-- Commandes de démonstration supplémentaires : un exemple pour chaque statut
INSERT INTO commande (numero_commande, utilisateur_id, menu_id, date_commande, date_prestation, heure_livraison, adresse_livraison, ville_livraison, nombre_personne, prix_menu, prix_livraison, prix_total, statut, pret_materiel, restitution_materiel) VALUES
('CMD-2026-0009', 5, 2, '2026-11-12 10:00:00', '2026-11-22', '11:00:00', '5 cours de l''Intendance', 'Bordeaux', 4, 88.00, 0.00, 88.00, 'accepté', FALSE, FALSE),
('CMD-2026-0010', 6, 5, '2026-11-08 15:00:00', '2026-11-17', '12:30:00', '14 avenue Jean Jaurès', 'Pessac', 6, 96.00, 10.31, 106.31, 'en cours de livraison', FALSE, FALSE),
('CMD-2026-0011', 8, 4, '2026-11-05 18:00:00', '2026-11-15', '19:30:00', '22 rue Pasteur', 'Talence', 6, 132.00, 8.54, 140.54, 'livré', FALSE, FALSE),
('CMD-2026-0012', 7, 1, '2026-11-09 09:00:00', '2026-11-21', '19:00:00', '3 place des Quinconces', 'Bordeaux', 5, 95.00, 0.00, 95.00, 'annulée', FALSE, FALSE),
('CMD-2026-0013', 4, 3, '2026-11-15 20:00:00', '2026-12-19', '19:30:00', '8 avenue de la Marne', 'Mérignac', 8, 208.00, 12.08, 220.08, 'en attente', FALSE, FALSE);
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    commande_id INT NOT NULL UNIQUE,
    note INT NOT NULL,
    commentaire TEXT NOT NULL,
    statut VARCHAR(20) NOT NULL DEFAULT 'en attente',
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    CHECK (note BETWEEN 1 AND 5),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id),
    FOREIGN KEY (commande_id) REFERENCES commande(id)
);

INSERT INTO avis (utilisateur_id, commande_id, note, commentaire, statut, date_publication) VALUES
(5, 5, 5, 'Le menu Burger Artisan a fait l''unanimité auprès de tous nos invités lors de notre soirée. Les ingrédients sont très frais et le pain maison fait vraiment la différence.', 'validé', '2026-09-15 20:00:00'),
(6, 6, 5, 'Nous avons voyagé avec le menu Évasion Italienne, généreux et plein de saveurs authentiques. L''organisation de Vite & Gourmand était parfaite, de la commande jusqu''à la livraison.', 'validé', '2026-09-22 10:00:00'),
(7, 7, 5, 'J''ai commandé le menu Tradition Française pour un repas de famille et les cuissons étaient maîtrisées à la perfection. Un service traiteur d''une grande qualité que je recommande les yeux fermés.', 'validé', '2026-10-06 18:00:00'),
(8, 8, 5, 'Le menu Fraîcheur Océane était un pur délice, mettant en valeur des produits de la mer d''une excellente qualité. Merci à toute l''équipe pour ce repas gourmand et le soin apporté à la présentation.', 'validé', '2026-10-20 12:00:00'),
(3, 1, 4, 'Très bon burger, livraison à l''heure. Un peu plus de frites la prochaine fois !', 'en attente', '2026-10-12 09:30:00');

-- Les plats choisis pour chaque commande
CREATE TABLE commande_plat (
commande_id INT NOT NULL,
plat_id INT NOT NULL,
PRIMARY KEY (commande_id, plat_id),
FOREIGN KEY (commande_id) REFERENCES commande(id),
FOREIGN KEY (plat_id) REFERENCES plat(id)
);

-- Les commandes déjà passées reçoivent les plats par défaut de leur menu
INSERT INTO commande_plat (commande_id, plat_id)
SELECT commande.id, menu_plat.plat_id
FROM commande
JOIN menu_plat ON menu_plat.menu_id = commande.menu_id
WHERE menu_plat.par_defaut = TRUE;
-- Le suivi des commandes : une ligne par changement de statut, avec la date et l'heure
CREATE TABLE suivi_commande (
id INT AUTO_INCREMENT PRIMARY KEY,
commande_id INT NOT NULL,
statut VARCHAR(50) NOT NULL,
date_changement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (commande_id) REFERENCES commande(id)
);

-- Historique complet des commandes de démonstration, étape par étape.
-- Les dates sont reconstituées à partir de la date de commande et de la prestation.
-- (Les vraies commandes, elles, enregistrent chaque changement au moment où il a lieu.)

-- 1. Création : « en attente », à la date de la commande
INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'en attente', date_commande FROM commande;

-- 2. Acceptée le lendemain
INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'accepté', date_commande + INTERVAL 1 DAY
FROM commande WHERE statut NOT IN ('en attente', 'annulée');

-- 3. En préparation la veille de la prestation, à 9h
INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'en préparation', TIMESTAMP(date_prestation - INTERVAL 1 DAY, '09:00:00')
FROM commande WHERE statut NOT IN ('en attente', 'accepté', 'annulée');

-- 4. En cours de livraison une heure avant l'heure prévue
INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'en cours de livraison', TIMESTAMP(date_prestation, heure_livraison) - INTERVAL 1 HOUR
FROM commande WHERE statut IN ('en cours de livraison', 'livré', 'en attente du retour de matériel', 'terminée');

-- 5. Livrée à l'heure prévue
INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'livré', TIMESTAMP(date_prestation, heure_livraison)
FROM commande WHERE statut IN ('livré', 'en attente du retour de matériel', 'terminée');

-- 6. En attente du retour de matériel (seulement si du matériel a été prêté), le lendemain à 10h
INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'en attente du retour de matériel', TIMESTAMP(date_prestation + INTERVAL 1 DAY, '10:00:00')
FROM commande WHERE pret_materiel = TRUE AND statut IN ('en attente du retour de matériel', 'terminée');

-- 7. Terminée : le lendemain à 10h, ou 3 jours après si du matériel devait être rendu
INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'terminée', TIMESTAMP(date_prestation + INTERVAL IF(pret_materiel, 3, 1) DAY, '10:00:00')
FROM commande WHERE statut = 'terminée';

-- 8. Annulée par le client deux jours après la commande
INSERT INTO suivi_commande (commande_id, statut, date_changement)
SELECT id, 'annulée', date_commande + INTERVAL 2 DAY
FROM commande WHERE statut = 'annulée';

-- Réinitialisation des mots de passe : un jeton par demande (seule son empreinte est stockée)
CREATE TABLE reinitialisation_mot_de_passe (
id INT AUTO_INCREMENT PRIMARY KEY,
utilisateur_id INT NOT NULL,
jeton_hash CHAR(64) NOT NULL,
date_expiration DATETIME NOT NULL,
FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
);