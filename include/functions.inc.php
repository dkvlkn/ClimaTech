<?php
// Fonction pour obtenir une image d'arrière-plan aléatoire
function getBackgroundImage($directory = 'img/photos/', $default = 'img/arriere_plan_header_clair.jpg')
{
    $image = getRandomImage($directory); // Utilise ta fonction avec chemin relatif
    return $image ?? $default;
}
/**
 * Récupère une ou plusieurs images aléatoires dans un dossier donné.
 * @param string $dir Chemin du dossier contenant les images.
 * @param int $count Nombre d'images à sélectionner (par défaut 1).
 * @return string|array|null Chemin d'une image (si $count=1) ou tableau de chemins (si $count>1), ou null si aucune image.
 */
function getRandomImage($dir = "../img/photos/", $count = 1)
{
    // Vérifie si le dossier existe
    if (!is_dir($dir)) {
        return $count === 1 ? null : [];
    }

    // Récupère toutes les images (jpg, jpeg, png, gif)
    $files = array_diff(scandir($dir), ['.', '..']);
    $images = array_filter($files, function ($file) use ($dir) {
        return preg_match('/\.(jpg|jpeg|png|gif)$/i', $file) && is_file($dir . $file);
    });

    // Si aucune image n'est trouvée, retourne null (pour 1 image) ou un tableau vide (pour plusieurs)
    if (empty($images)) {
        return $count === 1 ? null : [];
    }

    // Convertit les noms de fichiers en chemins complets
    $images = array_map(function ($image) use ($dir) {
        return "img/photos/" . $image;
    }, array_values($images));

    // Si on demande une seule image
    if ($count === 1) {
        return $images[array_rand($images)];
    }

    // Si on demande plusieurs images
    $selected_images = [];
    $attempts = 0;
    $max_attempts = $count * 2; // Limite pour éviter une boucle infinie

    // Continue jusqu'à obtenir le nombre d'images demandé ou atteindre la limite d'essais
    while (count($selected_images) < $count && $attempts < $max_attempts && !empty($images)) {
        $index = array_rand($images);
        $image = $images[$index];
        if (!in_array($image, $selected_images)) {
            $selected_images[] = $image;
        }
        // Retire l'image sélectionnée pour éviter les doublons
        unset($images[$index]);
        $images = array_values($images);
        $attempts++;
    }

    return $selected_images;
}

/**
 * Récupère le nom de la ville à partir des coordonnées via OpenWeatherMap
 * @param float $lat Latitude
 * @param float $lon Longitude
 * @return string|null Nom de la ville ou null si échec
 */
function getCityFromCoordinates($lat, $lon)
{
    $apiKey = '07cdca46f2c356ffb34c6b0f3e240cb5'; // Votre clé API OpenWeatherMap
    $url = "https://api.openweathermap.org/geo/1.0/reverse?lat=" . urlencode($lat) . "&lon=" . urlencode($lon) . "&limit=1&appid=" . urlencode($apiKey);

    $response = @file_get_contents($url);
    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    if (!isset($data[0]['name']) || empty($data[0]['name'])) {
        return null;
    }

    return $data[0]['name'];
}

/**
 * Récupère des images aléatoires d'une ville via l'API Pixabay
 * @param string $city Nom de la ville
 * @param int $count Nombre d'images à récupérer (max 3)
 * @return array|null Tableau d'URLs d'images ou null si échec
 */
function getPixabayCityImages($city, $count = 10)
{
    // Remplacez par votre clé API Pixabay
    $apiKey = '49739011-28ed38da4f4c58d9c9fb403f4';
    $url = "https://pixabay.com/api/?key=" . urlencode($apiKey) . "&q=" . urlencode($city) . "&image_type=photo&category=places&per_page=" . min($count, 3);

    // Effectuer la requête
    $response = @file_get_contents($url);
    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    if (!isset($data['hits']) || empty($data['hits'])) {
        return null;
    }

    // Extraire les URLs des images
    $images = array_map(function ($hit) {
        return $hit['webformatURL'] ?? null;
    }, array_slice($data['hits'], 0, $count));

    // Filtrer les valeurs null
    $images = array_filter($images);

    return !empty($images) ? $images : null;
}

