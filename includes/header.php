<header>
        <div class="logo">
            <a href="index.php">Vite & Gourmand</a>
        </div>
        <button class="menu-burger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="navigation-principale">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <nav class="navigation-principale" id="navigation-principale">
            <ul>
                <li><a href="menus.php">NOS MENUS</a></li>
                <li><a href="contacts.php">CONTACTS</a></li>
                <?php if (isset($_SESSION['utilisateur_id'])) { ?>
    <?php if ($_SESSION['role'] == 'administrateur') { ?>
    <li><a href="admin.php">ADMINISTRATION</a></li>
<?php } elseif ($_SESSION['role'] == 'employe') { ?>
    <li><a href="admin.php">ESPACE EMPLOYÉ</a></li>
<?php } ?>
    <li><a href="deconnexion.php">DÉCONNEXION</a></li>
<?php } else { ?>
    <li><a href="connexion.php">CONNEXION</a></li>
    <li><a href="inscription.php">INSCRIPTION</a></li>
<?php } ?>
            </ul>
        </nav>
    </header>