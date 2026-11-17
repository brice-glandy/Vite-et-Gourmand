<?php
// Modèle de configuration.
// Pour installer le projet : copier ce fichier sous le nom config.php
// (dans le même dossier), puis adapter les valeurs à la machine.
// Les valeurs ci-dessous sont celles de l'environnement Docker local.

// MySQL
$config_db_hote = 'db';
$config_db_nom = 'v_et_g';
$config_db_utilisateur = 'root';
$config_db_mot_de_passe = 'root';

// MongoDB
$config_mongo_adresse = 'mongodb://root:root@mongo:27017';
$config_mongo_base = 'v_et_g';


// Cookie de session : true seulement si le site est servi en HTTPS
$config_cookie_secure = false;

// Envoi des mails : 'mailpit' en local (boîte de réception sur http://localhost:8026),
// 'journal' en production (le mail est seulement écrit dans le journal du serveur)
$config_mail_mode = 'mailpit';