<?php
// Sécurité : empêcher l'accès direct
if (!defined('PHP_EOL')) {
    die('Accès direct interdit');
}

// Inclure les fonctions utilitaires
if (!file_exists('include/functions.inc.php')) {
    die('Erreur : Fichier functions.inc.php manquant.');
}
require_once 'include/functions.inc.php';

// Définir le titre de la page
$pageTitle = 'Accueil | Climatech';

// Vérifier l'existence de header.inc.php
if (!file_exists('include/header.inc.php')) {
    die('Erreur : Fichier header.inc.php manquant.');
}
include 'include/header.inc.php';

// Définir le Content-Type
header('Content-Type: text/html; charset=UTF-8');

// Gestion de la recherche par ville
$searchCity = filter_input(INPUT_GET, 'city', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
$latitude = '48.8566'; // Paris par défaut
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
        $errorMessage = 'Ville non trouvée. Affichage des données pour Paris par défaut.';
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
if (!$randomImages || count($randomImages) < 3) {
    $defaultImages = [
        'img/photos/degleex-ganzorig-wQImoykAwGs-unsplash-min-min.jpg',
        'img/photos/mike-kotsch-9wTWFyInJ4Y-unsplash-min-min.jpg',
        'img/photos/benjamin-voros-AD6rn3vqG7o-unsplash-min-min.jpg'
    ];
    $randomImages = array_merge($randomImages ?: [], array_slice($defaultImages, 0, 3 - count($randomImages ?: [])));
}
$weatherData = getWeatherAlerte($latitude, $longitude);
$newsData = getEnvironmentalNews();
$imagesJson = json_encode($randomImages, JSON_HEX_QUOT | JSON_HEX_APOS);
?>

<!-- Lien CSS spécifique pour index.php -->
<link rel="stylesheet" href="css/index.css" />

<main class="container">
    <!-- Bannière de bienvenue -->
    <section class="welcome-banner" aria-labelledby="welcome-title">
        <h2 id="welcome-title">Bienvenue chez Climatech</h2>
        <p>Plongez dans l'univers de la météo et explorez les prévisions météo de toute la France.</p>
    </section>

    <!-- Recherche par ville -->
    <section class="search-city" aria-labelledby="search-title">
        <h2 id="search-title">Rechercher une ville</h2>
        <form method="get" action="index.php" class="city-search-form">
            <label for="city">Rechercher la météo par ville :</label>
            <input type="text" name="city" id="city" placeholder="Entrez une ville (ex. Paris)" value="<?php echo htmlspecialchars($searchCity ?? '', ENT_QUOTES, 'UTF-8'); ?>" required="required" />
            <button type="submit" class="icon-search">Rechercher</button>
            <?php if ($errorMessage): ?>
                <p class="error-message"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </form>
    </section>

    <!-- Grille d'images et météo -->
    <div class="big-rectangle">
        <div class="image-grid">
            <figure class="image-main">
                <img src="<?php echo htmlspecialchars($randomImages[0], ENT_QUOTES, 'UTF-8'); ?>" alt="Photo de <?php echo htmlspecialchars($searchCity ?? 'ville', ENT_QUOTES, 'UTF-8'); ?>" />
            </figure>
            <div class="image-secondary">
                <figure>
                    <img src="<?php echo htmlspecialchars($randomImages[1], ENT_QUOTES, 'UTF-8'); ?>" alt="Photo de <?php echo htmlspecialchars($searchCity ?? 'ville', ENT_QUOTES, 'UTF-8'); ?>" class="changing-image" data-images="<?php echo htmlspecialchars($imagesJson, ENT_QUOTES, 'UTF-8'); ?>" />
                </figure>
                <figure>
                    <img src="<?php echo htmlspecialchars($randomImages[2], ENT_QUOTES, 'UTF-8'); ?>" alt="Photo de <?php echo htmlspecialchars($searchCity ?? 'ville', ENT_QUOTES, 'UTF-8'); ?>" />
                </figure>
            </div>
        </div>

        <!-- Boîte météo -->
        <section class="weather-box <?php echo htmlspecialchars($weatherData['type'] === 'alert' ? 'alert' : '', ENT_QUOTES, 'UTF-8'); ?>" aria-live="polite">
            <?php if ($weatherData && isset($weatherData['type']) && $weatherData['type'] !== 'error'): ?>
                <div class="weather-icon">
                    <?php
                    $description = $weatherData['type'] === 'alert' ? ($weatherData['message'] ?? 'alerte') : ($weatherData['condition'] ?? 'unknown');
                    $iconCode = $weatherData['icon'] ?? ($weatherData['type'] === 'alert' ? 'alert' : 'unknown');
                    $iconUrl = getWeatherIcon($description, $iconCode);
                    ?>
                    <img src="<?php echo htmlspecialchars($iconUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Icône météo" />
                </div>
                <div class="weather-info">
                    <?php if ($weatherData['type'] === 'alert'): ?>
                        <h3 class="icon-alert">Alerte Météo</h3>
                        <p><strong><?php echo htmlspecialchars($weatherData['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></p>
                        <p><?php echo htmlspecialchars($weatherData['details'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php else: ?>
                        <h3><?php echo htmlspecialchars($weatherData['condition'] ?? 'Conditions actuelles', ENT_QUOTES, 'UTF-8'); ?> à <?php echo htmlspecialchars($searchCity ?? 'Paris', ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($weatherData['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="weather-details">
                            <div class="weather-detail">
                                <span>Température</span>
                                <strong><?php echo htmlspecialchars($weatherData['temp'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?> °C</strong>
                            </div>
                            <div class="weather-detail">
                                <span>Ressenti</span>
                                <strong><?php echo htmlspecialchars($weatherData['feels_like'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?> °C</strong>
                            </div>
                            <div class="weather-detail">
                                <span>Humidité</span>
                                <strong><?php echo htmlspecialchars($weatherData['humidity'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?> %</strong>
                            </div>
                            <div class="weather-detail">
                                <span>Vent</span>
                                <strong><?php echo htmlspecialchars($weatherData['wind'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?> km/h</strong>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="sun-times">
                        <span class="icon-sun">Lever du soleil : <?php echo htmlspecialchars($weatherData['sunrise'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="icon-moon">Coucher du soleil : <?php echo htmlspecialchars($weatherData['sunset'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            <?php else: ?>
                <p>Impossible de récupérer les données météo.</p>
            <?php endif; ?>
        </section>

        <!-- Actualités -->
        <div class="weather-extras news-section">
            <h3>Actualités Environnement &amp; Météo</h3>
            <?php if (isset($newsData['error'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($newsData['error'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php else: ?>
                <ul class="news-list">
                    <?php foreach ($newsData['articles'] as $article): ?>
                        <li class="news-item">
                            <?php if (!empty($article['image'])): ?>
                                <img src="<?php echo htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($article['title'] ?? 'Article', ENT_QUOTES, 'UTF-8'); ?>" class="news-image" />
                            <?php else: ?>
                                <img src="img/placeholder-news.jpg" alt="Image par défaut" class="news-image" />
                            <?php endif; ?>
                            <div class="news-content">
                                <h4><a href="<?php echo htmlspecialchars($article['url'] ?? '#', ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($article['title'] ?? 'Sans titre', ENT_QUOTES, 'UTF-8'); ?></a></h4>
                                <p class="news-description"><?php echo htmlspecialchars($article['description'] ?? 'Aucune description disponible', ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="news-meta">Source : <?php echo htmlspecialchars($article['source'] ?? 'Inconnu', ENT_QUOTES, 'UTF-8'); ?> | Publié le : <?php echo htmlspecialchars($article['publishedAt'] ?? 'Inconnu', ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Liens de navigation -->
    <div class="page-links-section">
        <ul class="page-links">
            <li><a href="meteo.php?style=<?php echo htmlspecialchars($currentTheme ?? 'standard', ENT_QUOTES, 'UTF-8'); ?>" class="icon-search">Rechercher la météo par ville</a></li>
            <li><a href="stats.php?style=<?php echo htmlspecialchars($currentTheme ?? 'standard', ENT_QUOTES, 'UTF-8'); ?>" class="icon-stats">Statistiques de consultation</a></li>
            <li><a href="tech.php?style=<?php echo htmlspecialchars($currentTheme ?? 'standard', ENT_QUOTES, 'UTF-8'); ?>" class="icon-tech">Page technique</a></li>
        </ul>
    </div>

    <!-- Bannière de consentement -->
    <div id="cookie-consent" class="cookie-consent<?php echo shouldShowCookieConsent() ? '' : ' hidden'; ?>" role="dialog" aria-label="Consentement aux cookies">
        <p>Nous utilisons des cookies pour améliorer votre expérience et analyser notre trafic. Acceptez-vous leur utilisation ?</p>
        <div class="cookie-buttons">
            <button id="accept-cookies">Accepter</button>
            <button id="decline-cookies">Refuser</button>
        </div>
    </div>
</main>

<!-- Scripts -->
<script src="js/image-animation.js"></script>
<script src="js/cookie-consent.js"></script>

<?php
// Inclure le pied de page
if (!file_exists('include/footer.inc.php')) {
    die('Erreur : Fichier footer.inc.php manquant.');
}
include 'include/footer.inc.php';
?>