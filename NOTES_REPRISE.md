# Notes de reprise – ECF Vite & Gourmand (DWWM Studi)

Fichier de suivi du projet. À lire en début de séance (ou à donner à Claude dans une nouvelle conversation, avec l'accès au dossier `v_et_g`).

Dernière mise à jour : dimanche 15 novembre 2026, 16h.

---

## 1. Échéance

- Date limite Studi : **jeudi 19 novembre 2026 à 23h59**.
- Objectif personnel (revu le 15/11) : **dépôt le jeudi 19 novembre**, le mercredi 18 sert à la documentation, à la fusion vers `main` et au déploiement final.

## 2. Méthode de travail (à respecter)

- Avancer par **petits blocs**, jamais une page entière d'un coup. Quand un bloc contient plusieurs nouveautés, le découper en sous-étapes testées une par une (4a, 4b, 4c...).
- **Squelette d'abord** : HTML, puis CSS fidèle à la maquette, puis JavaScript en dernier.
- Stack au niveau DWWM : HTML5 sémantique, CSS3 (Flexbox/Grid), JavaScript natif avec Fetch, PHP avec PDO, MySQL. Pas de raccourcis syntaxiques avancés.
- **Expliquer chaque script** étape par étape, concrètement, sans jargon. Tout terme nouveau est expliqué la première fois.
- Donner pour chaque choix technique **l'argument à présenter à l'oral**. Pour les choix importants, présenter le problème, les possibilités envisagées et la raison du choix (pour que Brice puisse le défendre comme le sien).
- **Droit de veto** : si un code paraît illogique ou trop abstrait, il est refusé et remplacé par une approche plus directe.
- Format des explications : texte simple, paragraphes ou listes à puces. **Pas de tableaux.**
- Claude lit les fichiers du projet, mais c'est Brice qui tape et applique les modifications (sauf demande explicite).
- **Un commit par bloc qui fonctionne**, avec un message qui décrit ce qui a été fait.
- Si Brice dit « je suis confus » ou « je tourne en rond » : une seule action à la fois, il répond « ok », puis la suivante.
- Après une pause : Brice écrit « je suis là », Claude donne une seule action de deux minutes pour relancer.

## 3. Environnement de travail

### Local (Docker)

- Dossier local : `C:\projets web et mobile\v_et_g` (PC, VS Code) et `C:\dev\v_et_g` (laptop, application Claude)
- Deux machines : toujours `git pull` en début de séance et `git push` en fin de séance
- **Laptop : après le prochain `git pull`, créer `includes/config.php`** en copiant `includes/config.exemple.php` (il ne voyage pas par GitHub). Sans lui, le site affiche une erreur.
- Docker (`docker-compose.yml`) :
  - service `web` : PHP 8.2 + Apache (Dockerfile avec `pdo_mysql` et `mongodb`), port **8000**
  - service `db` : MySQL 8.0, utf8mb4, port **3307** côté PC, base `v_et_g`, utilisateur `root` / mot de passe `root`
  - `database/init.sql` est monté dans `/docker-entrypoint-initdb.d/` : il s'exécute **uniquement quand le volume est vide**, dans la base `MYSQL_DATABASE` (le script ne contient plus de `USE`)
  - service `mongo` : MongoDB 7.0, port **27018** côté PC (27017 déjà pris par un autre projet), utilisateur `root` / `root`, base `v_et_g`
  - `database/init_mongo.js` est monté dans `/docker-entrypoint-initdb.d/` : il s'exécute **uniquement quand le volume est vide**
  - Si `mongo` s'arrête juste après le premier démarrage (logs : « Address already in use » juste après « init process complete »), relancer `docker compose up -d`.
- Recréer les bases après une modification de `init.sql` : `docker compose down -v` puis `docker compose up -d` (efface toutes les données saisies depuis le site ; fait le 15/11, le compte « Etienne Durand » a disparu)
- Recréer **uniquement** MongoDB : `docker compose rm -sf mongo`, puis `docker volume rm v_et_g_mongo_data`, puis `docker compose up -d`
- Après une modification du `Dockerfile` : `docker compose up -d --build`
- Voir les erreurs : `docker compose logs db`, `docker compose logs mongo`, et pour PHP / Apache `docker compose logs web --tail 5` (journal des visites avec les codes HTTP, et messages `error_log`)
- Requête SQL depuis le terminal : `docker compose exec db mysql -uroot -proot v_et_g -e "SELECT ... ;"`
- Terminal MongoDB : `docker compose exec mongo mongosh -u root -p root --authenticationDatabase admin v_et_g`
- Sur PC : `http://localhost:8000/` ; sur iPhone (même Wi-Fi) : `http://192.168.1.60:8000/`
- Recharger sans cache dans Chrome : **Ctrl + Fn + F5** ; tester « déconnecté » : **Ctrl + Maj + N**
- VS Code : terminal **Ctrl + ù** (affiche / masque) ; aperçu d'un Markdown **Ctrl + Maj + V**
- VS Code (Intelephense) souligne les classes `MongoDB\Driver\...` (et compte « Problems 1 » sur `mongo.php`) : fausse alerte, l'extension `mongodb` est dans Docker, pas sous Windows (choix : on laisse tel quel)
- Autre projet Docker sur le PC (`vg_php`, `vg_pma`, `vg_mailpit`, `vg_mongo`) : sans rapport avec `v_et_g`

### Configuration par machine

- `includes/config.php` : identifiants de la machine (MySQL, adresse MongoDB complète, `$config_cookie_secure`). **Ignoré par Git**, jamais sur GitHub.
- `includes/config.exemple.php` : modèle versionné, avec les valeurs de Docker. Chaque nouvelle machine le copie sous le nom `config.php`.
- `db.php`, `mongo.php` et `session.php` lisent `config.php` avec `require_once __DIR__ . '/config.php';`.
- Règle : quand un réglage est ajouté, l'ajouter dans `config.exemple.php`, dans le `config.php` du PC, **et dans celui du serveur avant le `git pull`**.

### Production (en ligne depuis le 15/11)

- Site : **https://studi-v-et-g.alwaysdata.net** (HTTPS forcé)
- **alwaysdata** (offre gratuite) : compte `studi-v-et-g`
  - PHP « 8.2 » (suit la dernière 8.2.x, 8.2.35 au 15/11), extension `mongodb` 2.5.7 compilée avec `ad_install_pecl mongodb`, déclarée dans Environnement > PHP : `extension=/home/studi-v-et-g/mongodb-8.2.so`
  - Code : dossier `~/v_et_g` (cloné depuis GitHub, branche `feature/deploiement` pour l'instant ; **passer sur `main` le 18/11**), répertoire racine du site `/v_et_g/`
  - `~/v_et_g/includes/config.php` créé à la main avec `nano`, droits `600`
  - MariaDB 11.8 : hôte `mysql-studi-v-et-g.alwaysdata.net`, base `studi-v-et-g_v_et_g`, utilisateur du site `studi-v-et-g_app` (tous les droits sur cette base seulement), utilisateur principal `studi-v-et-g` (passe-partout, mot de passe changé)
  - Serveur HTTP : `http20.paris1`, IP `185.31.40.30` (Avancé > Statut des serveurs) ; serveur SSH : `ssh2`
- **MongoDB Atlas** (cluster gratuit `Cluster0`, AWS Paris eu-west-3, MongoDB 8.0, replica set de 3 nœuds)
  - Adresse : `mongodb+srv://vg_app:<mot de passe>@cluster0.vjlk1hz.mongodb.net/`
  - Utilisateur `vg_app` : `readWrite@v_et_g` uniquement (l'administrateur créé automatiquement a été supprimé)
  - IP Access List : la box (import et tests) et `185.31.40.30/32` (serveur HTTP d'alwaysdata). **Si les statistiques ne marchent plus en ligne, vérifier d'abord cette adresse.**
  - Données d'exemple d'Atlas (`sample_mflix`) supprimées : seule la base `v_et_g` reste
- Mots de passe : uniquement dans **Bitwarden** (entrées « alwaysdata – SSH studi-v-et-g », « alwaysdata – MariaDB principal », « alwaysdata – MariaDB app », « Atlas – vg_app », « MongoDB Atlas »). Jamais dans ce fichier ni dans une conversation.

### Commandes utiles en production

- Connexion SSH (depuis le PC) : `ssh studi-v-et-g@ssh-studi-v-et-g.alwaysdata.net` (attendre `Password:` avant de coller)
- Repère : invite `PS C:\...>` = PC ; invite `studi-v-et-g@ssh2:~$` = serveur. Deux terminaux nommés `PC` et `SERVEUR` dans VS Code.
- **Mettre à jour le site** : `git push` sur le PC, puis sur le serveur `cd v_et_g` et `git pull`
- Base MariaDB en ligne : `mysql -h mysql-studi-v-et-g.alwaysdata.net -u studi-v-et-g_app -p studi-v-et-g_v_et_g` (le mot de passe est demandé)
- Import MongoDB vers Atlas (depuis le PC, Docker lancé) : `docker compose exec mongo mongosh "mongodb+srv://vg_app@cluster0.vjlk1hz.mongodb.net/v_et_g" --file /docker-entrypoint-initdb.d/init_mongo.js`
- Test de connexion MySQL sur le serveur : `php -r 'require "includes/db.php"; echo "MySQL OK\n";'`
- nano : flèches pour se déplacer, clic droit pour coller, **Ctrl + O** (relâcher) puis **Entrée** pour enregistrer (« Wrote... »), **Ctrl + X** pour quitter
- Vérifier un fichier sans afficher les mots de passe : `grep -c "texte" includes/config.php`
- Avant `git push`, faire `git status` : il doit afficher `nothing to commit, working tree clean`

## 4. Git, GitHub et gestion de projet

- Dépôt : https://github.com/brice-glandy/Vite-et-Gourmand (public)
- Branches :
  - `main` : version stable, reçoit `dev` au moment de la livraison (pas encore fusionnée)
  - `dev` : branche de développement
  - `feature/connexion`, `feature/statistiques`, `feature/accueil` : **terminées et fusionnées dans `dev` par pull request** (conservées pour le jury)
  - `feature/deploiement` : **terminée le 15/11** (commits ci-dessous), pull request vers `dev` à faire (bloc 8d)
- Commits de `feature/deploiement` : modèle `config.exemple.php` et `config.php` ignoré ; `db.php` lit la configuration (message neutre et code 503 en cas de panne) ; `mongo.php` lit l'adresse complète ; `init.sql` sans `USE` ; `.htaccess` ; cookie `secure` réglé dans la configuration ; README.

Cycle pour chaque fonctionnalité :

1. `git switch dev` puis `git pull` (partir d'une `dev` à jour)
2. `git switch -c feature/nom-de-la-fonctionnalite` puis `git push -u origin feature/nom-de-la-fonctionnalite`
3. À chaque bloc qui marche : `git add .` → `git commit -m "..."` → `git status` → `git push`
4. Fonctionnalité terminée et testée : sur GitHub, **Pull requests > New pull request**, **base : `dev`** (attention, GitHub propose `main` par défaut), **compare : `feature/...`** → Create pull request → Merge pull request → Confirm merge (ne pas supprimer la branche)
5. En local : `git switch dev` puis `git pull`

Repères VS Code : **U** = nouveau fichier non suivi, **M** = modifié, **A** = ajouté avec `git add` mais pas encore commité, `+` après le nom de branche = changements préparés non commités, `*` = changements non commités, fichier **gris** = ignoré par Git.

- `.gitignore` : fichiers système, `.vscode/`, `includes/config.php`
- Trello « ECF - Vite & Gourmand » : 19 cartes. **À mettre à jour** (cartes 1 à 8, 10, déploiement et une partie de 11 et 14 terminées).
- Figma : wireframes et mockups desktop + mobile des 3 pages principales. À exporter en PDF pour la charte graphique.

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

`init.sql` ne contient plus `USE v_et_g;` : la base est choisie par la commande qui l'exécute (Docker en local, commande `mysql` en ligne, où le nom est imposé avec le préfixe `studi-v-et-g_`). Le script passe tel quel sur MariaDB 11.8 (12 tables, accents corrects).

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
- **Décision du 15/11 :** ces identifiants restent valables **aussi en ligne** (données fictives, risque mesuré). Risque identifié : sabotage de la démonstration par quelqu'un qui lirait ces notes. Parade : procédure de restauration (partie 8). Changer les mots de passe de José et Julie en ligne reste possible en 5 minutes si le temps le permet (à la fin).
- Compte client créé en ligne pendant les tests : `test.jury@mail.fr`.
- Attention au **pavé numérique** : si Verr Num est désactivé, les chiffres ne sont pas tapés.

## 7. Ce qui fonctionne

Structure générale :

- Header / footer en `includes`, pages en `.php`
- Footer : horaires lus en base
- Chaque page commence par `require_once 'includes/session.php';` puis `require_once 'includes/db.php';`
- `js/menu.js` : menu burger commun à toutes les pages, chargé une seule fois depuis `includes/footer.php`, `aria-expanded` / `aria-controls` / `aria-label`

Déploiement (fonctionnalité `feature/deploiement`, 14 et 15/11) :

- Site en ligne et testé sur ordinateur et iPhone : accueil, menus, commande (statique), inscription, connexion / déconnexion, espace employé, administration (horaires, avis, statistiques avec filtres), redirection vers la connexion pour `admin.php`
- Configuration par machine (`config.php` ignoré, `config.exemple.php` versionné)
- `db.php` : en cas de panne, message neutre pour le visiteur, détail dans le journal (`error_log`), code HTTP **503**
- Une panne de MongoDB ne casse que les statistiques (testé en local)
- `.htaccess` : `RedirectMatch 404` sur `.git`, `database/`, `includes/`, `NOTES_REPRISE.md`, `README.md`, `docker-compose.yml`, `Dockerfile` (testé en local et en ligne)
- HTTPS forcé (option alwaysdata) ; cookie de session `HttpOnly`, `SameSite=Lax`, `Secure` en ligne (vérifié dans F12 > Application > Cookies)
- README : installation locale et résumé du déploiement

Page d'accueil (fonctionnalité `feature/accueil`) :

- `index.php` : présentation de l'entreprise, professionnalisme de l'équipe, avis clients validés ; un seul `h1`, sections reliées à leur titre par `aria-labelledby`, `meta description` ; pas de photo (à envisager éventuellement)
- Avis : uniquement le statut `validé`, du plus récent au plus ancien, 6 au maximum, `htmlspecialchars`, initiale du nom avec `mb_substr`, date au format français, message si aucun avis
- Test de démonstration pour le jury : valider un avis dans l'administration → il apparaît aussitôt sur l'accueil

Connexion, inscription et sécurité (fonctionnalité `feature/connexion`) :

- `includes/session.php` : cookie de session `HttpOnly` + `SameSite=Lax` + `Secure` (selon `config.php`), puis `session_start()`
- `connexion.php` / `traitement_connexion.php` : requête préparée, `password_verify`, message d'erreur unique, refus des comptes inactifs, `session_regenerate_id(true)`, redirection selon le rôle
- `js/mot_de_passe.js` : bouton œil afficher / masquer
- `inscription.php` + `traitement_inscription.php` : validation serveur complète, `password_hash`, rôle « utilisateur » par sous-requête
- `includes/verif_equipe.php` : accès réservé à l'administrateur et aux employés
- `traitement_avis.php` : **code 403 en JSON** si l'utilisateur n'est pas de l'équipe
- `deconnexion.php` : vide la session, supprime le cookie, détruit la session
- Header dynamique selon le rôle ; `admin.php` adapté au rôle (`$est_admin`)

Statistiques (fonctionnalité `feature/statistiques`) :

- MongoDB (Docker en local, Atlas en ligne), extension PHP `mongodb` (PECL dans le `Dockerfile`, `ad_install_pecl` chez alwaysdata)
- `database/init_mongo.js` : collection `commandes` (copie dénormalisée des 8 commandes), index unique sur `numero_commande`
- `statistiques.php` : API JSON réservée à l'administrateur, filtres validés côté serveur, agrégation `$match` / `$group` / `$sort`, commandes annulées exclues, 500 si MongoDB est indisponible
- `admin.php` + `js/admin.js` : graphique Chart.js, liste du chiffre d'affaires, total, filtres en fetch sans rechargement

Administration :

- Navigation par rubriques en JavaScript, ancre de l'URL conservée
- Horaires : affichage + modification complète (Post/Redirect/Get)
- Avis : Valider / Refuser en fetch (liste blanche, JSON, codes HTTP)
- `htmlspecialchars()` sur toutes les données saisies par les utilisateurs (XSS)

Front encore statique :

- Page Commande : calcul JavaScript (réduction 10 %, livraison 5 € + 0,59 €/km hors Bordeaux), pas encore relié à la base
- Page Menus : HTML statique

## 8. Points en attente / à ne pas oublier

- **Pull request `feature/deploiement` → `dev`** (bloc 8d), puis `git pull` sur le laptop **et création de son `config.php`**.
- **18/11 : déploiement final** : sur le serveur, `git switch main` puis `git pull` après la fusion `dev` → `main`. Penser à ajouter dans le `config.php` du serveur tout nouveau réglage **avant** le `git pull`.
- **Procédure de restauration des données en ligne** (à documenter dans la documentation technique) : supprimer et recréer la base `studi-v-et-g_v_et_g` dans alwaysdata, relancer l'import de `init.sql` ; supprimer la collection `commandes` dans Atlas et relancer l'import de `init_mongo.js`.
- Optionnel, à la fin : nouveaux mots de passe en ligne pour José et Julie (commande sur le serveur avec `db.php`, requête préparée et `password_hash`), donnés dans la copie à rendre.
- Différence de version MongoDB : 7.0 en local, 8.0 sur Atlas (sans effet sur l'agrégation utilisée). Possibilité de passer Docker en `mongo:8.0` pour aligner.
- **Actions réservées à l'administrateur** (créer / désactiver un employé, statistiques) : chaque fichier de traitement devra vérifier le rôle `administrateur` côté serveur (`includes/verif_admin.php` à créer avec la gestion de l'équipe).
- Pages à créer : `contacts.php`, `mentions_legales.php`, CGV (liens déjà présents).
- `admin.php` : remplacer `substr` par `mb_substr` pour l'initiale du nom dans la liste des avis.
- `index.php` : le commentaire de la section des avis dit encore « écrits en dur pour l'instant » → à corriger.
- Espace client : ajouter « MON ESPACE » dans le header pour le rôle `utilisateur`.
- « Mot de passe oublié ? » : lien présent mais vide (nécessite l'envoi de mails).
- Mails de l'énoncé : décider envoi réel ou simulation (piste **Mailpit** en local).
- **Commandes → MongoDB** : `traitement_commande.php` devra aussi insérer la fiche dans la collection `commandes` ; une commande annulée devra y passer au statut exact `annulée`.
- Les prix sont en dur dans `js/commandes.js` (et différents de `menus.php`) → à remplacer par les données de la base.
- Ligne `version:` obsolète à retirer de `docker-compose.yml` (simple avertissement).
- F12 : onglet **Application** (cookies) vu le 15/11 ; reste à voir **Réseau** et **Console** avant la documentation.
- Améliorations CSS proposées par Claude à la fin du projet (demande de Brice).

## 9. Planning (revu le 15/11)

- ~~**Jeudi 12** : Git + connexion, inscription, déconnexion, protection par rôle~~ → **fait**
- ~~**Vendredi 13** : MongoDB dans Docker, statistiques, page d'accueil, menu burger commun~~ → **fait**
- ~~**Samedi 14 et dimanche 15** : premier déploiement (alwaysdata + MongoDB Atlas)~~ → **fait** (dimanche 15 à 16h)
- **Lundi 16** : page Menus depuis la base + filtres en fetch, page détail d'un menu, commande enregistrée en base (MySQL et MongoDB)
- **Mardi 17** : espace client, gestion des commandes et des employés dans l'administration, contact, mentions légales et CGV en version simple
- **Mercredi 18** : documentation (README, manuel PDF, charte graphique PDF, doc technique, gestion de projet), copie à rendre, fusion `dev` → `main`, déploiement final, tests complets
- **Jeudi 19** : dépôt sur Studi (avant 23h59), journée de marge

Simplifications possibles si retard : mails simulés, pages secondaires minimales, mot de passe oublié non réalisé, modification et suppression des menus limitées.

## 10. Arguments clés pour l'oral (résumé)

Sécurité :

- Requêtes préparées PDO partout où une donnée vient de l'utilisateur → protection contre l'injection SQL
- `htmlspecialchars()` à l'affichage → protection contre le XSS
- Mots de passe hachés (`password_hash` / `password_verify`, bcrypt + sel), jamais en clair
- Message de connexion identique que l'email existe ou non (pas de découverte des comptes)
- Session : `session_regenerate_id` à la connexion, cookie `HttpOnly` (vol par script), `SameSite=Lax` (CSRF), `Secure` en production (HTTPS uniquement), vérifié dans F12 > Application > Cookies ; session limitée à l'id, au prénom et au rôle
- Contrôle d'accès côté serveur sur **chaque** point d'entrée ; 403 en JSON pour l'API ; « cacher un bouton ne protège rien »
- Vérifications côté serveur ; liste blanche pour les valeurs attendues
- Comptes désactivables (`actif`) au lieu d'être supprimés
- Fichier de diagnostic `phpinfo` supprimé avant le premier commit public ; en production, vérification de l'extension avec une page de test qui ne révèle qu'un oui / non, supprimée aussitôt

Configuration et secrets :

- Dépôt public → aucun mot de passe de production sur GitHub. Séparer ce qui est commun (connexion dans `db.php`, publié) de ce qui change selon la machine (identifiants dans `config.php`, ignoré par Git). Image : la recette (le code) est publique, la clé (le mot de passe) reste dans le tiroir de chaque maison (`config.php`).
- Possibilités écartées : mot de passe dans `db.php` (publié) ; modifier `db.php` à la main sur le serveur (deux versions du même fichier, écrasées à chaque mise à jour) ; un `if` selon la machine (proposé et analysé par Brice : publie quand même le mot de passe de production et casse dès qu'une machine n'est pas prévue, par exemple le test sur iPhone).
- `config.exemple.php` = modèle publié (même principe que `wp-config-sample.php` de WordPress) ; chaque machine le copie. Alternative connue : variables d'environnement (Heroku, Fly.io) ; fichier préféré car visible et identique dans Docker et chez alwaysdata.
- Les développeurs et le jury utilisent leurs propres bases Docker avec des données de test ; les données réelles restent en production (RGPD).
- Le réglage `secure` du cookie est aussi dans `config.php` (`false` en local en HTTP, `true` en ligne) : chez alwaysdata, le HTTPS est géré par le proxy, la détection automatique serait incertaine.
- Identifiants de production uniquement dans Bitwarden et dans le `config.php` du serveur (droits 600). Mots de passe saisis à la demande des outils (`-p`, mongosh), jamais écrits dans une commande.
- Mots de passe de test laissés en ligne : risque identifié et mesuré (données fictives), parade prévue (restauration), solution connue pour un vrai commerce (mots de passe uniques transmis par un canal privé).

Déploiement :

- Docker = environnement de développement identique pour tous ; production sur des services gérés (alwaysdata, Atlas). Même code partout, seule la configuration change.
- Choix d'hébergement vérifié sur les conditions actuelles : Fly.io sans offre gratuite durable, Aiven sans MySQL gratuit depuis novembre 2026 → alwaysdata + Atlas.
- Le plus risqué d'abord : extension MongoDB absente chez l'hébergeur → compilée sur le serveur avec `ad_install_pecl`, pour la version exacte de PHP (comme `pecl install` dans le `Dockerfile`), vérifiée avec le SSL avant de brancher Atlas.
- PHP « 8.2 » en production comme `php:8.2-apache` en local : même règle (dernière 8.2.x, correctifs de sécurité automatiques).
- Administration par SSH (accès chiffré), empreinte du serveur vérifiée à la première connexion (protection contre l'homme du milieu).
- Atlas à Paris (proximité avec alwaysdata, données dans l'UE), replica set de 3 nœuds. Adresse `mongodb+srv` : le pilote découvre les serveurs par le DNS, chiffrement par défaut.
- Moindre privilège : `vg_app` limité à `readWrite@v_et_g`, `studi-v-et-g_app` limité à sa base ; l'administrateur créé automatiquement par Atlas a été supprimé.
- Liste d'adresses IP chez Atlas : la box et le serveur HTTP d'alwaysdata (`/32`), relevé dans le statut des serveurs (les connexions sortantes passent par lui).
- Mêmes scripts de données en local et en production (`init.sql`, `init_mongo.js`) ; `init.sql` ne suppose pas le nom de la base.
- Mise en ligne préparée dans un dossier séparé, bascule du site seulement quand tout est prêt : le site public n'est jamais passé par un état cassé. Configuration préparée avant d'installer le code qui en a besoin.
- Panne de la base : message neutre + code 503 + détail dans le journal ; panne de MongoDB : seules les statistiques sont touchées.
- `.htaccess` : fichiers internes masqués avec une réponse 404 (et non 403, pour ne pas confirmer leur existence).
- HTTPS forcé par l'hébergeur ; adresse gratuite `studi-v-et-g.alwaysdata.net` (un vrai client achèterait son nom de domaine, sans changer le code).

Bases de données SQL et NoSQL :

- MySQL = source de vérité ; MongoDB = copie dénormalisée pour les statistiques
- Calcul fait par MongoDB (`$match` / `$group` / `$sort`), PHP ne fait que transmettre le JSON
- Filtres validés côté serveur avant d'entrer dans la requête
- `statistiques.php` est une API JSON réutilisable
- Authentification MongoDB activée dès le développement ; ports décalés côté PC (3307, 27018)
- Débogage par hypothèses : lire les logs, formuler des causes possibles, faire un test qui tranche (raté de premier démarrage de MongoDB, connexion SSH coupée avant le mot de passe, commit oublié repéré grâce au `A` et au `+` de VS Code)
- Éditeur ≠ environnement d'exécution : un soulignement d'Intelephense n'est pas une erreur PHP

Page d'accueil et code commun :

- Seuls les avis validés sont affichés, textes échappés, colonnes strictement nécessaires (RGPD)
- `mb_substr` au lieu de `substr` (lettres accentuées en UTF-8)
- `query()` acceptable sans donnée utilisateur ; requêtes préparées dès qu'une valeur vient d'un formulaire ou de l'adresse
- Menu burger centralisé dans un seul fichier chargé par le pied de page commun

Accessibilité et ergonomie :

- RGAA : `fieldset`/`legend`, un seul `h1` par page, sections nommées par `aria-labelledby`, menu burger avec `aria-expanded` / `aria-controls`, `:focus-visible`, lien pour changer de page et bouton pour une action, graphique décrit par `aria-label` + liste chiffrée, `role="alert"` / `role="status"`, statut en texte et pas seulement en couleur, `aria-describedby`, `aria-label` / `aria-pressed` sur le bouton œil
- Mobile first, testé sur un vrai iPhone (en local et en ligne) ; champs à 16 px ; `type="email"` / `type="tel"` / `autocomplete`
- RGPD : consentement explicite à l'inscription, prénom + initiale du nom seulement sur les avis
