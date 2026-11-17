<?php
require_once 'includes/session.php';
require_once 'includes/verif_equipe.php';
require_once 'includes/db.php';
// L'administrateur voit tout ; l'employé ne voit pas le tableau de bord ni l'équipe
$est_admin = ($_SESSION['role'] == 'administrateur');
// Rubrique Commandes : filtres lus dans l'adresse (formulaire GET), puis la liste
require_once 'includes/Commandes.php';
$filtre_statut = '';
if (isset($_GET['statut']) && in_array($_GET['statut'], Commandes::STATUTS)) {
    $filtre_statut = $_GET['statut'];
}
$filtre_client = '';
if (isset($_GET['client']) && is_string($_GET['client'])) {
    $filtre_client = trim($_GET['client']);
}
$modele_commandes = new Commandes($pdo);
$commandes = $modele_commandes->trouver_toutes($filtre_statut, $filtre_client);
?>
<?php
$sql = "SELECT avis.*, utilisateur.prenom, utilisateur.nom
        FROM avis
        JOIN utilisateur ON avis.utilisateur_id = utilisateur.id
        ORDER BY avis.date_publication DESC";
$requete = $pdo->query($sql);
$avis = $requete->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
$sql = "SELECT * FROM horaires";
$requete = $pdo->query($sql);
$horaires = $requete->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
// Liste des menus pour les filtres du tableau de bord (administrateur uniquement)
if ($est_admin) {
    $sql = "SELECT id, titre FROM menu ORDER BY titre";
    $requete = $pdo->query($sql);
    $menus_filtre = $requete->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMINISTRATEUR</title>
     <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    <main class="page-admin">
    
    <!-- Barre de navigation latérale (Sidebar) -->
    <aside class="menu-lateral">
        <h2><?php if ($est_admin) { echo 'ADMINISTRATEUR'; } else { echo 'ESPACE EMPLOYÉ'; } ?></h2>
        <nav>
            <ul>
                <?php if ($est_admin) { ?>
    <li><a href="#tableau-bord">Tableau de bord</a></li>
<?php } ?>
<li><a href="#commandes">Commandes</a></li>
<?php if ($est_admin) { ?>
    <li><a href="#equipe">Équipe</a></li>
<?php } ?>
<li><a href="#menus">Menus</a></li>
<li><a href="#horaires">Horaires</a></li>
<li><a href="#avis">Avis</a></li>
<li><a href="deconnexion.php" class="btn-deconnexion">Déconnexion</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Zone de contenu principale -->
    <section class="contenu-admin">
        <?php if ($est_admin) { ?>
        <!-- Bloc 1 : Tableau de bord (Graphiques et Statistiques) -->
        <article id="tableau-bord" class="section-admin">
            <h2>Tableau de bord</h2>
            
            <!-- Zone des filtres du graphique -->
            <div class="filtres-dashboard">
                <fieldset class="groupe-filtre">
    <legend>Menus à afficher (plusieurs choix) :</legend>
    <div class="liste-checkbox-scroll">
        <label><input type="checkbox" name="filtre-menu" value="tous" checked> Tous les menus</label>
        <?php foreach ($menus_filtre as $un_menu) { ?>
            <label><input type="checkbox" name="filtre-menu" value="<?php echo $un_menu['id']; ?>" checked> <?php echo htmlspecialchars($un_menu['titre']); ?></label>
        <?php } ?>
    </div>
</fieldset>
               

                <div class="groupe-filtre">
                    <label for="date-debut">Date de début :</label>
                    <input type="date" id="date-debut" name="date-debut">
                </div>

                <div class="groupe-filtre">
                    <label for="date-fin">Date de fin :</label>
                    <input type="date" id="date-fin" name="date-fin">
                </div>

            </div>
               

           <!-- Graphique : nombre de commandes par menu -->
            <h3>Nombre de commandes par menu</h3>
            <div class="graphique-conteneur">
                <canvas id="graphique-ca" role="img" aria-label="Graphique en barres : nombre de commandes par menu. Le détail chiffré est disponible dans la liste qui suit."></canvas>
            </div>

            <!-- Chiffre d'affaires par menu (rempli par JavaScript) -->
            <h3>Chiffre d'affaires par menu</h3>
            <p id="message-statistiques" role="status"></p>
            <ul id="liste-chiffre-affaires" class="liste-chiffre-affaires">
            </ul>
            <p class="total-chiffre-affaires">Total : <strong id="total-chiffre-affaires">—</strong></p>
        </article>
        <?php } ?>
        <!-- Bloc 2 : Gestion des Commandes -->
        <article id="commandes" class="section-admin">
            <h2>Commandes</h2>
    
                    <!-- Filtres (énoncé : par statut et par client) -->
            <form method="GET" action="admin.php#commandes" class="filtres-recherche">
                <div class="champ-saisie">
                    <label for="filtre-statut">Statut :</label>
                    <select id="filtre-statut" name="statut">
                        <option value="">Tous les statuts</option>
                        <?php foreach (Commandes::STATUTS as $un_statut) { ?>
                            <option value="<?php echo $un_statut; ?>" <?php if ($un_statut == $filtre_statut) { echo 'selected'; } ?>><?php echo $un_statut; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="champ-saisie">
                    <label for="filtre-client">Client (nom, prénom ou email) :</label>
                    <input type="text" id="filtre-client" name="client" value="<?php echo htmlspecialchars($filtre_client); ?>">
                </div>
                <button type="submit" class="btn-valider">Filtrer</button>
                <a href="admin.php#commandes">Effacer les filtres</a>
            </form>

            <p role="status"><?php echo count($commandes); ?> commande(s)</p>

            <div class="grille-commandes">
                <?php foreach ($commandes as $une_commande) { ?>
                    <?php $plats_choisis = $modele_commandes->trouver_plats_choisis($une_commande['id']); ?>
                    <div class="carte-commande">
                        <div class="infos-principales">
                            <p class="nom-client"><strong><?php echo htmlspecialchars($une_commande['prenom'] . ' ' . $une_commande['nom']); ?></strong></p>
                            <p><?php echo htmlspecialchars($une_commande['email']); ?> - <?php echo htmlspecialchars($une_commande['telephone']); ?></p>
                            <p><?php echo htmlspecialchars($une_commande['numero_commande']); ?> - <?php echo htmlspecialchars($une_commande['menu_titre']); ?>, <?php echo $une_commande['nombre_personne']; ?> personnes, <?php echo number_format($une_commande['prix_total'], 2, ',', ' '); ?> €</p>
                            <p class="date-prestation">Prestation : <?php echo date('d/m/Y', strtotime($une_commande['date_prestation'])); ?> à <?php echo substr($une_commande['heure_livraison'], 0, 5); ?>, <?php echo htmlspecialchars($une_commande['adresse_livraison'] . ' ' . $une_commande['ville_livraison']); ?></p>
                            <p>Plats : <?php echo htmlspecialchars(implode(', ', array_column($plats_choisis, 'titre_plat'))); ?></p>
                            <span class="badge-statut statut-commande"><?php echo htmlspecialchars($une_commande['statut']); ?></span>
                        </div>
                    </div>
                <?php } ?>
            </div>        
        </article>
            <!-- Bloc 3 : Gestion de l'équipe -->
             <?php if ($est_admin) { ?>
            <article id="equipe" class="section-admin">
                <div class="en-tete-section">
                    <h2>Équipe</h2>
                    <button class="btn-ajouter" id="btn-ajouter-employe">+ Ajouter un employé</button>
                </div>

            <!-- Liste des employés -->
            <div class="grille-equipe">
        
                <!-- Carte Employé 1 (Administrateur principal) -->
                <div class="carte-employe">
                    <div class="infos-employe">
                        <p><strong>Nom :</strong> José</p>
                        <p><strong>Email :</strong> jose@vite-gourmand.fr</p>
                        <p><strong>Statut :</strong> <span class="badge-statut statut-actif">Actif</span></p>
                    </div>
                    <div class="actions-employe">
                        <button class="btn-modifier" disabled>Inactif</button>
                        <button class="btn-supprimer" disabled>Supprimer</button>
                    </div>
                </div>

                <!-- Carte Employé 2 (Employé classique) -->
                <div class="carte-employe">
                    <div class="infos-employe">
                        <p><strong>Nom :</strong> Julie</p>
                        <p><strong>Email :</strong> julie@vite-gourmand.fr</p>
                        <p><strong>Statut :</strong> <span class="badge-statut statut-actif">Actif</span></p>
                    </div>
                    <div class="actions-employe">
                        <button class="btn-modifier">Inactif</button>
                        <button class="btn-supprimer">Supprimer</button>
                    </div>
                </div>
            </div>
        </article>
        <?php } ?>
            <!-- Bloc 4 : Gestion des Menus et Plats -->
            <article id="menus" class="section-admin">
                <div class="en-tete-section">
                    <h2>Menus</h2>
                    <div class="actions-globales">
                        <button class="btn-ajouter" id="btn-ajouter-menu">+ Ajouter un menu</button>
                        <button class="btn-ajouter" id="btn-ajouter-plat">+ Ajouter un plat</button>
                    </div>
                </div>

            <!-- Affichage des menus sous forme de cartes -->
            <div class="grille-menus-admin">
        
                <!-- Carte Menu 1 -->
                <div class="carte-menu-admin">
                    <div class="infos-menu">
                        <p><strong>Nom :</strong> Burger Artisan</p>
                        <p><strong>Prix :</strong> 15.00 €</p>
                        <p><strong>Thème :</strong> Classique</p>
                    </div>
                    <div class="actions-menu">
                        <button class="btn-modifier">Modifier</button>
                        <button class="btn-supprimer">Supprimer</button>
                    </div>
                </div>

                <!-- Carte Menu 2 -->
                <div class="carte-menu-admin">
                    <div class="infos-menu">
                        <p><strong>Nom :</strong> Brunch Complet</p>
                        <p><strong>Prix :</strong> 22.00 €</p>
                        <p><strong>Thème :</strong> Évènement</p>
                    </div>
                    <div class="actions-menu">
                        <button class="btn-modifier">Modifier</button>
                        <button class="btn-supprimer">Supprimer</button>
                    </div>
                </div>
            </div>
        </article>
        <!-- Bloc 5 : Gestion des Horaires -->
            <article id="horaires" class="section-admin">
                <h2>Horaires</h2>
                <div class="grille-horaires">
    
                <div class="liste-horaires">
    <?php foreach ($horaires as $un_horaire) { ?>
        <div class="ligne-horaire">
            <p class="jour-horaire"><?php echo $un_horaire['jour']; ?></p>
            
            <div class="heures-horaire">
    <?php if (empty($un_horaire['ouverture_matin']) && empty($un_horaire['ouverture_soir'])) { ?>
        <p>Fermé</p>
    <?php } else { ?>
        
        <?php if (!empty($un_horaire['ouverture_matin'])) { ?>
            <p>Matin : de <?php echo substr($un_horaire['ouverture_matin'], 0, 5); ?> à <?php echo substr($un_horaire['fermeture_matin'], 0, 5); ?></p>
        <?php } ?>
        
        <?php if (!empty($un_horaire['ouverture_soir'])) { ?>
            <p>Soir : de <?php echo substr($un_horaire['ouverture_soir'], 0, 5); ?> à <?php echo substr($un_horaire['fermeture_soir'], 0, 5); ?></p>
        <?php } ?>
        
    <?php } ?>
</div>
            
            <a href="modifier_horaire.php?id=<?php echo $un_horaire['id']; ?>" class="bouton-modifier">MODIFIER</a>
        </div>
    <?php } ?>
</div>
        </article>

            <!-- Bloc 6 : Modération des Avis -->
            <article id="avis" class="section-admin">
                <h2>Avis</h2>
                <div class="grille-avis">

   <?php foreach ($avis as $un_avis) { ?>
    <?php
    // On choisit la classe CSS du badge selon le statut de l'avis
    if ($un_avis['statut'] == 'validé') {
        $classe_statut = 'statut-valide';
    } elseif ($un_avis['statut'] == 'refusé') {
        $classe_statut = 'statut-refuse';
    } else {
        $classe_statut = 'statut-attente';
    }
    ?>
    <div class="carte-avis" id="avis-<?php echo $un_avis['id']; ?>">
        <div class="en-tete-avis">
            <p><strong><?php echo htmlspecialchars($un_avis['prenom'] . ' ' . substr($un_avis['nom'], 0, 1) . '.'); ?></strong> - <?php echo date('d/m/Y à H\hi', strtotime($un_avis['date_publication'])); ?></p>
            <p class="note-avis">Note : <?php echo $un_avis['note']; ?>/5</p>
            <span class="badge-statut <?php echo $classe_statut; ?>"><?php echo $un_avis['statut']; ?></span>
        </div>
        <p class="texte-avis"><?php echo htmlspecialchars($un_avis['commentaire']); ?></p>
        <div class="actions-avis">
            <button class="btn-valider" data-id="<?php echo $un_avis['id']; ?>" data-statut="validé">Valider</button>
            <button class="btn-refuser" data-id="<?php echo $un_avis['id']; ?>" data-statut="refusé">Refuser</button>
        </div>
    </div>
<?php } ?>
</div>
            </article>
        <!-- Les sections Commandes, Equipe, Menus... viendront ici -->
    </section>

</main>
  <?php include_once 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/admin.js"></script>
</body>
</html>