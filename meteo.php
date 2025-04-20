<?php
require_once "include/functions.inc.php";
$pageTitle = 'Prévisions Météo | Climatech';
include "include/header.inc.php";
?>
<link rel="stylesheet" href="css/meteo.css">
<h2 class="map-title">Cliquez sur une région</h2>
<div class="map-container">
    <figure>
        <img src="img/CarteDeFrance.jpeg" usemap="#image-map" alt="Carte des régions de France"/>
    </figure>
</div>
<map name="image-map">
    <area alt="Auvergne-Rhône-Alpes" title="Auvergne-Rhône-Alpes" href="./regions.php?region=Auvergne-Rh%C3%B4ne-Alpes&amp;style=normal&amp;lang=fr" coords="467,397,458,425,406,420,381,386,321,396,329,365,343,344,332,295,352,283,377,283,402,311,439,296,471,309,508,294,524,361" shape="poly" />
    <area alt="Hauts-de-France" title="Hauts-de-France" href="./regions.php?region=Hauts-de-France&amp;style=normal&amp;lang=fr" coords="307,29,340,29,404,82,385,141,369,147,309,132,294,82" shape="poly" />
    <area alt="Provence-Alpes-Côte d'Azur" title="Provence-Alpes-Côte d'Azur" href="./regions.php?region=Provence-Alpes-C%C3%B4te+d%27Azur&amp;style=normal&amp;lang=fr" coords="505,484,480,491,430,473,424,422,459,428,468,399,488,389,501,375,516,393,517,424,546,433" shape="poly" />
    <area alt="Grand Est" title="Grand Est" href="./regions.php?region=Grand+Est&amp;style=normal&amp;lang=fr" coords="446,227,425,204,393,206,377,158,405,99,429,82,434,103,493,122,513,138,547,143,565,156,540,191,544,232,511,217,479,204" shape="poly" />
    <area alt="Occitanie" title="Occitanie" href="./regions.php?region=Occitanie&amp;style=normal&amp;lang=fr" coords="414,458,365,483,339,533,307,519,223,507,242,445,271,436,298,392,336,399,384,390" shape="poly" />
    <area alt="Normandie" title="Normandie" href="./regions.php?region=Normandie&amp;style=normal&amp;lang=fr" coords="161,106,170,138,179,169,273,193,275,164,301,127,302,101,242,105,239,132,186,122" shape="poly" />
    <area alt="Nouvelle-Aquitaine" title="Nouvelle-Aquitaine" href="./regions.php?region=Nouvelle-Aquitaine&amp;style=normal&amp;lang=fr" coords="186,493,179,319,202,296,240,257,299,299,339,319,296,381,229,440,221,509" shape="poly" />
    <area alt="Centre-Val de Loire" title="Centre-Val de Loire" href="./regions.php?region=Centre-Val+de+Loire&amp;style=normal&amp;lang=fr" coords="339,286,308,294,267,272,238,247,265,211,276,189,272,168,288,164,314,180,351,199,357,268" shape="poly" />
    <area alt="Bourgogne-Franche-Comté" title="Bourgogne-Franche-Comté" href="./regions.php?region=Bourgogne-Franche-Comt%C3%A9&amp;style=normal&amp;lang=fr" coords="436,291,406,312,385,282,354,271,367,183,400,206,428,207,456,225,479,208,514,213,527,240,502,275,467,303,450,299" shape="poly" />
    <area alt="Bretagne" title="Bretagne" href="./regions.php?region=Bretagne&amp;style=normal&amp;lang=fr" coords="41,179,49,202,61,210,127,232,183,214,191,179,170,169,130,172,105,158,86,164,58,173" shape="poly" />
    <area alt="Corse" title="Corse" href="./regions.php?region=Corse&amp;style=normal&amp;lang=fr" coords="611,499,622,521,624,545,607,592,594,579,576,540" shape="poly" />
    <area alt="Pays de la Loire" title="Pays de la Loire" href="./regions.php?region=Pays+de+la+Loire&amp;style=normal&amp;lang=fr" coords="142,252,159,294,200,303,226,256,267,202,251,186,229,182,213,177,198,188,180,221,147,235" shape="poly" />
    <area alt="Île-de-France" title="Île-de-France" href="./regions.php?region=%C3%8Ele-de-France&amp;style=normal&amp;lang=fr" coords="339,143,361,143,371,154,372,179,356,196,318,182,291,148,310,136" shape="poly" />
    <area alt="Guadeloupe" title="Guadeloupe" href="./regions.php?region=Guadeloupe&amp;style=normal&amp;lang=fr" coords="360,555,424,611" shape="rect" />
    <area alt="Martinique" title="Martinique" href="./regions.php?region=Martinique&amp;style=normal&amp;lang=fr" coords="490,568,525,610" shape="rect" />
    <area alt="Guyane" title="Guyane" href="./regions.php?region=Guyane&amp;style=normal&amp;lang=fr" coords="102,432,158,475,168,499,107,600,13,602,40,543,23,501,16,461,24,442,45,414" shape="poly" />
    <area alt="La Réunion" title="La Réunion" href="./regions.php?region=La+R%C3%A9union&amp;style=normal&amp;lang=fr" coords="432,565,482,612" shape="rect" />
    <area alt="Mayotte" title="Mayotte" href="./regions.php?region=Mayotte&amp;style=normal&amp;lang=fr" coords="531,584,554,611" shape="rect" />
</map>
<h2 id="form-title" class="form-title">Sélection</h2>
<div id="form-container" class="form-container">
    <label for="region" class="form-label">Région :</label>
    <select id="region" class="form-select">
        <option value="">-- Choisir une région --</option>
    </select>
    <label for="departement" class="form-label">Département :</label>
    <select id="departement" class="form-select" disabled="disabled">
        <option value="">-- Choisir un département --</option>
    </select>
    <label for="ville" class="form-label">Ville :</label>
    <select id="ville" class="form-select" disabled="disabled">
        <option value="">-- Choisir une ville --</option>
    </select>
    <button id="rechercher" class="form-button" disabled="disabled">Rechercher</button>
</div>
<div id="resultat-meteo" class="weather-container"></div>

<div id="cookie-consent" class="cookie-consent" style="display: <?= shouldShowCookieConsent() ? 'block' : 'none' ?>;">
    <p>Nous utilisons des cookies pour améliorer votre expérience et analyser notre trafic. Acceptez-vous leur utilisation ?</p>
    <div class="cookie-buttons">
        <button id="accept-cookies">Accepter</button>
        <button id="decline-cookies">Refuser</button>
    </div>
</div>

<script src="js/meteo.js"></script>

<?php include "include/footer.inc.php"; ?>