<?php
// Sécurité : empêcher l'accès direct
if (!defined('PHP_EOL')) {
    die('Accès direct interdit');
}
?>
<footer>
    <a href="#top" class="back-to-top" title="Retour en haut">⬆</a>
    <p>
        © 2025 – <strong>Climatech</strong> | Par <strong>Yanis SAMAH</strong> &amp; <strong>Adel MAHI</strong><br />
        Tous droits réservés. | <a href="#" id="manage-cookies">Gérer les cookies</a><br />
        Contactez-nous via <a href="contact.php">Contact</a><br />
        <a href="tech.php" class="icon-tech">Page Tech</a> – <a href="plan.php" class="icon-plan">Plan du site</a> – <span>CY Cergy Université</span>
    </p>
    <p class="hits-counter">Hits : <?php echo htmlspecialchars($hits ?? 0, ENT_QUOTES, 'UTF-8'); ?></p>
</footer>