<?php
require_once "include/functions.inc.php";
include "include/header.inc.php";
?>
<main class="container plan-page">
    <section class="plan">
        <h1>📍 Plan du site</h1>
        <p>Voici la structure de Climatech :</p>
        <ul>
            <li><a href="index.php">Accueil</a> - Page d'entrée du site.</li>
            <li><a href="meteo.php">Prévisions</a> - Consultez la météo par région, département et ville.</li>
            <li><a href="tech.php">Tech</a> - Détails techniques sur le projet.</li>
            <li><a href="stats.php">Statistiques</a> - Statistiques de consultation.</li>
            <li><a href="plan.php">Plan du site</a> - Vous êtes ici !</li>
        </ul>
    </section>
</main>

<?php
require_once "include/footer.inc.php";
?>