<?php
require_once "include/functions.inc.php";
include "include/header.inc.php";

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
}

$backgroundImage = getRandomImage("img/photos/") ?? 'img/default-bg.jpg';
$randomImages = getRandomImage("img/photos/", 3) ?? ['img/default1.jpg', 'img/default2.jpg', 'img/default3.jpg'];
$weatherData = getWeatherAlerte($latitude, $longitude);
?>

<style>
    body {
        background: url("<?= htmlspecialchars($backgroundImage) ?>") no-repeat center center fixed;
        background-size: cover;
    }
</style>

<main class="container">
    <!-- Bannière de bienvenue avec h1, h2 et p regroupés -->
    <section class="welcome-banner">
        <h1>ClimaTech</h1>
        <h2>Bienvenue chez ClimaTech</h2>
        <p>Plongez dans l'univers de la météo et explorez les prévisions météo de toute la France.</p>
    </section>

    <!-- Formulaire de recherche par ville -->
    <section class="search-city">
        <form method="get" action="index.php" class="city-search-form">
            <label for="city">Rechercher la météo par ville :</label>
            <input type="text" name="city" id="city" placeholder="Entrez une ville (ex. Paris)" value="<?= htmlspecialchars($searchCity ?? '') ?>" required>
            <button type="submit">🔍 Rechercher</button>
            <?php if ($errorMessage): ?>
                <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>
        </form>
    </section>

    <!-- Conteneur principal avec images et météo -->
    <div class="big-rectangle">
        <!-- Grille d'images -->
        <div class="image-grid">
            <figure class="image-main">
                <img src="<?= htmlspecialchars($randomImages[0]) ?>" alt="Photo météo principale">
            </figure>
            <div class="image-secondary">
                <figure>
                    <img src="<?= htmlspecialchars($randomImages[1]) ?>" alt="Photo météo secondaire" class="changing-image">
                </figure>
                <figure>
                    <img src="<?= htmlspecialchars($randomImages[2]) ?>" alt="Photo météo secondaire">
                </figure>
            </div>
        </div>

        <!-- Section météo avec icône gérée par getWeatherIcon() -->
        <section class="weather-box <?= $weatherData['type'] === 'alert' ? 'alert' : '' ?>">
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

        <!-- Section statistiques -->
        <div class="weather-extras">
            <h3>Statistiques</h3>
            <p>Consultez nos analyses météo avancées <a href="stats.php?style=<?= htmlspecialchars($style ?? 'day') ?>">ici</a></p>
        </div>
    </div>

    <!-- Liens de navigation -->
    <section class="page-links-section">
        <ul class="page-links">
            <li><a href="meteo.php?style=<?= htmlspecialchars($style ?? 'day') ?>">🔍 Rechercher la météo par ville</a></li>
            <li><a href="stats.php?style=<?= htmlspecialchars($style ?? 'day') ?>">📊 Statistiques de consultation</a></li>
            <li><a href="tech.php?style=<?= htmlspecialchars($style ?? 'day') ?>">⚙️ Page technique</a></li>
        </ul>
    </section>
</main>

<!-- Script pour l'animation des images -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const changingImage = document.querySelector('.changing-image');
    if (changingImage) {
        const availableImages = [
            <?php
            $allImages = array_diff(scandir(__DIR__ . '/img/photos/'), ['.', '..']);
            $imagePaths = array_map(fn($img) => "'img/photos/" . htmlspecialchars($img) . "'", $allImages);
            echo implode(',', $imagePaths);
            ?>
        ];

        setInterval(() => {
            const currentSrc = changingImage.src.split('/').pop();
            let newImage;
            do {
                newImage = availableImages[Math.floor(Math.random() * availableImages.length)];
            } while (newImage.split('/').pop() === currentSrc);

            changingImage.style.opacity = 0;
            setTimeout(() => {
                changingImage.src = newImage;
                changingImage.style.opacity = 1;
            }, 500);
        }, 7000);
    }
});
</script>
<!-- Script pour l'animation des images -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const changingImage = document.querySelector('.changing-image');
    if (changingImage) {
        const availableImages = [
            <?php
            $allImages = array_diff(scandir(__DIR__ . '/img/photos/'), ['.', '..']);
            $imagePaths = array_map(fn($img) => "'img/photos/" . htmlspecialchars($img) . "'", $allImages);
            echo implode(',', $imagePaths);
            ?>
        ];

        setInterval(() => {
            const currentSrc = changingImage.src.split('/').pop();
            let newImage;
            do {
                newImage = availableImages[Math.floor(Math.random() * availableImages.length)];
            } while (newImage.split('/').pop() === currentSrc);

            changingImage.style.opacity = 0;
            setTimeout(() => {
                changingImage.src = newImage;
                changingImage.style.opacity = 1;
            }, 500);
        }, 7000);
    }

    // Gestion de la bannière de consentement aux cookies
    const cookieConsent = document.getElementById('cookie-consent');
    if (cookieConsent && !document.cookie.includes('cookie_consent')) {
        cookieConsent.style.display = 'block';
    }

    document.getElementById('accept-cookies')?.addEventListener('click', function() {
        document.cookie = 'cookie_consent=accepted; path=/; max-age=' + (365 * 24 * 60 * 60);
        cookieConsent.style.display = 'none';
    });

    document.getElementById('decline-cookies')?.addEventListener('click', function() {
        document.cookie = 'cookie_consent=declined; path=/; max-age=' + (365 * 24 * 60 * 60);
        cookieConsent.style.display = 'none';
    });
});
</script>

<!-- Bannière de consentement aux cookies -->
<div id="cookie-consent" class="cookie-consent">
    <p>Nous utilisons des cookies pour améliorer votre expérience et analyser notre trafic. Acceptez-vous leur utilisation ?</p>
    <div class="cookie-buttons">
        <button id="accept-cookies">Accepter</button>
        <button id="decline-cookies">Refuser</button>
    </div>
</div>

<?php include "include/footer.inc.php"; ?>