# Vite & Gourmand

Application web d'un traiteur bordelais : présentation, menus, commandes,
espace employé et administrateur avec statistiques.
ECF du titre professionnel Développeur Web et Web Mobile (Studi).

- Application en ligne : https://studi-v-et-g.alwaysdata.net
- Technologies : HTML, CSS, JavaScript natif (fetch), PHP 8.2 avec PDO,
  MySQL (MariaDB en production), MongoDB pour les statistiques.

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
3. Lancer les conteneurs :
   ```
   docker compose up -d
   ```
   Au premier lancement, les bases sont créées et remplies automatiquement
   par `database/init.sql` (MySQL) et `database/init_mongo.js` (MongoDB).
   Si le conteneur `mongo` s'arrête juste après ce premier démarrage,
   relancer `docker compose up -d`.
4. Ouvrir http://localhost:8000

Les identifiants de test sont donnés dans le manuel d'utilisation.

Repartir de données neuves : `docker compose down -v` puis `docker compose up -d`
(efface toutes les données saisies depuis le site).

## Déploiement en production (résumé)

- PHP et MariaDB chez alwaysdata (offre gratuite), MongoDB chez MongoDB Atlas
  (cluster gratuit, région Paris).
- Le code est récupéré sur le serveur avec Git (`git clone`, puis `git pull`
  pour chaque mise à jour).
- Les identifiants de production sont dans `includes/config.php`, créé
  directement sur le serveur (droits 600) et jamais envoyé sur GitHub
  (voir `.gitignore`).
- L'extension PHP `mongodb` a été compilée sur le serveur (`ad_install_pecl mongodb`).
- La base MariaDB a été créée avec le même `database/init.sql` qu'en local.
- Atlas n'accepte que les adresses IP autorisées : celle du serveur HTTP
  d'alwaysdata (menu Avancé > Statut des serveurs). Si les statistiques
  ne s'affichent plus, vérifier d'abord cette adresse.
- Sécurité : HTTPS forcé, cookie de session `HttpOnly`, `SameSite=Lax` et `Secure`,
  fichier `.htaccess` qui masque les fichiers internes (`.git`, `database`,
  `includes`...).

Restaurer les données de démonstration en ligne : supprimer puis recréer la base
dans l'administration d'alwaysdata, relancer l'import de `init.sql`, et réimporter
`init_mongo.js` dans Atlas après avoir supprimé la collection `commandes`.

Le détail de chaque étape figure dans la documentation technique.
