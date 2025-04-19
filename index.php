<?php
require_once "include/functions.inc.php";
$pageTitle = 'Accueil | Climatech';
include "include/header.inc.php";

// Gestion de la recherche par ville
$searchCity = filter_input(INPUT_GET, 'city', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
$latitude = '48.8566';
$longitude = '2.3522';
$errorMessage = null;

if (!$searchCity && isset($_COOKIE['last_city'])) {
    $lastCityData = json_decode($_COOKIE['last_city'], true);
    $searchCity = $lastCityData['city'] ?? null;
}

if ($searchCity) {
    logCitySearch($searchCity);
    $geoData = getGeoDataByCity($searchCity);
    if ($geoData && isset($geoData['lat']) && isset($geoData['lon'])) {
        $latitude = $geoData['lat'];
        $longitude = $geoData['lon'];
    } else {
        $errorMessage = "Ville non trouvée. Affichage des données pour Paris par défaut.";
    }
    $cookieData = [
        'city' => $searchCity,
        'date' => date('Y-m-d H:i:s')
    ];
    setConditionalCookie('last_city', json_encode($cookieData), time() + (30 * 24 * 3600), '/');
} else {
    $geoData = getGeoData();
    $latitude = $geoData['lat'] ?? $latitude;
    $longitude = $geoData['lon'] ?? $longitude;
    $searchCity = getCityFromCoordinates($latitude, $longitude) ?? 'Paris';
}

// Images pour la grille
$randomImages = getPixabayCityImages($searchCity, 3);
if (!$randomImages) {
    $randomImages = getRandomImage("img/photos/", 3) ?? ['img/photos/degleex-ganzorig-wQImoykAwGs-unsplash-min-min.jpg', 'img/photos/mike-kotsch-9wTWFyInJ4Y-unsplash-min-min.jpg', 'img/benjamin-voros-AD6rn3vqG7o-unsplash-min-min.jpg'];
}
$weatherData = getWeatherAlerte($latitude, $longitude);
$newsData = getEnvironmentalNews();
$imagesJson = json_encode($randomImages);
?>

<main class="container">
    <section class="welcome-banner">
        <h2>Bienvenue chez Climatech</h2>
        <p>Plongez dans l'univers de la météo et explorez les prévisions météo de toute la France.</p>
    </section>

    <section class="search-city">
        <h2>Rechercher une ville</h2>
        <form method="get" action="index.php" class="city-search-form">
            <label for="city">Rechercher la météo par ville :</label>
            <input type="text" name="city" id="city" placeholder="Entrez une ville (ex. Paris)" value="<?= htmlspecialchars($searchCity ?? '') ?>" required>
            <button type="submit">🔍 Rechercher</button>
            <?php if ($errorMessage): ?>
                <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>
        </form>
    </section>

    <div class="big-rectangle">
        <div class="image-grid">
            <figure class="image-main">
                <img src="<?= htmlspecialchars($randomImages[0]) ?>" alt="Photo de <?= htmlspecialchars($searchCity ?? 'ville') ?>">
            </figure>
            <div class="image-secondary">
                <figure>
                    <img src="<?= htmlspecialchars($randomImages[1]) ?>" alt="Photo de <?= htmlspecialchars($searchCity ?? 'ville') ?>" class="changing-image" data-images='<?= htmlspecialchars($imagesJson) ?>'>
                </figure>
                <figure>
                    <img src="<?= htmlspecialchars($randomImages[2]) ?>" alt="Photo de <?= htmlspecialchars($searchCity ?? 'ville') ?>">
                </figure>
            </div>
        </div>

        <section class="weather-box <?= htmlspecialchars($weatherData['type'] === 'alert' ? 'alert' : '') ?>">
            <?php if ($weatherData && $weatherData['type'] !== 'error'): ?>
                <div class="weather-icon">
                    <?php
                    $description = $weatherData['type'] === 'alert' ? ($weatherData['message'] ?? 'alerte') : ($weatherData['condition'] ?? 'unknown');
                    $iconCode = $weatherData['icon'] ?? ($weatherData['type'] === 'alert' ? 'alert' : 'unknown');
                    $iconUrl = getWeatherIcon($description, $iconCode);
                    ?>
                    <img src="<?= htmlspecialchars($iconUrl) ?>" alt="Icône météo">
                </div>
                <div class="weather-info">
                    <?php if ($weatherData['type'] === 'alert'): ?>
                        <h3>⚠️ Alerte Météo</h3>
                        <p><strong><?= htmlspecialchars($weatherData['message']) ?></strong></p>
                        <p><?= htmlspecialchars($weatherData['details']) ?></p>
                    <?php else: ?>
                        <h3><?= htmlspecialchars($weatherData['condition'] ?? 'Conditions actuelles') ?> à <?= htmlspecialchars($searchCity ?? 'Paris') ?></h3>
                        <p><?= htmlspecialchars($weatherData['message']) ?></p>
                        <div class="weather-details">
                            <div class="weather-detail">
                                <span>Température</span>
                                <strong><?= htmlspecialchars($weatherData['temp']) ?> °C</strong>
                            </div>
                            <div class="weather-detail">
                                <span>Ressenti</span>
                                <strong><?= htmlspecialchars($weatherData['feels_like']) ?> °C</strong>
                            </div>
                            <div class="weather-detail">
                                <span>Humidité</span>
                                <strong><?= htmlspecialchars($weatherData['humidity']) ?> %</strong>
                            </div>
                            <div class="weather-detail">
                                <span>Vent</span>
                                <strong><?= htmlspecialchars($weatherData['wind']) ?> km/h</strong>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="sun-times">
                        <span>☀️ Lever: <?= htmlspecialchars($weatherData['sunrise']) ?></span>
                        <span>🌙 Coucher: <?= htmlspecialchars($weatherData['sunset']) ?></span>
                    </div>
                </div>
            <?php else: ?>
                <p>⚠️ Impossible de récupérer les données météo.</p>
            <?php endif; ?>
        </section>

        <div class="weather-extras news-section">
            <h3>Actualités Environnement & Météo</h3>
            <?php if (isset($newsData['error'])): ?>
                <p class="error-message"><?= htmlspecialchars($newsData['error']) ?></p>
            <?php else: ?>
                <ul class="news-list">
                    <?php foreach ($newsData['articles'] as $article): ?>
                        <li class="news-item">
                            <?php if (!empty($article['image'])): ?>
                                <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="news-image">
                            <?php else: ?>
                                <img src="img/placeholder-news.jpg" alt="Image par défaut" class="news-image">
                            <?php endif; ?>
                            <div class="news-content">
                                <h4><a href="<?= htmlspecialchars($article['url']) ?>" target="_blank"><?= htmlspecialchars($article['title']) ?></a></h4>
                                <p class="news-description"><?= htmlspecialchars($article['description']) ?></p>
                                <p class="news-meta">Source : <?= htmlspecialchars($article['source']) ?> | Publié le : <?= htmlspecialchars($article['publishedAt']) ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="page-links-section">
        <ul class="page-links">
            <li><a href="meteo.php?style=<?= htmlspecialchars($style ?? 'standard') ?>">🔍 Rechercher la météo par ville</a></li>
            <li><a href="stats.php?style=<?= htmlspecialchars($style ?? 'standard') ?>">📊 Statistiques de consultation</a></li>
            <li><a href="tech.php?style=<?= htmlspecialchars($style ?? 'standard') ?>">⚙️ Page technique</a></li>
        </ul>
    </div>

    <div id="cookie-consent" class="cookie-consent" style="display: <?= shouldShowCookieConsent() ? 'block' : 'none' ?>;">
        <p>Nous utilisons des cookies pour améliorer votre expérience et analyser notre trafic. Acceptez-vous leur utilisation ?</p>
        <div class="cookie-buttons">
            <button id="accept-cookies">Accepter</button>
            <button id="decline-cookies">Refuser</button>
        </div>
    </div>
</main>

<script src="js/image-animation.js"></script>

<?php include "include/footer.inc.php"; ?>