function getApodData($api_key = "dqUiZ2IwqKnOYADVpNhzzfiM9WY4XjdJshceSy62")
{
    $date = date("Y-m-d");
    $cacheDir = __DIR__ . '/../cache/apod/';
    $cacheFile = $cacheDir . "apod-$date.json";
    $cacheTTL = 24 * 60 * 60; // Durée de vie : 24 heures

    // Vérifie si le fichier existe et n'est pas expiré
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTTL)) {
        $json = file_get_contents($cacheFile);
        return json_decode($json, true);
    }

    // Si le cache est expiré ou n'existe pas, appelle l'API
    $url = "https://api.nasa.gov/planetary/apod?api_key=$api_key&thumbs=True";
    $context = stream_context_create(["http" => ["timeout" => 5]]);
    $json = file_get_contents($url, false, $context);
    if (!$json) return null;

    $data = json_decode($json, true);

    if (isset($data["media_type"])) {
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        file_put_contents($cacheFile, json_encode($data, JSON_PRETTY_PRINT));
        return $data;
    }

    return null;
}

function cleanCache($days = 3)
{
    $cacheDir = __DIR__ . '/../cache/';
    if (!is_dir($cacheDir)) return;

    $files = scandir($cacheDir);
    $now = time();

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $filePath = $cacheDir . $file;

        if (is_file($filePath)) {
            $fileTime = filemtime($filePath);
            if ($now - $fileTime > $days * 86400) {
                unlink($filePath);
            }
        }
    }
}

function getGeoData($ip = null, $cacheTTL = 24 * 60 * 60) // Ajout d'un TTL par défaut de 24h
{
    if ($ip === null) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    $cacheDir = __DIR__ . '/../cache/ip3/';
    $cacheFile = $cacheDir . 'geo-' . str_replace('.', '_', $ip) . '.xml';

    // Vérifie si le fichier existe et n'est pas expiré
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTTL)) {
        $xml = simplexml_load_file($cacheFile);
    } else {
        $url = "http://www.geoplugin.net/xml.gp?ip=$ip";
        $xml = simplexml_load_file($url);

        if ($xml && isset($xml->geoplugin_countryName)) {
            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0755, true);
            }
            file_put_contents($cacheFile, $xml->asXML());
        }
    }

    if (!$xml) return null;

    return [
        "ip"     => $ip,
        "ville"  => (string) $xml->geoplugin_city,
        "region" => (string) $xml->geoplugin_region,
        "pays"   => (string) $xml->geoplugin_countryName,
        "lat"    => (string) $xml->geoplugin_latitude,
        "lon"    => (string) $xml->geoplugin_longitude
    ];
}

/**
 * Récupère la liste des régions depuis region.csv
 * @return array Liste des noms de régions
 */
function getRegions()
{
    $file = __DIR__ . '/../data/region.csv';
    $regions = [];

    if (!file_exists($file)) return $regions;

    if (($handle = fopen($file, 'r')) !== false) {
        fgetcsv($handle); // Ignorer l'en-tête
        while (($row = fgetcsv($handle)) !== false) {
            $regions[] = $row[5]; // "LIBELLE" (nom de la région)
        }
        fclose($handle);
    }

    sort($regions);
    return array_unique($regions);
}

/**
 * Récupère les départements d'une région donnée depuis departement.csv
 * @param string $regionNom Nom de la région
 * @return array Liste des noms de départements
 */
function getDepartements($regionNom)
{
    $file = __DIR__ . '/../data/departement.csv';
    $departements = [];
    $regionCode = null;

    // Trouver le code de la région à partir de son nom
    $regionFile = __DIR__ . '/../data/region.csv';
    if (file_exists($regionFile) && ($handle = fopen($regionFile, 'r')) !== false) {
        fgetcsv($handle); // Ignorer l'en-tête
        while (($row = fgetcsv($handle)) !== false) {
            if (trim($row[5]) === trim($regionNom)) { // "LIBELLE"
                $regionCode = $row[0]; // "REG"
                break;
            }
        }
        fclose($handle);
    }

    if (!$regionCode || !file_exists($file)) return $departements;

    if (($handle = fopen($file, 'r')) !== false) {
        fgetcsv($handle); // Ignorer l'en-tête
        while (($row = fgetcsv($handle)) !== false) {
            if (trim($row[1]) === $regionCode) { // "REG"
                $departements[] = $row[6]; // "LIBELLE" (nom du département)
            }
        }
        fclose($handle);
    }

    sort($departements);
    return array_unique($departements);
}

