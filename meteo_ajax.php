<?php
/**
 * Script pour afficher les données météo d'une ville.
 *
 * Ce fichier récupère la météo actuelle, celle de demain,
 * ainsi que les prévisions sur 9 jours pour une ville donnée via GET.
 *
 * PHP version 8+
 *
 * @category Météo
 * @package  Climatech\Meteo
 * @author   VotreNom
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://climatech.example.com/meteo_display.php
 */

// Inclusion des fonctions nécessaires
require_once "include/functions.inc.php";

// Récupération de la ville depuis les paramètres GET
$ville = $_GET['ville'] ?? '';

if ($ville) {
    // Enregistre la recherche de ville
    logCitySearch($ville);

    // Récupère les données météo de la ville
    $meteo = getMeteo($ville);

    if ($meteo):
        ?>
        <h2 class="weather-title">🌤️ Météo à <?= htmlspecialchars($ville) ?></h2>

        <div class="weather-cards-container">
            <!-- Météo actuelle -->
            <div class="weather-card weather-card-current">
                <div class="weather-icon">
                    <img src="<?= $meteo['icon'] ?>" alt="<?= htmlspecialchars($meteo['description']) ?>">
                </div>
                <div class="weather-info">
                    <h3 class="weather-subtitle">Actuellement</h3>
                    <p class="weather-temperature"><?= $meteo['temperature'] ?> °C</p>
                    <p class="weather-description"><?= htmlspecialchars($meteo['description']) ?></p>
                    <p class="weather-detail">Ressenti : <?= $meteo['feels_like'] ?> °C</p>
                    <p class="weather-detail">Vent : <?= $meteo['wind_speed'] ?> km/h</p>
                    <p class="weather-detail">Humidité : <?= $meteo['humidity'] ?> %</p>
                    <p class="weather-detail">Pression : <?= $meteo['pressure'] ?> hPa</p>
                </div>
            </div>

            <!-- Météo de demain -->
            <div class="weather-card weather-card-tomorrow">
                <div class="weather-icon">
                    <img src="<?= $meteo['demain']['icon'] ?>" alt="<?= htmlspecialchars($meteo['demain']['description']) ?>">
                </div>
                <div class="weather-info">
                    <h3 class="weather-subtitle">Demain</h3>
                    <p class="weather-temperature"><?= htmlspecialchars($meteo['demain']['temperature']) ?></p>
                    <p class="weather-description"><?= htmlspecialchars($meteo['demain']['description']) ?></p>
                    <p class="weather-detail">Ressenti : <?= $meteo['demain']['feels_like'] ?> °C</p>
                    <p class="weather-detail">Vent : <?= $meteo['demain']['wind_speed'] ?> km/h</p>
                    <p class="weather-detail">Humidité : <?= $meteo['demain']['humidity'] ?> %</p>
                    <p class="weather-detail">Pression : <?= $meteo['demain']['pressure'] ?> hPa</p>
                </div>
            </div>
        </div>

        <!-- Prévisions J+2 à J+10 -->
        <h3 class="weather-forecast-title">Prévisions pour la semaine</h3>
        <div class="weather-forecast-container">
            <?php
            // Extraction des prévisions du jour 2 à 10
            $previsions = array_slice($meteo['semaine'], 2, 9);
            foreach ($previsions as $index => $jour):
                $date = $jour['date'];
                $condition = $jour['description'];
                $temp = $jour['temperature'];
                $feels_like = $jour['feels_like'];
                $wind_speed = $jour['wind_speed'];
                $humidity = $jour['humidity'];
                $pressure = $jour['pressure'];
                $icon = $jour['icon'];
            ?>
                <div class="weather-forecast-card" data-index="<?= $index ?>">
                    <div class="forecast-date"><?= htmlspecialchars($date) ?></div>
                    <div class="forecast-description"><?= htmlspecialchars($condition) ?></div>
                    <div class="forecast-temperature"><?= htmlspecialchars($temp) ?></div>
                    <div class="forecast-details" style="display: none;">
                        <div class="weather-icon">
                            <img src="<?= $icon ?>" alt="<?= htmlspecialchars($condition) ?>">
                        </div>
                        <p class="weather-detail">Ressenti : <?= $feels_like ?> °C</p>
                        <p class="weather-detail">Vent : <?= $wind_speed ?> km/h</p>
                        <p class="weather-detail">Humidité : <?= $humidity ?> %</p>
                        <p class="weather-detail">Pression : <?= $pressure ?> hPa</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    else:
        echo '<p class="weather-error">Impossible de récupérer la météo pour ' . htmlspecialchars($ville) . '.</p>';
    endif;
} else {
    echo '<p class="weather-error">Veuillez sélectionner une ville.</p>';
}
?>
