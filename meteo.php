<?php
require_once "include/functions.inc.php";
include "include/header.inc.php";

// Récupération des régions pour le chargement initial
$regions = getRegions();
$selectedRegion = $_GET['region'] ?? null;
$selectedDept = $_GET['departement'] ?? null;
$selectedVille = $_GET['ville'] ?? null;

// Initialisation météo
$meteo = null;

if ($selectedVille) {
    $meteo = getMeteo($selectedVille);
}
?>

<h2>🗺️ Cliquez sur une région de la carte</h2>

<div class="map-container">
    <figure>
        <img src="img/carte_regional.png" usemap="#image-map" alt="Carte des régions de France">
        <figcaption>🗺️ Cliquez sur une région pour commencer</figcaption>
    </figure>
</div>

<!-- 🌍 MAP HTML AVEC <area> -->
<map name="image-map">
    <area target="" alt="Nouvelle-Aquitaine" title="Nouvelle-Aquitaine" href="meteo.php?region=Nouvelle-Aquitaine" coords="206,472,182,460,173,436,180,416,188,345,184,329,191,306,205,300,204,285,199,272,227,263,236,270,245,271,265,301,297,298,308,312,305,324,307,343,297,356,293,365,282,365,273,367,269,376,258,387,257,394,253,398,254,405,246,412,218,420,216,432,224,445,219,460,213,471" shape="poly">
    <area target="" alt="Occitanie" title="Occitanie" href="meteo.php?region=Occitanie" coords="313,495,298,492,294,497,286,489,281,482,274,483,275,476,249,472,245,479,227,480,217,473,221,458,226,446,223,439,220,422,247,414,256,407,257,398,271,377,274,369,294,368,298,383,305,385,316,371,325,379,335,369,341,374,347,375,355,382,359,400,370,402,377,402,383,415,377,428,370,433,363,437,355,437,333,453,325,468,325,479,331,492" shape="poly">
    <area target="" alt="Corse" title="Corse" href="meteo.php?region=Corse" coords="531,543,518,537,515,524,514,515,510,509,512,495,516,488,528,480,534,480,534,467,539,467,541,488,542,505,537,529" shape="poly">
    <area target="" alt="Pays de la Loire" title="Pays de la Loire" href="meteo.php?region=Pays de la Loire" coords="202,297,188,301,168,293,156,274,158,267,153,262,161,255,148,257,143,249,150,247,159,237,177,231,193,220,192,194,197,197,205,196,213,195,219,196,226,200,235,197,238,204,248,209,249,224,234,236,228,251,224,262,211,262,202,266,197,272,201,284" shape="poly">
    <area target="" alt="Provence-Alpes-Côte d'Azur" title="Provence-Alpes-Côte d'Azur" href="meteo.php?region=Provence-Alpes-Côte d'Azur" coords="365,441,373,434,378,431,383,420,386,415,380,402,388,405,391,399,398,405,405,408,412,409,412,403,406,393,413,393,415,384,430,374,435,370,430,365,436,362,446,365,450,372,455,375,456,380,453,395,458,402,467,409,478,408,478,416,474,428,464,434,443,456,425,463,413,460,402,449,386,448,378,448" shape="poly">
    <area target="" alt="Auvergne-Rhône-Alpes" title="Auvergne-Rhône-Alpes" href="meteo.php?region=Auvergne-Rhône-Alpes" coords="410,407,397,402,390,396,386,402,369,398,361,397,356,381,351,376,342,371,334,367,325,375,315,368,304,382,298,379,295,365,309,343,308,332,307,323,310,313,301,299,308,291,325,280,340,287,345,282,356,296,354,307,360,311,367,311,379,305,385,310,390,293,401,298,405,304,419,305,427,300,449,299,458,321,451,326,462,346,460,354,449,361,432,359,427,367,431,371,423,375,413,382,410,389,403,391" shape="poly">
    <area target="" alt="Bretagne" title="Bretagne" href="meteo.php?region=Bretagne" coords="145,245,157,234,190,218,188,209,189,194,175,194,173,188,161,186,147,186,135,192,119,176,106,179,97,186,88,185,67,195,67,200,84,204,75,206,70,215,80,227,91,225,100,230,124,240" shape="poly">
    <area target="" alt="Centre-Val de Loire" title="Centre-Val de Loire" href="meteo.php?region=Centre-Val de Loire" coords="252,224,235,238,228,260,235,267,242,268,248,268,267,298,298,295,316,281,325,278,322,255,320,233,326,225,326,214,310,212,291,203,276,183,274,174,269,180,253,186,257,194,251,208" shape="poly">
    <area target="" alt="Normandie" title="Normandie" href="meteo.php?region=Normandie" coords="250,206,240,202,236,194,226,197,216,192,197,194,189,191,177,192,174,181,170,157,163,145,162,137,183,138,181,146,190,152,216,156,235,149,230,143,234,135,271,117,282,126,282,153,278,162,272,167,272,174,262,179,253,183,250,188,254,194" shape="poly">
    <area target="" alt="Bourgogne-Franche-Comté" title="Bourgogne-Franche-Comté" href="meteo.php?region=Bourgogne-Franche-Comté" coords="384,306,380,301,367,308,357,307,359,296,346,280,339,284,329,280,328,268,324,258,323,243,325,237,322,233,328,226,329,216,326,211,330,202,343,204,350,213,355,221,362,223,379,219,390,227,391,234,401,240,411,234,418,224,428,219,440,225,445,221,461,243,458,251,444,269,436,281,426,297,419,303,407,302,402,296,394,293,388,291" shape="poly">
    <area target="" alt="Île-de-France" title="Île-de-France" href="meteo.php?region=Île-de-France" coords="304,206,291,201,278,183,275,170,281,160,291,161,301,164,328,166,339,177,341,187,338,196,339,202,329,199,324,211,312,210" shape="poly">
    <area target="" alt="Hauts-de-France" title="Hauts-de-France" href="meteo.php?region=Hauts-de-France" coords="306,161,283,156,284,124,273,115,277,81,282,74,309,66,311,78,320,84,329,81,336,94,345,97,347,104,360,104,366,121,365,131,361,138,358,148,349,152,347,157,347,162,340,175,330,163" shape="poly">
    <area target="" alt="Grand Est" title="Grand Est" href="meteo.php?region=Grand Est" coords="428,216,415,222,409,233,400,237,392,232,392,225,381,217,358,219,343,202,340,196,343,189,341,177,349,162,350,155,359,151,362,139,367,131,368,123,378,122,385,113,387,130,405,142,413,142,422,147,438,146,443,150,446,160,468,163,473,160,480,167,497,171,483,189,481,200,477,210,477,216,476,224,477,236,467,247,461,239,454,228,446,217,439,222" shape="poly">