/**
 * Récupère les villes d'un département donné depuis cities.csv en utilisant son nom
 * @param string $departementNom Nom du département (ex. "Ain")
 * @return array Liste triée et unique des noms de villes
 */
function getVilles($departementNom)
{
    $villes = [];
    $deptCode = null;

    // Chemins des fichiers CSV
    $deptFile = __DIR__ . '/../data/departement.csv';
    $citiesFile = __DIR__ . '/../data/cities.csv';

    // Vérifier l'existence des fichiers avant de continuer
    if (!file_exists($deptFile) || !file_exists($citiesFile)) {
        return $villes; // Retourne un tableau vide si un fichier est manquant
    }

    // Trouver le code du département à partir de son nom dans departement.csv
    if (($handle = fopen($deptFile, 'r')) !== false) {
        fgetcsv($handle); // Ignorer l'en-tête
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) > 6 && trim($row[6]) === trim($departementNom)) { // "LIBELLE"
                $deptCode = trim($row[0]); // "DEP"
                break;
            }
        }
        fclose($handle);
    }

    // Si aucun code de département n'est trouvé, retourner un tableau vide
    if (!$deptCode) {
        return $villes;
    }

    // Récupérer les villes dans cities.csv en utilisant department_code
    if (($handle = fopen($citiesFile, 'r')) !== false) {
        fgetcsv($handle); // Ignorer l'en-tête
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) > 4 && trim($row[1]) === $deptCode) { // "department_code"
                $villes[] = trim($row[4]); // "name"
            }
        }
        fclose($handle);
    }

    // Trier et supprimer les doublons
    $villes = array_unique($villes);
    sort($villes);

    return $villes;
}
/**
 * Retourne le chemin d'une icône locale en fonction du code OpenWeatherMap
 * @param string $iconCode Code de l'icône (ex. "01d", "10n")
 * @return string Chemin ou URL de l'icône
 */
function getWeatherIcon2($iconCode)
{
    $iconMap = [
        '01d' => 'img/icons/sun.svg',
        '01n' => 'img/icons/moon.svg',
        '02d' => 'img/icons/cloud-sun.svg',
        '02n' => 'img/icons/cloudy-night.svg',
        '03d' => 'img/icons/cloudy.svg',
        '03n' => 'img/icons/cloudy.svg',
        '04d' => 'img/icons/cloud-sun',
        '04n' => 'img/icons/cloudy-night.svg',
        '09d' => 'img/icons/rain.svg',
        '09n' => 'img/icons/rain.svg',
        '10d' => 'img/icons/rain.svg',
        '10n' => 'img/icons/rain.svg',
        '11d' => 'img/icons/storm.svg',
        '11n' => 'img/icons/storm.svg',
        '13d' => 'img/icons/snowd.svg',
        '13n' => 'img/icons/snown.svg',
        '50d' => 'img/icons/mist.svg',
        '50n' => 'img/icons/mist.svg'
    ];
    return $iconMap[$iconCode] ?? 'img/weather/default.png';
}

/**
 * Récupère des actualités sur l'environnement et la météo via NewsAPI
 * @return array Tableau contenant les articles ou un message d'erreur
 */
function getEnvironmentalNews()
{
    $apiKey = '849d0dc76d454e15b98772827d48556a'; // Remplace par ta clé API NewsAPI
    $cacheFile = 'cache/news/news_cache.json';
    $cacheDuration = 3600; // 1 heure

    // Vérifier le cache
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheDuration) {
        $cachedData = json_decode(file_get_contents($cacheFile), true);
        if ($cachedData && !empty($cachedData['articles'])) {
            return $cachedData;
        }
    }

    // Paramètres de l'API
    $params = [
        'q' => 'environnement météo climat',
        'language' => 'fr',
        'sortBy' => 'publishedAt',
        'apiKey' => $apiKey
    ];
    $url = 'https://newsapi.org/v2/everything?' . http_build_query($params);

    // Requête cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ClimaTech/1.0');
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        return ['error' => 'Échec cURL : ' . ($curlError ?: 'Code HTTP ' . $httpCode)];
    }

    $data = json_decode($response, true);
    if ($data['status'] !== 'ok' || empty($data['articles'])) {
        return ['error' => 'Aucune actualité trouvée. Veuillez réessayer plus tard.'];
    }

    // Limiter à 5 articles
    $articles = array_slice($data['articles'], 0, 5);
    $result = [
        'articles' => array_map(function ($article) {
            return [
                'title' => htmlspecialchars($article['title'] ?? 'Sans titre'),
                'description' => htmlspecialchars($article['description'] ?? 'Aucune description disponible.'),
                'url' => htmlspecialchars($article['url'] ?? '#'),
                'source' => htmlspecialchars($article['source']['name'] ?? 'Source inconnue'),
                'publishedAt' => date('d/m/Y H:i', strtotime($article['publishedAt'] ?? 'now')),
                'image' => htmlspecialchars($article['urlToImage'] ?? '') // Ajout de l'image
            ];
        }, $articles)
    ];

    // Sauvegarder dans le cache
    file_put_contents($cacheFile, json_encode($result));
    return $result;
}

