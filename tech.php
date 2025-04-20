<?php
require_once "include/functions.inc.php";
$pageTitle = 'Page Technique | Climatech';
include "include/header.inc.php";

// Nettoyage des caches vieux de plus de 3 jours
cleanCache(3);

// Récupération des données API
$apod = getApodData() ?? [];
$geo = getGeoData() ?? [];
$geo_json = getGeoIpInfoJSON(null, "b95e1aafd66f5a") ?? [];
$geo_xml = getGeoIpInfoXML() ?? [];
?>

<main class="container tech-page">
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

    <div id="cookie-consent" class="cookie-consent" style="display: <?= shouldShowCookieConsent() ? 'block' : 'none' ?>;">
        <p>Nous utilisons des cookies pour améliorer votre expérience et analyser notre trafic. Acceptez-vous leur utilisation ?</p>
        <div class="cookie-buttons">
            <button id="accept-cookies">Accepter</button>
            <button id="decline-cookies">Refuser</button>
        </div>
    </div>
</main>

<script src="js/cookie-consent.js"></script>

<?php include "include/footer.inc.php"; ?>