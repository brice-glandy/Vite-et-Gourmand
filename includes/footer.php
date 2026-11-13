<?php
$sql = "SELECT * FROM horaires";
$requete = $pdo->query($sql);
$horaires = $requete->fetchAll(PDO::FETCH_ASSOC);
?>
<footer class="pied-de-page">
    <div class="footer-horaires">
      <?php foreach ($horaires as $ligne) { 
    if ($ligne['ouverture_matin'] == null) {
        $matin = "Fermé";
    } else {
        $matin = substr($ligne['ouverture_matin'], 0, 5) . ' à ' . substr($ligne['fermeture_matin'], 0, 5);
    }

    if ($ligne['ouverture_soir'] == null) {
        $soir = "Fermé";
    } else {
        $soir = substr($ligne['ouverture_soir'], 0, 5) . ' à ' . substr($ligne['fermeture_soir'], 0, 5);
    }
?>
    <p>
        <?php echo $ligne['jour']; ?> : <?php echo $matin; ?> | <?php echo $soir; ?>
    </p>
<?php } ?>
    </div>
    <div class="footer-contact">
        <p class="footer-titre">VITE & GOURMAND</p>
        <p>18 Avenue Victor Hugo</p>
        <p>33000 Bordeaux</p>
        <p>0612345678</p>
    </div>
    <div class="footer-liens">
        <a href="#">Conditions Générales de vente</a>
        <a href="#">Mentions Légales</a>
    </div>
</footer>
<script src="js/menu.js"></script>