/**
 * Récupère les données météo pour une ville via OpenWeatherMap
 * @param string $ville Nom de la ville
 * @param string $apiKey Clé API OpenWeatherMap
 * @return array|null Données météo ou null si échec
 */
function getMeteo($ville, $apiKey = "07cdca46f2c356ffb34c6b0f3e240cb5")
{
    $ville = urlencode($ville);

    $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=$ville&appid=$apiKey&units=metric&lang=fr";
    $weatherJson = file_get_contents($weatherUrl);
    if (!$weatherJson) return null;

    $weatherData = json_decode($weatherJson, true);
    if (!isset($weatherData['main']['temp'])) return null;

    $forecastUrl = "https://api.openweathermap.org/data/2.5/forecast?q=$ville&appid=$apiKey&units=metric&lang=fr";
    $forecastJson = file_get_contents($forecastUrl);
    if (!$forecastJson) return null;

    $forecastData = json_decode($forecastJson, true);
    if (!isset($forecastData['list'])) return null;

    $meteo = [
        'temperature' => round($weatherData['main']['temp']),
        'feels_like'  => round($weatherData['main']['feels_like']),
        'description' => ucfirst($weatherData['weather'][0]['description']),
        'wind_speed'  => round($weatherData['wind']['speed'] * 3.6),
        'humidity'    => $weatherData['main']['humidity'] ?? 'N/A',
        'pressure'    => $weatherData['main']['pressure'] ?? 'N/A',
        'icon'        => getWeatherIcon($weatherData['weather'][0]['icon'])
    ];

    $meteo['demain'] = [
        'description' => "Indisponible",
        'temperature' => "N/A",
        'feels_like'  => "N/A",
        'wind_speed'  => "N/A",
        'humidity'    => "N/A",
        'pressure'    => "N/A",
        'icon'        => getWeatherIcon('01d') // Par défaut
    ];
    foreach ($forecastData['list'] as $entry) {
        if (date('Y-m-d', $entry['dt']) === date('Y-m-d', strtotime('+1 day'))) {
            $meteo['demain'] = [
                'description' => ucfirst($entry['weather'][0]['description']),
                'temperature' => round($entry['main']['temp']) . " °C",
                'feels_like'  => round($entry['main']['feels_like']),
                'wind_speed'  => round($entry['wind']['speed'] * 3.6),
                'humidity'    => $entry['main']['humidity'] ?? 'N/A',
                'pressure'    => $entry['main']['pressure'] ?? 'N/A',
                'icon'        => getWeatherIcon($entry['weather'][0]['icon'])
            ];
            break;
        }
    }

    $meteo['semaine'] = [];
    $days = [];
    $joursFr = [
        'Mon' => 'Lun.',
        'Tue' => 'Mar.',
        'Wed' => 'Mer.',
        'Thu' => 'Jeu.',
        'Fri' => 'Ven.',
        'Sat' => 'Sam.',
        'Sun' => 'Dim.'
    ];
    foreach ($forecastData['list'] as $entry) {
        $dateEn = date('D d', $entry['dt']);
        $jourEn = substr($dateEn, 0, 3);
        $numero = substr($dateEn, 4);
        $date = $joursFr[$jourEn] . " " . $numero;
        if (!isset($days[$date])) {
            $days[$date] = true;
            $meteo['semaine'][] = [
                'date' => ucfirst($date),
                'description' => ucfirst($entry['weather'][0]['description']),
                'temperature' => round($entry['main']['temp']) . " °C",
                'feels_like' => round($entry['main']['feels_like']),
                'wind_speed' => round($entry['wind']['speed'] * 3.6),
                'humidity' => $entry['main']['humidity'] ?? 'N/A',
                'pressure' => $entry['main']['pressure'] ?? 'N/A',
                'icon' => getWeatherIcon2($entry['weather'][0]['icon'])
            ];
        }
    }

    return $meteo;
}

