<footer>
    <a href="#top" class="back-to-top" title="Retour en haut">⬆</a>
    <p>
        © 2025 – <strong>Climatech</strong> | Par <strong>Yanis SAMAH</strong> & <strong>Adel MAHI</strong><br>
        Tous droits réservés. | <a href="#" id="manage-cookies">Gérer les cookies</a><br>
        Contactez-nous via <a href="contact.php">Contact</a><br>
        <a href="tech.php">📘 Page Tech</a> – <a href="plan.php">📍 Plan du site</a> – <span>CY Cergy Université</span>
    </p>
    <p class="hits-counter">Hits : <?= htmlspecialchars($hits); ?></p>
</footer>
<!-- Bannière de consentement aux cookies -->
<div id="cookie-consent" class="cookie-consent">
    <p>Nous utilisons des cookies pour améliorer votre expérience et analyser notre trafic. Acceptez-vous leur utilisation ?</p>
    <div class="cookie-buttons">
        <button id="accept-cookies">Accepter</button>
        <button id="decline-cookies">Refuser</button>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const cookieConsent = document.getElementById('cookie-consent');
    const acceptButton = document.getElementById('accept-cookies');
    const declineButton = document.getElementById('decline-cookies');
    const manageLink = document.getElementById('manage-cookies');

    // Fonction pour afficher la bannière
    function showCookieConsent() {
        cookieConsent.style.display = 'block';
    }

    // Vérifier si le consentement existe déjà
    if (!document.cookie.includes('cookie_consent')) {
        showCookieConsent();
    }

    // Accepter les cookies
    acceptButton?.addEventListener('click', function() {
        document.cookie = 'cookie_consent=accepted; path=/; max-age=' + (365 * 24 * 60 * 60);
        cookieConsent.style.display = 'none';
        // Appliquer les cookies non essentiels si besoin
        applyNonEssentialCookies();
    });

    // Refuser les cookies
    declineButton?.addEventListener('click', function() {
        document.cookie = 'cookie_consent=declined; path=/; max-age=' + (365 * 24 * 60 * 60);
        cookieConsent.style.display = 'none';
        // Supprimer les cookies non essentiels
        removeNonEssentialCookies();
    });

    // Gérer les cookies (réafficher la bannière)
    manageLink?.addEventListener('click', function(e) {
        e.preventDefault();
        showCookieConsent();
    });

    // Fonction pour appliquer les cookies non essentiels
    function applyNonEssentialCookies() {
        // Exemple : réactiver le cookie last_city si accepté
        <?php if (isset($_COOKIE['last_city'])): ?>
            document.cookie = 'last_city=<?= addslashes($_COOKIE['last_city']) ?>; path=/; max-age=' + (30 * 24 * 60 * 60);
        <?php endif; ?>
        // Ajoutez ici d'autres cookies non essentiels
    }

    // Fonction pour supprimer les cookies non essentiels
    function removeNonEssentialCookies() {
        document.cookie = 'last_city=; path=/; max-age=0';
        document.cookie = 'theme=; path=/; max-age=0';
        // Ajoutez ici d'autres cookies non essentiels à supprimer
    }
});
</script>
</body>
</html>