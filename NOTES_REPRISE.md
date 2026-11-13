# Notes de reprise – ECF Vite & Gourmand (DWWM Studi)

Fichier de suivi du projet. À lire en début de séance (ou à donner à Claude dans une nouvelle conversation, avec l'accès au dossier `v_et_g`).

Dernière mise à jour : vendredi 13 novembre 2026, 14h.

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
- **Un commit par bloc qui fonctionne**, avec un message qui décrit ce qui a été fait.

## 3. Environnement de travail

- Dossier local : `C:\projets web et mobile\v_et_g` (PC) et `C:\dev\v_et_g` (laptop)
- Deux machines : toujours `git pull` en début de séance et `git push` en fin de séance
- Docker (`docker-compose.yml`) :
  - service `web` : PHP 8.2 + Apache (Dockerfile avec `pdo_mysql`), port **8000**
  - service `db` : MySQL 8.0, utf8mb4, port **3307** côté PC, base `v_et_g`, utilisateur `root` / mot de passe `root`
  - `database/init.sql` est monté dans `/docker-entrypoint-initdb.d/` : il s'exécute **uniquement quand le volume est vide**
  - service `mongo` : MongoDB 7.0, port **27018** côté PC (27017 déjà pris par un autre projet), utilisateur `root` / `root`, base `v_et_g`
  - `database/init_mongo.js` est monté dans `/docker-entrypoint-initdb.d/` : il s'exécute **uniquement quand le volume est vide**
  - Si `mongo` s'arrête juste après le premier démarrage (logs : « Address already in use » juste après « init process complete »), relancer `docker compose up -d` : l'initialisation est faite, le second démarrage fonctionne.
- Recréer la base après une modification de `init.sql` : `docker compose down -v` puis `docker compose up -d` (efface toutes les données saisies depuis le site, y compris les comptes créés par l'inscription)
- Recréer **uniquement** MongoDB (sans toucher à MySQL) : `docker compose rm -sf mongo`, puis `docker volume rm v_et_g_mongo_data`, puis `docker compose up -d`
- Après une modification du `Dockerfile` : `docker compose up -d --build`
- Voir les erreurs MySQL : `docker compose logs db` (MongoDB : `docker compose logs mongo`)
- Exécuter une requête SQL depuis le terminal :
  `docker compose exec db mysql -uroot -proot v_et_g -e "SELECT ... ;"`
  (ou via l'extension base de données de VS Code : cylindre > v_et_g > Query > Run)
- Terminal MongoDB : `docker compose exec mongo mongosh -u root -p root --authenticationDatabase admin v_et_g` (invite `v_et_g>`, sortie avec `exit`, Ctrl + C si l'invite affiche `|`)
- Sur PC : `http://localhost:8000/`
- Sur iPhone (même Wi-Fi) : `http://192.168.1.60:8000/` (vérifier l'IP avec `ipconfig` si besoin)
- Recharger sans cache dans Chrome : **Ctrl + Fn + F5**
- Tester « déconnecté » : fenêtre de navigation privée (**Ctrl + Maj + N**)
- Live Server (port 5500) ne sert plus : il ne sait pas exécuter le PHP.
- VS Code : barre de menus masquée → Ctrl + Maj + P > « View: Toggle Menu Bar »
- VS Code (Intelephense) souligne les classes `MongoDB\Driver\...` : fausse alerte, l'extension `mongodb` est installée dans Docker, pas sous Windows (choix : on laisse tel quel)
- Autre projet Docker sur le PC (`vg_php`, `vg_pma`, `vg_mailpit`, `vg_mongo`) : sans rapport avec `v_et_g`, à arrêter avec `docker stop` s'il ne sert plus

## 4. Git, GitHub et gestion de projet

- Dépôt : https://github.com/brice-glandy/Vite-et-Gourmand (public)
- Le dossier local est relié au dépôt (`origin`).
- Branches :
  - `main` : version stable, reçoit `dev` uniquement au moment des livraisons / du déploiement (pas encore fusionnée)
  - `dev` : branche de développement
  - `feature/connexion` : **terminée et fusionnée dans `dev` par pull request** (branche conservée volontairement sur GitHub pour montrer le workflow au jury)
  - `feature/statistiques` : **terminée et fusionnée dans `dev` par pull request** (branche conservée)
- Historique : premier commit sur `dev` (« Intégration front-end, base de données MySQL alignée sur le MCD, gestion des horaires et modération des avis »), puis un commit par bloc de la connexion, puis un commit par bloc des statistiques.

Cycle pour chaque fonctionnalité :

1. `git switch dev` puis `git pull` (partir d'une `dev` à jour)
2. `git switch -c feature/nom-de-la-fonctionnalite` puis `git push -u origin feature/nom-de-la-fonctionnalite`
3. À chaque bloc qui marche : `git add .` → `git commit -m "..."` → `git push`
4. Fonctionnalité terminée et testée : sur GitHub, **Pull requests > New pull request**, **base : `dev`** (attention, GitHub propose `main` par défaut), **compare : `feature/...`** → Create pull request → Merge pull request → Confirm merge (ne pas supprimer la branche)
5. En local : `git switch dev` puis `git pull`

Repères VS Code : **U** = nouveau fichier non suivi, **M** = modifié depuis le dernier commit, point sur un dossier = il contient un changement, `*` après le nom de branche = changements non commités. Toujours vérifier avec `git status` avant `git add .`.

- `.gitignore` : fichiers système (Thumbs.db, desktop.ini, .DS_Store) et `.vscode/`
- `info.php` supprimé avant le premier commit (il affichait la configuration du serveur).
- Git 2.55 installé, nom et email configurés.
- Trello « ECF - Vite & Gourmand » : 19 cartes. **À mettre à jour** (cartes 1 à 8, 10 et une partie de 11 et 14 terminées).
- Figma : wireframes et mockups desktop + mobile des 3 pages principales (vue globale des menus, commande, tableau de bord admin). À exporter en PDF pour la charte graphique.

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
- Compte « Etienne Durand » créé par l'inscription pour les tests : il disparaîtra au prochain `docker compose down -v` (il n'est pas dans `init.sql`).
- **Changer le mot de passe de José avant la mise en ligne.**
- Attention au **pavé numérique** : si Verr Num est désactivé, les chiffres ne sont pas tapés (cause du faux bug de connexion du 12/11).

## 7. Ce qui fonctionne

Structure générale :

- Header / footer en `includes`, pages en `.php`
- Footer : horaires lus en base
- Chaque page commence par `require_once 'includes/session.php';` puis `require_once 'includes/db.php';`

Connexion, inscription et sécurité (fonctionnalité `feature/connexion`) :

- `includes/session.php` : cookie de session `HttpOnly` + `SameSite=Lax`, puis `session_start()`
- `connexion.php` : formulaire (POST, `autocomplete`, champs en 16 px contre le zoom iPhone, contour `:focus` visible), message d'erreur unique « Email ou mot de passe incorrect », message « compte désactivé », message de succès après inscription
- `traitement_connexion.php` : recherche par email (requête préparée + jointure sur `role`), `password_verify`, refus des comptes inactifs, `session_regenerate_id(true)`, session = id + prénom + rôle uniquement, redirection selon le rôle (équipe → `admin.php`, client → `menus.php`)
- `js/mot_de_passe.js` : bouton œil afficher / masquer (type `button`, `aria-label`, `aria-pressed`), fonctionne pour plusieurs champs
- `inscription.php` + `traitement_inscription.php` : validation serveur complète (champs obligatoires, `filter_var` pour l'email, GSM `06`/`07` sur 10 chiffres, mot de passe : 10 caractères, majuscule, minuscule, chiffre, caractère spécial, confirmation identique, consentement RGPD, email déjà utilisé), toutes les erreurs affichées en une fois, saisie conservée via la session (jamais les mots de passe), rôle « utilisateur » par sous-requête, `password_hash`
- `includes/verif_equipe.php` : accès réservé à l'administrateur et aux employés, inclus dans `admin.php`, `modifier_horaire.php`, `traitement_horaire.php`
- `traitement_avis.php` : renvoie un **code 403 en JSON** si l'utilisateur n'est pas de l'équipe
- `deconnexion.php` : vide la session, supprime le cookie, détruit la session
- Header dynamique : CONNEXION / INSCRIPTION si déconnecté ; ADMINISTRATION (administrateur) ou ESPACE EMPLOYÉ (employé) + DÉCONNEXION si connecté ; rien de plus pour un client
- `admin.php` adapté au rôle (`$est_admin`) : l'employé ne reçoit ni le Tableau de bord ni l'Équipe (PHP ne les envoie pas), titre « ESPACE EMPLOYÉ » ; le JavaScript affiche la première rubrique disponible

Statistiques (fonctionnalité `feature/statistiques`) :

- MongoDB dans Docker (`mongo:7.0`, authentification activée), extension PHP `mongodb` installée par PECL dans le `Dockerfile` (sans Composer)
- `database/init_mongo.js` : collection `commandes` (copie dénormalisée des 8 commandes de `init.sql` : numéro, `menu_id`, `menu_titre`, date en `Date`, nombre de personnes, `prix_menu`, `prix_total`, statut), index unique sur `numero_commande`
- `includes/mongo.php` : connexion (équivalent de `db.php`)
- `statistiques.php` : API JSON réservée à l'administrateur (403 sinon), fonction `repondre_erreur`, filtres GET facultatifs `menus=1,3`, `debut=AAAA-MM-JJ`, `fin=AAAA-MM-JJ`, validés côté serveur (entiers, dates réelles, ordre début / fin → 400), agrégation MongoDB `$match` / `$group` / `$sort`, commandes annulées exclues, 500 si MongoDB est indisponible
- Chiffre d'affaires par menu calculé sur `prix_menu` (sans la livraison), période filtrée sur la date de commande
- `admin.php` : cases des menus générées depuis la table `menu` (MySQL, avec les identifiants), `fieldset` / `legend`, graphique avec `aria-label`, liste du chiffre d'affaires (alternative textuelle du graphique), total, zone `role="status"`
- `js/admin.js` : fetch vers `statistiques.php` avec `URLSearchParams`, mise à jour du graphique Chart.js, de la liste et du total sans rechargement, montants formatés avec `toLocaleString('fr-FR')`, insertion avec `textContent` (pas d'`innerHTML`), case « Tous les menus » synchronisée, aucun appel si aucun menu n'est coché, affichage vidé en cas d'erreur

Administration :

- Navigation par rubriques en JavaScript ; l'ancre de l'URL (`admin.php#horaires`) affiche la bonne rubrique et survit à F5 (`history.replaceState`)
- **Horaires** : affichage + modification complète (`modifier_horaire.php`, `traitement_horaire.php` : POST, champs vides → NULL, cases « Fermé le matin / le soir / toute la journée », vérification de cohérence côté serveur, Post/Redirect/Get)
- **Avis** : jointure sur `utilisateur` (prénom + initiale), date au format français, badge de statut ; Valider / Refuser en **fetch** → `traitement_avis.php` (liste blanche, JSON, codes HTTP)
- `htmlspecialchars()` sur toutes les données saisies par les utilisateurs (XSS)

Front encore statique :

- Page Commande : calcul JavaScript (réduction 10 %, livraison 5 € + 0,59 €/km hors Bordeaux), pas encore relié à la base
- Page Menus : HTML statique

## 8. Points en attente / à ne pas oublier

- **Actions réservées à l'administrateur** (créer / désactiver un employé, statistiques) : chaque fichier de traitement devra vérifier le rôle `administrateur` côté serveur (prévoir `includes/verif_admin.php`).
- Pages à créer : `contacts.php` (lien déjà dans le menu), `mentions_legales.php` (lien dans l'inscription), CGV, `index.php` (le logo pointe déjà vers `index.php`).
- Espace client : ajouter « MON ESPACE » dans le header pour le rôle `utilisateur`.
- « Mot de passe oublié ? » : lien présent mais vide (nécessite l'envoi de mails).
- Mails de l'énoncé (bienvenue, confirmation de commande, etc.) : décider envoi réel ou simulation.
- **Commandes → MongoDB** : quand `traitement_commande.php` enregistrera une commande dans MySQL, il devra aussi insérer sa fiche dans la collection `commandes` de MongoDB ; une commande annulée devra y passer au statut exact `annulée` (c'est la valeur exclue par `statistiques.php`).
- `includes/verif_admin.php` : à créer avec la gestion de l'équipe (créer / désactiver un employé).
- Mails : piste **Mailpit** (faux serveur mail qui affiche les mails dans une page web), déjà utilisé par l'autre projet Docker du PC.
- Déploiement MongoDB : base en ligne (MongoDB Atlas, offre gratuite) + mot de passe `root` à remplacer, comme pour MySQL.
- **Avant de rédiger la documentation et de préparer l'oral** : petit tour de F12 avec Claude (onglets **Réseau** et **Console**) pour savoir expliquer au jury comment je vérifie une requête fetch et je débogue le JavaScript.
- Les prix sont en dur dans `js/commandes.js` (et différents de `menus.php`) → à remplacer par les données de la base.
- Déploiement : `db.php` contient le mot de passe en clair → configuration propre ; activer l'option `secure` du cookie de session en HTTPS.
- Ligne `version:` obsolète à retirer de `docker-compose.yml` (simple avertissement).
- VS Code signale « Problems 1 » : à regarder.
- Améliorations CSS proposées par Claude à la fin du projet (demande de Brice).

## 9. Planning

- ~~**Jeudi 12** : Git + connexion, inscription, déconnexion, protection par rôle~~ → **fait** (en avance)
- ~~**Jeudi 12 après-midi / vendredi 13** : MongoDB dans Docker, statistiques réelles dans Chart.js via fetch, chiffre d'affaires par menu avec filtres~~ → **fait** (vendredi 13 à 14h)
- **Samedi 14** : premier déploiement en ligne + page d'accueil `index.php` (présentation, avis validés)
- **Dimanche 15** : page Menus depuis la base + filtres en fetch, page détail d'un menu, commande enregistrée en base
- **Lundi 16** : espace client (ses commandes), gestion des commandes dans l'admin, contact, mentions légales, CGV
- **Mardi 17** : documentation (README, manuel PDF, charte graphique PDF, doc technique, gestion de projet) + copie à rendre
- **Mercredi 18** : fusion `dev` → `main`, déploiement final, tests complets, **dépôt sur Studi**
- **Jeudi 19** : journée de sécurité

Simplifications possibles si retard : mails simulés, pages secondaires minimales, mot de passe oublié non réalisé.

## 10. Arguments clés pour l'oral (résumé)

Sécurité :

- Requêtes préparées PDO partout où une donnée vient de l'utilisateur → protection contre l'injection SQL
- `htmlspecialchars()` à l'affichage → protection contre le XSS
- Mots de passe hachés (`password_hash` / `password_verify`, bcrypt + sel), jamais en clair, même dans les données de test ou temporairement en session
- Message de connexion identique que l'email existe ou non (pas de découverte des comptes)
- Session : `session_regenerate_id` à la connexion (fixation de session), cookie `HttpOnly` (vol par script) et `SameSite=Lax` (CSRF), session limitée à l'id, au prénom et au rôle
- Contrôle d'accès côté serveur sur **chaque** point d'entrée (pages et scripts de traitement), centralisé dans un fichier inclus ; 403 en JSON pour l'API ; « cacher un bouton ne protège rien »
- Rubriques réservées à l'administrateur non envoyées par PHP (et non masquées en CSS)
- Vérifications côté serveur, car le navigateur peut toujours être contourné ; liste blanche pour les valeurs attendues ; `required` / `minlength` = confort seulement
- Comptes désactivables (`actif`) au lieu d'être supprimés : historique et clés étrangères préservés
- Fichier de diagnostic `phpinfo` supprimé avant le premier commit public

Architecture et bonnes pratiques :

- Séparation affichage / traitement, POST pour modifier, GET pour lire, Post/Redirect/Get
- La session comme « boîte aux lettres » pour renvoyer erreurs et saisie au formulaire (affichées une seule fois)
- Base de données = source unique de vérité (les prix ne doivent être écrits qu'à un seul endroit)
- Rôle récupéré par sous-requête plutôt qu'un identifiant écrit en dur
- Dates stockées au format MySQL, formatées seulement à l'affichage
- Workflow Git : `main` / `dev` / `feature/...`, pull request pour chaque fusion, un commit par étape
- Débogage méthodique : page de test isolant chaque maillon (base, formulaire, vérification)

Bases de données SQL et NoSQL :

- MySQL = source de vérité (commandes, contraintes, clés étrangères) ; MongoDB = copie dénormalisée pour les statistiques (titre du menu recopié dans chaque document, pas de jointure)
- Calcul fait par MongoDB lui-même (pipeline d'agrégation `$match` / `$group` / `$sort`), PHP ne fait que transmettre le JSON
- Filtres validés côté serveur avant d'entrer dans la requête (entiers, dates réelles) : l'équivalent NoSQL des requêtes préparées
- Séparation données / affichage : `statistiques.php` est une API JSON, réutilisable par un autre écran
- Liste des menus des filtres lue dans MySQL (source de vérité) : un menu jamais commandé apparaît quand même
- Données de départ MongoDB dans un fichier versionné, exécuté automatiquement comme `init.sql`
- Authentification MongoDB activée dès le développement ; ports décalés côté PC (3307, 27018), ports standards à l'intérieur de Docker
- Débogage par hypothèses : lire les logs, formuler deux causes possibles, faire un test qui tranche (raté de premier démarrage de MongoDB)
- Éditeur ≠ environnement d'exécution : un soulignement d'Intelephense n'est pas une erreur PHP

Accessibilité et ergonomie :

- RGAA : `fieldset`/`legend` (aussi pour les cases du tableau de bord), graphique décrit par `aria-label` + liste chiffrée en alternative textuelle, `role="alert"` / `role="status"`, statut affiché en texte et pas seulement en couleur, `aria-describedby` pour les règles du mot de passe, `aria-label` / `aria-pressed` sur le bouton œil, contour `:focus` visible
- Mobile first, testé sur un vrai iPhone ; champs à 16 px (pas de zoom automatique de Safari) ; `type="email"` / `type="tel"` / `autocomplete` pour les claviers et le remplissage automatique
- RGPD : consentement explicite à l'inscription, prénom + initiale du nom seulement sur les avis
