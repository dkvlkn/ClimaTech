<?php
require_once "include/functions.inc.php";
$pageTitle = 'Plan du site | Climatech';
include "include/header.inc.php";
?>

<main class="container plan-page">
    <section class="plan">
        <h2>📍 Plan du site</h2>
        <p>Voici la structure de Climatech :</p>
        <ul>
            <li><a href="index.php">Accueil</a> - Page d'entrée du site.</li>
            <li><a href="meteo.php">Prévisions</a> - Consultez la météo par région, département et ville.</li>
            <li><a href="tech.php">Tech</a> - Détails techniques sur le projet.</li>
            <li><a href="stats.php">Statistiques</a> - Statistiques de consultation.</li>
            <li><a href="contact.php">Contact</a> - Formulaire de contact.</li>
            <li><a href="plan.php">Plan du site</a> - Vous êtes ici !</li>
        </ul>

        <!-- Bannière de consentement aux cookies -->
        <div id="cookie-consent" class="cookie-consent<?php echo function_exists('shouldShowCookieConsent') && shouldShowCookieConsent() ? '' : ' hidden'; ?>" role="dialog" aria-label="Consentement aux cookies">
            <p>Nous utilisons des cookies pour améliorer votre expérience et analyser notre trafic. Acceptez-vous leur utilisation ?</p>
            <div class="cookie-buttons">
                <button id="accept-cookies">Accepter</button>
                <button id="decline-cookies">Refuser</button>
            </div>
        </div>
    </section>
</main>

<script src="js/cookie-consent.js"></script>
<?php
require_once "include/footer.inc.php";
?>