function getGeoDataByCity($city)
{
    $apiKey = '07cdca46f2c356ffb34c6b0f3e240cb5';
    $url = "http://api.openweathermap.org/geo/1.0/direct?q=" . urlencode($city) . "&limit=1&appid=" . $apiKey;
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data && !empty($data)) {
        return [
            'lat' => $data[0]['lat'],
            'lon' => $data[0]['lon']
        ];
    }
    return null;
}

/**
 * Récupère une alerte météo ou la météo actuelle pour la position de l'internaute avec OpenWeatherMap.
 * Inclut humidité, ressenti, lever/coucher du soleil et icônes améliorées.
 * @param float $latitude Latitude de la position
 * @param float $longitude Longitude de la position
 * @param string $apiKey Clé API OpenWeatherMap (par défaut fournie)
 * @return array Données météo ou erreur
 */
function getWeatherAlerte($latitude, $longitude, $apiKey = '07cdca46f2c356ffb34c6b0f3e240cb5')
{
    $url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$apiKey}&units=metric&lang=fr";
    $response = @file_get_contents($url);
    if ($response === false) {
        return [
            'type' => 'error',
            'message' => '⚠️ Impossible de récupérer les données météo.',
            'details' => '',
            'icon' => '',
            'condition' => '',
            'temp' => 'N/A',
            'wind' => 'N/A',
            'humidity' => 'N/A',
            'feels_like' => 'N/A',
            'sunrise' => 'N/A',
            'sunset' => 'N/A'
        ];
    }

    $data = json_decode($response, true);
    if (!isset($data['weather']) || !isset($data['main'])) {
        return [
            'type' => 'error',
            'message' => '⚠️ Données météo invalides.',
            'details' => '',
            'icon' => '',
            'condition' => '',
            'temp' => 'N/A',
            'wind' => 'N/A',
            'humidity' => 'N/A',
            'feels_like' => 'N/A',
            'sunrise' => 'N/A',
            'sunset' => 'N/A'
        ];
    }

    // Extraction des données
    $condition = ucfirst($data['weather'][0]['description']);
    $location = $data['name'] ?? 'Position inconnue';
    $temp = round($data['main']['temp']);
    $wind = round($data['wind']['speed'] * 3.6); // Conversion m/s en km/h
    $humidity = $data['main']['humidity']; // Taux d'humidité en %
    $feelsLike = round($data['main']['feels_like']); // Température ressentie en °C
    $sunrise = date('H:i', $data['sys']['sunrise']); // Lever du soleil
    $sunset = date('H:i', $data['sys']['sunset']); // Coucher du soleil
    $iconCode = $data['weather'][0]['icon'];
    $iconUrl = "https://openweathermap.org/img/wn/{$iconCode}@2x.png"; // Icône améliorée

    // Détection d'une alerte (vent fort ou pluie intense)
    $rain = $data['rain']['1h'] ?? 0; // Pluie sur la dernière heure
    if ($wind > 50 || $rain > 10) {
        return [
            'type' => 'alert',
            'message' => "Alerte : {$condition} à {$location}",
            'details' => "Vent : {$wind} km/h, Humidité : {$humidity}%, Ressenti : {$feelsLike}°C" .
                ($rain > 0 ? ", Pluie : {$rain} mm/h" : ""),
            'icon' => $iconUrl,
            'condition' => $condition,
            'temp' => $temp,
            'wind' => $wind,
            'humidity' => $humidity,
            'feels_like' => $feelsLike,
            'sunrise' => $sunrise,
            'sunset' => $sunset
        ];
    }

    // Météo normale
    return [
        'type' => 'normal',
        'message' => "{$condition} à {$location} : {$temp} °C",
        'details' => "Vent : {$wind} km/h, Humidité : {$humidity}%, Ressenti : {$feelsLike}°C",
        'icon' => $iconUrl,
        'condition' => $condition,
        'temp' => $temp,
        'wind' => $wind,
        'humidity' => $humidity,
        'feels_like' => $feelsLike,
        'sunrise' => $sunrise,
        'sunset' => $sunset
    ];
}

