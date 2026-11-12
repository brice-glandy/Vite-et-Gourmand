# Notes de reprise – ECF Vite & Gourmand (DWWM Studi)

Fichier de suivi du projet. À lire en début de séance (ou à donner à Claude dans une nouvelle conversation, avec l'accès au dossier `v_et_g`).

Dernière mise à jour : mercredi 11 novembre 2026, 23h20.

---

## 1. Échéance

- Date limite Studi : **jeudi 19 novembre 2026 à 23h59**.
- Objectif personnel : **rendre le mercredi 18 novembre** (le jeudi 19 sert de journée de sécurité).

## 2. Méthode de travail (à respecter)

- Avancer par **petits blocs**, jamais une page entière d'un coup.
- **Squelette d'abord** : HTML, puis CSS fidèle à la maquette, puis JavaScript en dernier.
- Stack au niveau DWWM : HTML5 sémantique, CSS3 (Flexbox/Grid), JavaScript natif avec Fetch, PHP avec PDO, MySQL. Pas de raccourcis syntaxiques avancés.
- **Expliquer chaque script** étape par étape, concrètement, sans jargon.
- Donner pour chaque choix technique **l'argument à présenter à l'oral**.
- **Droit de veto** : si un code paraît illogique ou trop abstrait, il est refusé et remplacé par une approche plus directe.
- Format des explications : texte simple, paragraphes ou listes à puces. **Pas de tableaux.**
- Claude lit les fichiers du projet, mais c'est Brice qui tape et applique les modifications (sauf demande explicite).

## 3. Environnement de travail

- Dossier local : `C:\projets web et mobile\v_et_g`
- Docker (`docker-compose.yml`) :
  - service `web` : PHP 8.2 + Apache (Dockerfile avec `pdo_mysql`), port **8000**
  - service `db` : MySQL 8.0, utf8mb4, port **3307** côté PC, base `v_et_g`, utilisateur `root` / mot de passe `root`
  - `database/init.sql` est monté dans `/docker-entrypoint-initdb.d/` : il s'exécute **uniquement quand le volume est vide**
- Recréer la base après une modification de `init.sql` : `docker compose down -v` puis `docker compose up -d` (efface toutes les données saisies depuis le site)
- Voir les erreurs MySQL : `docker compose logs db`
- Sur PC : `http://localhost:8000/admin.php`
- Sur iPhone (même Wi-Fi) : `http://192.168.1.60:8000/admin.php` (vérifier l'IP avec `ipconfig` si besoin)
- Recharger sans cache dans Chrome : **Ctrl + Fn + F5**
- Live Server (port 5500) ne sert plus : il ne sait pas exécuter le PHP.
- Connexion PHP : `includes/db.php` (PDO, hôte `db`, `charset=utf8mb4`, mode exceptions)

## 4. GitHub et gestion de projet

- Dépôt : https://github.com/brice-glandy/Vite-et-Gourmand (public)
- Branches existantes sur GitHub : `main` et `dev` (un seul commit « Initial commit » avec README.md)
- **Le dossier local `v_et_g` n'est pas encore relié au dépôt** : étape prévue le jeudi 12 au matin.
- Organisation prévue : `main` (stable) ← `dev` (développement) ← `feature/...` (une branche par fonctionnalité)
- Git installé (2.55), nom et email déjà configurés.
- Trello « ECF - Vite & Gourmand » : 19 cartes. À mettre à jour (cartes 1 à 8 en grande partie terminées).
- Figma : wireframes et mockups desktop + mobile des 3 pages principales (vue globale des menus, commande, tableau de bord admin).

## 5. Base de données (réalignée sur le MCD de l'énoncé, page 10)

Tables, dans l'ordre de `init.sql` :

- `role` (administrateur = 1, employe = 2, utilisateur = 3)
- `utilisateur` (email unique, mot de passe haché, nom, prénom, téléphone, adresse, ville, pays, `actif`)
- `theme` (Classique, Noël, Pâques, Événement)
- `regime` (Classique, Végétarien, Vegan)
- `menu` (titre, description, nb personnes minimum, prix par personne, quantité restante, conditions, image, `theme_id`, `regime_id`)
- `plat` (titre, type Entrée/Plat/Dessert, photo)
- `allergene`
- `menu_plat` (liaison menu ↔ plat, clé primaire composée)
- `plat_allergene` (liaison plat ↔ allergène, clé primaire composée)
- `commande` (numéro unique, utilisateur, menu, dates, heure, adresse, ville, nb personnes, prix menu / livraison / total, statut, prêt et restitution de matériel, mode de contact, motif d'annulation)
- `horaires` (jour, ouverture/fermeture matin et soir en TIME, `est_ferme`)
- `avis` (utilisateur, commande unique, note 1 à 5 avec CHECK, commentaire, statut en attente / validé / refusé, date)

Améliorations apportées au MCD (à reprendre dans la documentation) :

- Prix en `DECIMAL(10,2)` au lieu de `DOUBLE` (pas d'erreurs d'arrondi)
- Mot de passe en `VARCHAR(255)`, haché avec `password_hash()` (bcrypt + sel)
- Colonne `actif` sur `utilisateur` : désactiver un employé sans le supprimer
- Infos personnelles facultatives en base (un employé est créé avec seulement email + mot de passe ; contrôle des champs clients en PHP)
- Suppression de la redondance du MCD : `menu` avait une colonne texte `regime` ET une relation vers `regime` → seule la relation est gardée
- Colonne `conditions` sur `menu` (exigée par l'énoncé)
- Colonne `type_plat` (entrée / plat / dessert)
- Photos stockées en chemin (`VARCHAR`) au lieu de `BLOB`
- `commande` : adresse et ville de livraison, prix total, mode de contact et motif d'annulation ; heure en `TIME` ; prix figés au moment de la commande
- `avis` : lié à la commande (un avis par commande), statut à 3 valeurs, `CHECK (note BETWEEN 1 AND 5)`
- `horaires` : créneaux matin et soir au lieu d'une seule ouverture/fermeture
- Tables supprimées par rapport aux premières versions : `ligne_commande` (une commande = un menu dans le sujet) et `equipe` (« Équipe » = comptes employés dans `utilisateur`)

## 6. Identifiants de test (pour le manuel d'utilisation)

- Administrateur : `jose@viteetgourmand.fr` / `Jose@Admin2026`
- Employée : `julie@viteetgourmand.fr` / `Julie@Employe2026`
- Clients (même mot de passe `Client@Test2026`) : `marie.dupont@mail.fr`, `thomas.martin@mail.fr`, `sophie.laurent@mail.fr`, `daniele.durand@mail.fr`, `christine.thomas@mail.fr`, `marc.dubois@mail.fr`
- **Changer le mot de passe de José avant la mise en ligne.**

## 7. Ce qui fonctionne

- Header / footer en `includes`, pages en `.php`
- Footer : horaires lus en base
- Admin (SPA en JavaScript) :
  - navigation par rubriques ; l'ancre de l'URL (`admin.php#horaires`) affiche la bonne rubrique au chargement et survit à F5 (`history.replaceState`)
  - **Horaires** : affichage depuis la base + modification complète
    - `modifier_horaire.php?id=X` : formulaire pré-rempli (requête préparée, `TIME_FORMAT`), cases « Fermé le matin / le soir / toute la journée »
    - `traitement_horaire.php` : POST uniquement, champs vides → NULL, vérification de cohérence côté serveur, UPDATE préparé, redirection Post/Redirect/Get vers `admin.php#horaires`, message d'erreur `role="alert"`
  - **Avis** : affichage avec jointure sur `utilisateur` (prénom + initiale du nom), date au format français, badge de statut coloré + texte
    - boutons Valider / Refuser en **fetch** (`js/admin.js`) → `traitement_avis.php` (POST, liste blanche des statuts, UPDATE préparé, réponse JSON avec codes HTTP)
  - `htmlspecialchars()` sur les données saisies par les utilisateurs (protection XSS)
- Page Commande : calcul JavaScript (réduction 10 % si ≥ minimum + 5 personnes, livraison 5 € + 0,59 €/km hors Bordeaux) – **pas encore relié à la base**
- Page Menus : encore en HTML statique

## 8. Points en attente / à ne pas oublier

- **Sécurité** : `admin.php`, `modifier_horaire.php`, `traitement_horaire.php` et `traitement_avis.php` ne sont pas encore protégés → à faire avec la connexion (vérification de session et de rôle en haut de chaque fichier).
- Supprimer `info.php` avant la mise en ligne (il affiche la configuration du serveur).
- `db.php` contient le mot de passe en clair : prévoir une configuration propre pour le déploiement.
- Les prix sont en dur dans `js/commandes.js` (et différents de `menus.php`) → à remplacer par les données de la base.
- MongoDB obligatoire pour les statistiques du graphique admin (« base de données non relationnelle », énoncé page 9).
- Ligne `version:` obsolète à retirer de `docker-compose.yml` (simple avertissement).
- Améliorations CSS proposées par Claude à la fin du projet (demande de Brice).

## 9. Planning

- **Jeudi 12** : relier le dossier à GitHub, premier commit sur `dev`, branche `feature/connexion` → connexion (email + mot de passe), déconnexion, inscription, protection de l'admin et des fichiers de traitement par rôle
- **Vendredi 13** : MongoDB dans Docker, statistiques réelles dans Chart.js via fetch, chiffre d'affaires par menu avec filtres
- **Samedi 14** : premier déploiement en ligne + page d'accueil `index.php` (présentation, avis validés)
- **Dimanche 15** : page Menus depuis la base + filtres en fetch, page détail d'un menu, commande enregistrée en base
- **Lundi 16** : espace client (ses commandes), gestion des commandes dans l'admin, contact, mentions légales, CGV
- **Mardi 17** : documentation (README, manuel PDF, charte graphique PDF, doc technique, gestion de projet) + copie à rendre
- **Mercredi 18** : fusion `dev` → `main`, déploiement final, tests complets, **dépôt sur Studi**
- **Jeudi 19** : journée de sécurité

Simplifications possibles si retard : espace employé fusionné avec l'admin, mails simulés, pages secondaires minimales.

## 10. Arguments clés pour l'oral (résumé)

- Requêtes préparées PDO partout où une donnée vient de l'utilisateur → protection contre l'injection SQL
- `htmlspecialchars()` à l'affichage → protection contre le XSS
- Mots de passe hachés (`password_hash` / `password_verify`), jamais en clair, même dans les données de test
- Vérifications côté serveur, car le navigateur peut toujours être contourné ; liste blanche pour les valeurs attendues
- Séparation affichage / traitement, POST pour modifier, GET pour lire, Post/Redirect/Get
- Base de données = source unique de vérité (les prix ne doivent être écrits qu'à un seul endroit)
- Dates stockées au format MySQL, formatées seulement à l'affichage
- Accessibilité RGAA : `fieldset`/`legend`, `role="alert"`, statut affiché en texte et pas seulement en couleur
- Mobile first, testé sur un vrai iPhone
