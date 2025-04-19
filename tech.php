<?php
require_once "include/functions.inc.php";
include "include/header.inc.php";

// 🔁 Nettoyage des caches vieux de +3 jours
cleanCache(3);

// 📡 Récupération des données API
$apod = getApodData();
$geo = getGeoData();
$geo_json = getGeoIpInfoJSON(null, "b95e1aafd66f5a");
$geo_xml = getGeoIpInfoXML();
?>
<main class="container tech-page">
    <h2>📷 Image ou vidéo du jour (NASA - APOD)</h2>
    <?php if ($apod['media_type'] === 'image'): ?>
        <figure>
            <img src="<?= htmlspecialchars($apod['url']) ?>" alt="Image APOD" class="image-apod" />
            <figcaption>Image du jour (NASA)</figcaption>
        </figure>
    <?php elseif ($apod['media_type'] === 'video'): ?>
        <div class="apod-video">
            <iframe src="<?= htmlspecialchars($apod['url']) ?>"
                width="560" height="315"
                frameborder="0"
                allowfullscreen
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
            </iframe>
        </div>
    <?php else: ?>
        <p><strong>Erreur :</strong> Type de média non reconnu.</p>
    <?php endif; ?>

    <p><strong>Description :</strong> <?= htmlspecialchars($apod['explanation']) ?></p>

    <h2>📍 Localisation estimée (GeoPlugin - XML)</h2>
    <?php if ($geo): ?>
        <ul>
            <li><strong>Adresse IP</strong> : <?= htmlspecialchars($geo["ip"]) ?></li>
            <li><strong>Ville</strong> : <?= $geo["ville"] ?: 'Inconnue' ?></li>
            <li><strong>Région</strong> : <?= $geo["region"] ?: 'Inconnue' ?></li>
            <li><strong>Pays</strong> : <?= $geo["pays"] ?: 'Inconnu' ?></li>
            <li><strong>Latitude</strong> : <?= $geo["lat"] ?: 'n/a' ?></li>
            <li><strong>Longitude</strong> : <?= $geo["lon"] ?: 'n/a' ?></li>
        </ul>
    <?php else: ?>
        <p>❌ Impossible de récupérer les données GeoPlugin (XML)</p>
    <?php endif; ?>

    <h2>🌍 Localisation via API JSON (ipinfo.io)</h2>
    <?php if ($geo_json): ?>
        <ul>
            <li><strong>IP</strong> : <?= htmlspecialchars($geo_json['ip']) ?></li>
            <li><strong>Ville</strong> : <?= $geo_json['ville'] ?: 'Inconnue' ?></li>
            <li><strong>Région</strong> : <?= $geo_json['region'] ?: 'Inconnue' ?></li>
            <li><strong>Pays</strong> : <?= $geo_json['pays'] ?: 'Inconnu' ?></li>
            <li><strong>Coordonnées</strong> : <?= $geo_json['loc'] ?: 'n/a' ?></li>
        </ul>
    <?php else: ?>
        <p>❌ Impossible de récupérer les données JSON (ipinfo.io)</p>
    <?php endif; ?>

    <h2>📡 Localisation via API XML (whatismyip.com)</h2>
    <?php if ($geo_xml): ?>
        <ul>
            <li><strong>IP</strong> : <?= htmlspecialchars($geo_xml['ip']) ?></li>
            <li><strong>Ville</strong> : <?= $geo_xml['ville'] ?: 'Inconnue' ?></li>
            <li><strong>Région</strong> : <?= $geo_xml['region'] ?: 'Inconnue' ?></li>
            <li><strong>Pays</strong> : <?= $geo_xml['pays'] ?: 'Inconnu' ?></li>
            <li><strong>Latitude</strong> : <?= $geo_xml['latitude'] ?: 'Inconnue' ?></li>
            <li><strong>Longitude</strong> : <?= $geo_xml['longitude'] ?: 'Inconnue' ?></li>
        </ul>
    <?php else: ?>
        <p>❌ Impossible de récupérer les données XML (whatismyip.com) - Vous avez atteint la limite des appels</p>
    <?php endif; ?>
</main>

<?php include "include/footer.inc.php"; ?>