function getWeatherIcon($condition)
{
    // Normaliser la condition : minuscules, suppression des espaces superflus
    $condition = strtolower(trim($condition));

    // Log pour débogage (vérifiez les logs PHP, ex. /var/log/php_errors.log)
    error_log("Condition testée : '$condition'");

    // Mappage des conditions météo aux icônes
    $iconMap = [
        'ciel dégagé' => 'img/icons/sun.svg',
        'clear sky' => 'img/icons/sun.svg',
        'peu nuageux' => 'img/icons/cloud-sun.svg',
        'few clouds' => 'img/icons/cloud-sun.svg',
        'nuageux' => 'img/icons/cloudy.svg',
        'overcast clouds' => 'img/icons/cloudy.svg',
        'ensoleillé' => 'img/icons/sun.svg',
        'pluie éparse à proximité' => 'img/icons/rain.svg', // Corrigé
        'pluie' => 'img/icons/rain.svg',
        'rain' => 'img/icons/rain.svg',
        'pluie légère' => 'img/icons/rain.svg',
        'light rain' => 'img/icons/rain.svg',
        'orage' => 'img/icons/storm.svg',
        'thunderstorm' => 'img/icons/storm.svg',
        'neige' => 'img/icons/snowd.svg',
        'snow' => 'img/icons/snowd.svg',
        'brouillard' => 'img/icons/fog.svg',
        'fog' => 'img/icons/fog.svg',
        'indisponible' => 'img/icons/sun.svg'
    ];

    // Ajouter une logique flexible pour les termes similaires
    if (strpos($condition, 'dégagé') !== false || strpos($condition, 'clear') !== false) {
        return 'img/icons/sun.svg';
    }
    if (strpos($condition, 'nuage') !== false || strpos($condition, 'cloud') !== false) {
        return 'img/icons/cloud-sun.svg';
    }
    if (strpos($condition, 'pluie') !== false || strpos($condition, 'rain') !== false) {
        return 'img/icons/rain.svg';
    }
    if (strpos($condition, 'orage') !== false || strpos($condition, 'thunder') !== false) {
        return 'img/icons/storm.svg';
    }
    if (strpos($condition, 'neige') !== false || strpos($condition, 'snow') !== false) {
        return 'img/icons/snowd.svg';
    }
    if (strpos($condition, 'brouillard') !== false || strpos($condition, 'fog') !== false) {
        return 'img/icons/fog.svg';
    }

    // Retourner l’icône correspondante ou par défaut
    return $iconMap[$condition] ?? 'img/icons/sun.svg';
}

function getGeoIpInfoJSON($ip = null, $token = "b95e1aafd66f5a")
{
    if ($ip === null) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Chemin du fichier de cache
    $cacheDir = __DIR__ . '/../cache/ip2/';
    $cacheFile = $cacheDir . 'geoip_cache.json';
    $cacheTTL = 24 * 60 * 60; // Durée de vie du cache : 24 heures (en secondes)

    // Créer le dossier cache s'il n'existe pas
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    // Charger le cache existant
    $cacheData = [];
    if (file_exists($cacheFile)) {
        $cacheData = json_decode(file_get_contents($cacheFile), true);
        if (!is_array($cacheData)) {
            $cacheData = []; // Réinitialiser si le fichier est corrompu
        }
    }

    // Vérifier si l'IP est dans le cache et encore valide
    if (isset($cacheData[$ip]) && $cacheData[$ip]['timestamp'] + $cacheTTL > time()) {
        return $cacheData[$ip]['data'];
    }

    // Si pas dans le cache ou expiré, appeler l'API
    $url = "https://ipinfo.io/$ip/geo?token=$token";
    $json = file_get_contents($url);
    if (!$json) {
        // Si l'API échoue, retourner les données en cache si elles existent, même expirées
        if (isset($cacheData[$ip])) {
            return $cacheData[$ip]['data'];
        }
        return null;
    }

    $data = json_decode($json, true);
    $result = [
        'ip' => $ip,
        'ville' => $data['city'] ?? 'inconnue',
        'region' => $data['region'] ?? 'inconnue',
        'pays' => $data['country'] ?? 'inconnu',
        'loc' => $data['loc'] ?? 'n/a'
    ];

    // Mettre à jour le cache
    $cacheData[$ip] = [
        'timestamp' => time(),
        'data' => $result
    ];
    file_put_contents($cacheFile, json_encode($cacheData, JSON_PRETTY_PRINT));

    return $result;
}

