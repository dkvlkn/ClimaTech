<?php

function getRandomImage($dir = "../img/photos/")
{
    if (!is_dir($dir)) return null;

    $files = array_diff(scandir($dir), ['.', '..']);
    $images = array_filter($files, function ($file) use ($dir) {
        return preg_match('/\.(jpg|jpeg|png|gif)$/i', $file) && is_file($dir . $file);
    });

    if (empty($images)) return null;

    $image = $images[array_rand($images)];
    return "img/photos/" . $image;
}

function getApodData($api_key = "dqUiZ2IwqKnOYADVpNhzzfiM9WY4XjdJshceSy62")
{
    $date = date("Y-m-d");
    $cacheDir = __DIR__ . '/../cache/apod/';
    $cacheFile = $cacheDir . "apod-$date.json";

    if (file_exists($cacheFile)) {
        $json = file_get_contents($cacheFile);
        return json_decode($json, true);
    }

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

function getGeoData($ip = null)
{
    if ($ip === null) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    $cacheDir = __DIR__ . '/../cache/ip3/';
    $cacheFile = $cacheDir . 'geo-' . str_replace('.', '_', $ip) . '.xml';

    if (file_exists($cacheFile)) {
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
 * Récupère les données météo pour une ville via WeatherAPI
 * @param string $ville Nom de la ville
 * @param string $apiKey Clé API WeatherAPI (par défaut fournie)
 * @return array|null Données météo ou null si échec
 */
function getMeteo($ville, $apiKey = "3fb47a1a199f40c2879204035252603")
{
    $ville = urlencode($ville);
    $url = "https://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=$ville&days=7&lang=fr";

    $json = file_get_contents($url);
    if (!$json) return null;

    $data = json_decode($json, true);
    if (!isset($data['current']['temp_c'])) return null;

    // 🔹 Météo actuelle
    $temperature = round($data['current']['temp_c']);
    $description = ucfirst($data['current']['condition']['text']);

    // 🔹 Prévision demain (globale)
    $demain = "Indisponible";
    if (isset($data['forecast']['forecastday'][1]['day'])) {
        $desc = ucfirst($data['forecast']['forecastday'][1]['day']['condition']['text']);
        $temp = round($data['forecast']['forecastday'][1]['day']['avgtemp_c']);
        $demain = "$desc, $temp °C";
    }

    // 🔹 Prévisions des 7 prochains jours (max + condition)
    $semaine = [];
    foreach ($data['forecast']['forecastday'] as $day) {
        $date = $day['date'];
        $condition = ucfirst($day['day']['condition']['text']);
        $min = isset($day['day']['mintemp_c']) ? round($day['day']['mintemp_c']) : '?';
        $max = isset($day['day']['maxtemp_c']) ? round($day['day']['maxtemp_c']) : '?';

        $semaine[] = "$date : $condition, $min °C - $max °C";
    }

    return [
        'temperature' => $temperature,
        'description' => $description,
        'demain'      => $demain,
        'semaine'     => $semaine
    ];
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

function getGeoIpInfoXML($apiKey = "06b36d3aaed7fec84de2d0de4804b09b", $cacheDuration = 6 * 3600) {
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