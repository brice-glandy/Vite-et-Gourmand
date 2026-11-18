# Vite & Gourmand

Application web d'un traiteur bordelais : présentation, menus avec filtres,
commande en ligne, espace client, espace employé et administrateur avec statistiques.
ECF du titre professionnel Développeur Web et Web Mobile (Studi).

- Application en ligne : https://studi-v-et-g.alwaysdata.net
- Gestion de projet (Trello) : https://trello.com/b/1yia4zkW/ecf-vite-gourmand
- Maquettes Figma : [wireframes](https://www.figma.com/design/GtNn0K2srZyG1JrvAmVIk9/Vite_-_Gourmand?node-id=0-1&p=f) et [mockups](https://www.figma.com/design/GtNn0K2srZyG1JrvAmVIk9/Vite_-_Gourmand?node-id=71-619&t=Y32hoVmdcC1zUJVV-1)
- Documentation (manuel d'utilisation, charte graphique, documentation technique,
  gestion de projet) : dossier [`docs/`](docs/)

## Technologies

- Front : HTML, CSS (mobile first, sans framework), JavaScript natif (`fetch` vers une API JSON)
- Back : PHP 8.2 avec PDO (requêtes préparées), programmation orientée objet pour les modèles
- Bases de données : MySQL 8.0 en local (MariaDB 11.8 en production) pour les données de l'application,
  MongoDB (7.0 en local, Atlas en production) pour les statistiques
- Environnement : Docker (4 conteneurs), Git et GitHub

## Architecture du projet

```text
v_et_g/
├── *.php                  Pages affichées (menus.php, admin.php...) et traitements des formulaires
│                          (traitement_*.php : vérification, appel du modèle, redirection)
├── api_menus.php          API JSON des menus filtrés (appelée en fetch par js/filtres_menus.js)
├── includes/
│   ├── Menus.php          Modèles (classes) : les requêtes SQL de chaque sujet, réunies au même endroit
│   ├── Plats.php
│   ├── Commandes.php
│   ├── Employes.php
│   ├── Reinitialisations.php
│   ├── config.exemple.php Modèle de configuration (config.php, propre à chaque machine, n'est pas sur GitHub)
│   ├── db.php             Connexion PDO à MySQL / MariaDB
│   ├── mongo.php          Connexion à MongoDB
│   ├── mail.php           Envoi des mails (fonction envoyer_mail)
│   ├── session.php        Session sécurisée (cookie HttpOnly, SameSite, Secure en HTTPS)
│   ├── verif_equipe.php   Accès réservé aux employés et à l'administrateur
│   ├── verif_admin.php    Accès réservé à l'administrateur
│   └── header.php, footer.php
├── js/                    Un fichier par fonctionnalité (filtres des menus, administration, espace client...)
├── css/style.css          Feuille de style unique, organisée par page
├── images/                Photos des menus
├── database/
│   ├── init.sql           Création de la base MySQL et données de démonstration
│   ├── init_mongo.js      Collection MongoDB des statistiques et données de démonstration
│   └── ajout_*.sql        Mises à jour de la base en ligne, versionnées (une par évolution)
├── docs/                  Documentation en PDF
├── .htaccess              Renvoie une erreur 404 sur les dossiers internes (includes, database, .git)
├── Dockerfile             Image PHP 8.2 + Apache, extensions pdo_mysql et mongodb, msmtp
└── docker-compose.yml     Conteneurs web, db (MySQL), mongo, mailpit
```

## Installation en local

Prérequis : Git et Docker Desktop.

1. Cloner le dépôt :
   ```
   git clone https://github.com/brice-glandy/Vite-et-Gourmand.git v_et_g
   cd v_et_g
   ```
2. Créer le fichier de configuration à partir du modèle (les valeurs du modèle
   sont celles de Docker, rien à modifier) :
   ```
   copy includes\config.exemple.php includes\config.php
   ```
   (sous macOS ou Linux : `cp includes/config.exemple.php includes/config.php`)
3. Construire et lancer les conteneurs :
   ```
   docker compose up -d --build
   ```
   Au premier lancement, les bases sont créées et remplies automatiquement
   par `database/init.sql` (MySQL) et `database/init_mongo.js` (MongoDB).
   Si le conteneur `mongo` s'arrête juste après ce premier démarrage,
   relancer `docker compose up -d`.
4. Ouvrir le site : http://localhost:8000

Les mails envoyés par le site en local (confirmation de commande, bienvenue, contact,
mot de passe oublié...) n'arrivent pas dans de vraies boîtes : ils sont tous visibles
dans **Mailpit**, sur http://localhost:8026.

Ports utilisés : 8000 (site), 8026 (Mailpit), 3307 (MySQL), 27018 (MongoDB).

Les identifiants de test (administrateur, employée, clients) sont donnés dans le
manuel d'utilisation, dans le dossier `docs/`.

Repartir de données neuves : `docker compose down -v` puis `docker compose up -d`
(efface toutes les données saisies depuis le site).

## Base de données

- `database/init.sql` crée toutes les tables (clés étrangères, contraintes) et insère
  les données de démonstration : menus, plats et allergènes, horaires, avis, comptes,
  13 commandes avec leurs plats et leur historique de suivi.
- `database/init_mongo.js` crée la collection `commandes` de MongoDB (copie des commandes
  utilisée pour les statistiques du tableau de bord).
- Les fichiers `database/ajout_*.sql` ont servi à faire évoluer la base en ligne
  sans perdre ses données. Une nouvelle installation n'en a pas besoin : `init.sql`
  contient déjà toutes ces évolutions.

## Organisation Git

- `main` : branche principale (version validée).
- `dev` : branche de développement.
- `feature/...` : une branche par fonctionnalité, créée depuis `dev`, puis fusionnée
  dans `dev` par une pull request après les tests (19 pull requests). Les branches
  sont conservées pour garder l'historique.
- Quand `dev` est testée, elle est fusionnée dans `main`.

## Déploiement en production (résumé)

- PHP et MariaDB chez alwaysdata (offre gratuite), MongoDB chez MongoDB Atlas
  (cluster gratuit, région Paris).
- Le code est récupéré sur le serveur avec Git (`git clone`, puis `git pull`
  pour chaque mise à jour).
- Les identifiants de production sont dans `includes/config.php`, créé
  directement sur le serveur (droits 600) et jamais envoyé sur GitHub
  (voir `.gitignore`). En production : `$config_mail_mode = 'journal'`
  (les mails sont écrits dans le journal du serveur) et `$config_url_site`
  contient l'adresse du site.
- L'extension PHP `mongodb` a été compilée sur le serveur (`ad_install_pecl mongodb`).
- Une évolution de la base se déploie en deux temps : le script `database/ajout_....sql`
  est exécuté sur la base en ligne, puis le code est mis à jour avec `git pull`.
- Atlas n'accepte que les adresses IP autorisées : celle du serveur HTTP
  d'alwaysdata (menu Avancé > Statut des serveurs). Si les statistiques
  ne s'affichent plus, vérifier d'abord cette adresse.
- Sécurité : HTTPS forcé, cookie de session `HttpOnly`, `SameSite=Lax` et `Secure`,
  requêtes préparées, `htmlspecialchars` à l'affichage, mots de passe hachés
  (`password_hash`), fichier `.htaccess` qui masque les fichiers internes.

Restaurer les données de démonstration en ligne (par SSH) : sauvegarde avec `mysqldump`,
suppression des tables, import de `database/init.sql`, puis, dans Atlas, suppression de la
collection `commandes` et import de `database/init_mongo.js` avec `mongosh`.
Le détail de chaque étape figure dans la documentation technique.