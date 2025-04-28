<?php
/**
 * Page Technique de démonstration des API utilisées dans Climatech.
 *
 * Cette page affiche :
 * - l'image ou la vidéo astronomique du jour via l'API de la NASA (APOD),
 * - la géolocalisation estimée via 3 services (GeoPlugin XML, ipinfo.io JSON, whatismyip.com XML),
 * - un nettoyage du cache des données locales obsolètes (>3 jours).
 *
 * PHP version 8+
 *
 * @category WebApp
 * @package  Climatech\TechPage
 * @author   VotreNom
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://climatech.example.com/tech.php
 */

// Inclure les fonctions nécessaires
require_once "include/functions.inc.php";

// Définir le titre de la page
$pageTitle = 'Page Technique | Climatech';

// Inclure l'en-tête HTML
include "include/header.inc.php";

// Nettoyage des caches vieux de plus de 3 jours
cleanCache(3);

/**
 * Récupération des données depuis les API
 *
 * @var array $apod      Données de la NASA (APOD)
 * @var array $geo       Données GeoPlugin (XML)
 * @var array $geo_json  Données ipinfo.io (JSON)
 * @var array $geo_xml   Données whatismyip.com (XML)
 */
$apod = getApodData() ?? [];
$geo = getGeoData() ?? [];
$geo_json = getGeoIpInfoJSON(null, "b95e1aafd66f5a") ?? [];
$geo_xml = getGeoIpInfoXML() ?? [];
?>

<main class="container tech-page">
    <!-- Section NASA -->
    <section>
        <h2>📷 Image ou vidéo du jour (NASA - APOD)</h2>
        <?php if (isset($apod['media_type']) && $apod['media_type'] === 'image'): ?>
            <figure>
                <img src="<?= htmlspecialchars($apod['url'] ?? '') ?>" alt="Image astronomique du jour (NASA)" class="image-apod" loading="lazy"/>
                <figcaption>Image du jour (NASA)</figcaption>
            </figure>
        <?php elseif (isset($apod['media_type']) && $apod['media_type'] === 'video'): ?>
            <div class="apod-video">
                <iframe src="<?= htmlspecialchars($apod['url'] ?? '') ?>" 
                        width="560" 
                        height="315" 
                        title="Vidéo astronomique du jour (NASA)" 
                        frameborder="0" 
                        allowfullscreen 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                </iframe>
            </div>
        <?php else: ?>
            <p class="error-message"><strong>Erreur :</strong> Type de média non reconnu ou données indisponibles.</p>
        <?php endif; ?>
        <p class="apod-description"><strong>Description :</strong> <span lang="en"><?= htmlspecialchars($apod['explanation'] ?? 'Aucune description disponible') ?></span></p>
    </section>

    <!-- Section GeoPlugin XML -->
    <section>
        <h2>📍 Localisation estimée (GeoPlugin - XML)</h2>
        <?php if (!empty($geo)): ?>
            <ul>
                <li><strong>Adresse IP</strong> : <?= htmlspecialchars($geo['ip'] ?? 'Inconnue') ?></li>
                <li><strong>Ville</strong> : <?= htmlspecialchars($geo['ville'] ?? 'Inconnue') ?></li>
                <li><strong>Région</strong> : <?= htmlspecialchars($geo['region'] ?? 'Inconnue') ?></li>
                <li><strong>Pays</strong> : <?= htmlspecialchars($geo['pays'] ?? 'Inconnu') ?></li>
                <li><strong>Latitude</strong> : <?= htmlspecialchars($geo['lat'] ?? 'n/a') ?></li>
                <li><strong>Longitude</strong> : <?= htmlspecialchars($geo['lon'] ?? 'n/a') ?></li>
            </ul>
        <?php else: ?>
            <p class="error-message">❌ Impossible de récupérer les données GeoPlugin (XML).</p>
        <?php endif; ?>
    </section>

    <!-- Section ipinfo.io JSON -->
    <section>
        <h2>🌍 Localisation via API JSON (ipinfo.io)</h2>
        <?php if (!empty($geo_json)): ?>
            <ul>
                <li><strong>IP</strong> : <?= htmlspecialchars($geo_json['ip'] ?? 'Inconnue') ?></li>
                <li><strong>Ville</strong> : <?= htmlspecialchars($geo_json['ville'] ?? 'Inconnue') ?></li>
                <li><strong>Région</strong> : <?= htmlspecialchars($geo_json['region'] ?? 'Inconnue') ?></li>
                <li><strong>Pays</strong> : <?= htmlspecialchars($geo_json['pays'] ?? 'Inconnu') ?></li>
                <li><strong>Coordonnées</strong> : <?= htmlspecialchars($geo_json['loc'] ?? 'n/a') ?></li>
            </ul>
        <?php else: ?>
            <p class="error-message">❌ Impossible de récupérer les données JSON (ipinfo.io).</p>
        <?php endif; ?>
    </section>

    <!-- Section whatismyip.com XML -->
    <section>
        <h2>📡 Localisation via API XML (whatismyip.com)</h2>
        <?php if (!empty($geo_xml)): ?>
            <ul>
                <li><strong>IP</strong> : <?= htmlspecialchars($geo_xml['ip'] ?? 'Inconnue') ?></li>
                <li><strong>Ville</strong> : <?= htmlspecialchars($geo_xml['ville'] ?? 'Inconnue') ?></li>
                <li><strong>Région</strong> : <?= htmlspecialchars($geo_xml['region'] ?? 'Inconnue') ?></li>
                <li><strong>Pays</strong> : <?= htmlspecialchars($geo_xml['pays'] ?? 'Inconnu') ?></li>
                <li><strong>Latitude</strong> : <?= htmlspecialchars($geo_xml['latitude'] ?? 'Inconnue') ?></li>
                <li><strong>Longitude</strong> : <?= htmlspecialchars($geo_xml['longitude'] ?? 'Inconnue') ?></li>
            </ul>
        <?php else: ?>
            <p class="error-message">❌ Impossible de récupérer les données XML (whatismyip.com) - Vous avez peut-être atteint la limite des appels.</p>
        <?php endif; ?>
    </section>

    <!-- Bannière cookie -->
    <div id="cookie-consent" class="cookie-consent" style="display: <?= shouldShowCookieConsent() ? 'block' : 'none' ?>;">
        <p>Nous utilisons des cookies pour améliorer votre expérience et analyser notre trafic. Acceptez-vous leur utilisation ?</p>
        <div class="cookie-buttons">
            <button id="accept-cookies">Accepter</button>
            <button id="decline-cookies">Refuser</button>
        </div>
    </div>
</main>

<!-- Script cookies -->
<script src="js/cookie-consent.js"></script>

<?php
// Pied de page
include "include/footer.inc.php";
?>