function getGeoIpInfoXML($apiKey = "06b36d3aaed7fec84de2d0de4804b09b", $cacheDuration = 6 * 3600)
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $hash = md5($ip);
    $cacheDir = __DIR__ . '/../cache/ip/';
    $cacheFile = $cacheDir . $hash . '.xml';

    // Créer le dossier de cache s'il n'existe pas
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0777, true);
    }

    // Si le cache est valide, charger le XML depuis le fichier
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheDuration) {
        $xml = simplexml_load_file($cacheFile);
    } else {
        $url = "https://api.whatismyip.com/ip-address-lookup.php?key=$apiKey&input=$ip&output=xml";
        $xml = @simplexml_load_file($url);

        // Sauvegarder dans le cache si le XML est valide
        if ($xml && strlen($xml->asXML()) > 10) {
            file_put_contents($cacheFile, $xml->asXML());
        } else {
            // Si l'appel échoue et un ancien cache existe, on l'utilise quand même
            if (file_exists($cacheFile)) {
                $xml = simplexml_load_file($cacheFile);
            } else {
                return null;
            }
        }
    }

    // Helper pour sécuriser les valeurs
    $get = fn($field, $fallback) => !empty($xml->$field) ? (string)$xml->$field : $fallback;

    return [
        'ip'        => $ip,
        'ville'     => $get('city', 'inconnue'),
        'region'    => $get('region', 'inconnue'),
        'pays'      => $get('country', 'inconnu'),
        'latitude'  => $get('latitude', 'n/a'),
        'longitude' => $get('longitude', 'n/a')
    ];
}

/*les hits*/
function getAndIncrementHits()
{
    $hitsFile = __DIR__ . '/../data/hits.txt';
    $hits = 0;

    // Lire le nombre actuel de hits
    if (file_exists($hitsFile)) {
        $hits = (int) file_get_contents($hitsFile);
    }

    // Incrémenter et sauvegarder
    $hits++;
    file_put_contents($hitsFile, $hits);

    return $hits;
}

// Enregistrer une ville consultée dans un fichier CSV
function logCitySearch($city)
{
    $csvFile = __DIR__ . '/data/cities.csv';
    $timestamp = date('Y-m-d H:i:s');
    $data = [$timestamp, $city];

    // Créer le dossier "data" s'il n'existe pas
    if (!file_exists(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0755, true);
    }

    // Ajouter au fichier CSV
    $file = fopen($csvFile, 'a');
    if ($file) {
        fputcsv($file, $data);
        fclose($file);
    }
}

// Lire les villes consultées depuis le CSV et compter les occurrences
function getCityStats()
{
    $csvFile = __DIR__ . '/data/cities.csv';
    $stats = [];

    if (file_exists($csvFile)) {
        $file = fopen($csvFile, 'r');
        while (($row = fgetcsv($file)) !== false) {
            $city = trim($row[1]); // La ville est en 2e colonne
            $stats[$city] = ($stats[$city] ?? 0) + 1;
        }
        fclose($file);
    }

    arsort($stats); // Trier par ordre décroissant
    return $stats;
}

function setConditionalCookie($name, $value, $expire, $path)
{
    if (!isset($_COOKIE['cookie_consent']) || $_COOKIE['cookie_consent'] === 'accepted') {
        setcookie($name, $value, $expire, $path);
    }
}
// Fonction pour vérifier si la bannière de consentement doit être affichée
function shouldShowCookieConsent()
{
    return !isset($_COOKIE['cookie_consent']);
}

// Fonction pour supprimer les cookies non essentiels
function removeNonEssentialCookies()
{
    setcookie('last_city', '', time() - 3600, '/');
    setcookie('theme', '', time() - 3600, '/');
}
// Obtenir le thème courant
function getCurrentTheme(): string
{
    $defaultTheme = 'standard';
    if (isset($_GET['style']) && in_array($_GET['style'], ['standard', 'night'])) {
        $theme = $_GET['style'];
        setcookie('theme', $theme, time() + (30 * 24 * 60 * 60),'/');
        return $theme;
    }
    if (isset($_COOKIE['theme'])) {
        if (in_array($_COOKIE['theme'], ['standard', 'night'])) {
            return $_COOKIE['theme'];
        } else {
            setcookie('theme', '', time() - 3600,'/');
        }
    }
    date_default_timezone_set('Europe/Paris');
    $currentHour = (int)date('H');
    $isDay = ($currentHour >= 6 && $currentHour < 18);
    return $isDay ? 'standard' : 'night';
}