</map>
<h2 id="form-title">📍 Sélectionnez un département et une ville</h2>

<form method="get" action="meteo.php" id="meteo-form">
    <label for="region">Région :</label>
    <select name="region" id="region">
        <option value="">-- Choisir une région --</option>
        <?php foreach ($regions as $region): ?>
            <option value="<?= htmlspecialchars($region) ?>" <?= $region === $selectedRegion ? 'selected' : '' ?>>
                <?= htmlspecialchars($region) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="departement">Département :</label>
    <select name="departement" id="departement">
        <option value="">-- Choisir un département --</option>
        <?php if ($selectedRegion): ?>
            <?php $departements = getDepartements($selectedRegion); ?>
            <?php foreach ($departements as $dept): ?>
                <option value="<?= htmlspecialchars($dept) ?>" <?= $dept === $selectedDept ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dept) ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>

    <label for="ville">Ville :</label>
    <select name="ville" id="ville">
        <option value="">-- Choisir une ville --</option>
        <?php if ($selectedDept): ?>
            <?php $villes = getVilles($selectedDept); ?>
            <?php foreach ($villes as $ville): ?>
                <option value="<?= htmlspecialchars($ville) ?>" <?= $ville === $selectedVille ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ville) ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</form>

<?php if ($meteo): ?>
    <h2>🌤️ Météo à <?= htmlspecialchars($selectedVille) ?></h2>

    <ul>
        <li><strong>Actuellement :</strong> <?= $meteo['temperature'] ?> °C — <?= htmlspecialchars($meteo['description']) ?></li>
        <li><strong>Demain :</strong> <?= htmlspecialchars($meteo['demain']) ?></li>
    </ul>

    <h3>📅 Prévisions à 7 jours :</h3>
    <ul>
        <?php foreach ($meteo['semaine'] as $jour): ?>
            <li>
                <?php
                $parts = explode(" : ", $jour, 2);
                if (count($parts) === 2) {
                    $date = trim($parts[0]);
                    $reste = explode(",", $parts[1], 2);
                    $condition = trim($reste[0] ?? 'Indisponible');
                    $temp = trim($reste[1] ?? '');
                    if (preg_match('/(-?\d+)[^\d]+(-?\d+)/', $temp, $matches)) {
                        $min = $matches[1];
                        $max = $matches[2];
                        echo "<strong>" . htmlspecialchars($date) . "</strong> — " . htmlspecialchars($condition) . "<br>";
                        echo "🔽 Temp. minimale : $min °C | 🔼 Temp. maximale : $max °C";
                    } else {
                        echo htmlspecialchars($jour);
                    }
                } else {
                    echo htmlspecialchars($jour);
                }
                ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<script>
// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', () => {
    const areas = document.querySelectorAll('map area');
    const regionSelect = document.getElementById('region');
    const departementSelect = document.getElementById('departement');
    const villeSelect = document.getElementById('ville');
    const formTitle = document.getElementById('form-title');

    // Gérer les clics sur la carte
    areas.forEach(area => {
        area.addEventListener('click', (e) => {
            e.preventDefault(); // Empêche le rechargement
            const region = area.getAttribute('data-region');
            console.log('Région cliquée :', region); // Log pour debug

            // Mettre à jour le menu région
            regionSelect.value = region;
            regionSelect.dispatchEvent(new Event('change')); // Déclencher l'événement change

            // Faire défiler vers le formulaire
            formTitle.scrollIntoView({ behavior: 'smooth' });
        });

        // Remplacer href par data-region (si nécessaire)
        const region = area.getAttribute('title');
        area.setAttribute('data-region', region);
        area.removeAttribute('href');
    });

    // Gérer le changement de région
    regionSelect.addEventListener('change', () => {
        const region = regionSelect.value;
        console.log('Région sélectionnée :', region); // Log pour debug
        if (region) {
            fetch(`meteo_fetch.php?region=${encodeURIComponent(region)}`)
                .then(response => {
                    if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log('Départements reçus :', data.departements); // Log pour debug
                    if (data.error) {
                        console.error('Erreur serveur :', data.error);
                        return;
                    }
                    // Mettre à jour le menu département
                    departementSelect.innerHTML = '<option value="">-- Choisir un département --</option>';
                    data.departements.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept;
                        option.textContent = dept;
                        departementSelect.appendChild(option);
                    });
                    // Réinitialiser le menu ville
                    villeSelect.innerHTML = '<option value="">-- Choisir une ville --</option>';
                })
                .catch(error => console.error('Erreur lors de la récupération des départements :', error));
        } else {
            departementSelect.innerHTML = '<option value="">-- Choisir un département --</option>';
            villeSelect.innerHTML = '<option value="">-- Choisir une ville --</option>';
        }
    });

    // Gérer le changement de département
    departementSelect.addEventListener('change', () => {
        const dept = departementSelect.value;
        console.log('Département sélectionné :', dept); // Log pour debug
        if (dept) {
            fetch(`meteo_fetch.php?departement=${encodeURIComponent(dept)}`)
                .then(response => {
                    if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log('Villes reçues :', data.villes); // Log pour debug
                    if (data.error) {
                        console.error('Erreur serveur :', data.error);
                        return;
                    }
                    // Mettre à jour le menu ville
                    villeSelect.innerHTML = '<option value="">-- Choisir une ville --</option>';
                    if (data.villes && data.villes.length > 0) {
                        data.villes.forEach(ville => {
                            const option = document.createElement('option');
                            option.value = ville;
                            option.textContent = ville;
                            villeSelect.appendChild(option);
                        });
                    } else {
                        console.warn('Aucune ville reçue pour ce département');
                    }
                })
                .catch(error => console.error('Erreur lors de la récupération des villes :', error));
        } else {
            villeSelect.innerHTML = '<option value="">-- Choisir une ville --</option>';
        }
    });

    // Gérer le changement de ville (soumettre le formulaire pour afficher la météo)
    villeSelect.addEventListener('change', () => {
        const ville = villeSelect.value;
        console.log('Ville sélectionnée :', ville); // Log pour debug
        if (ville) {
            const form = document.getElementById('meteo-form');
            form.submit();
        }
    });
});
</script>

<?php include "include/footer.inc.php"; ?>