<?php
require_once "include/functions.inc.php";
include "include/header.inc.php";

// Image aléatoire pour l'arrière-plan
$image = getRandomImage("img/photos/");
?>

<style>
    body {
        background: url("<?= $image ?>") no-repeat center center fixed;
        background-size: cover;
    }
</style>

<section class="welcome-banner">
    <h1>ClimaTech 🌤️</h1>
    <p>Explorez les prévisions météo de toute la France.</p>
</section>

<section>
    <h2>Bienvenue chez ClimaTech 🌦️</h2>
    <p>Plongez dans l'univers de la météo avec ClimaTech ! Retrouvez les prévisions détaillées pour toutes les villes de France, des statistiques captivantes et des outils techniques pour les passionnés.</p>
    <ul class="page-links">
        <li><a href="meteo.php?style=<?= $style ?>">🔍 Rechercher la météo par ville</a></li>
        <li><a href="stats.php?style=<?= $style ?>">📊 Statistiques de consultation</a></li>
        <li><a href="tech.php?style=<?= $style ?>">⚙️ Page technique (API JSON/XML)</a></li>
    </ul>
</section>

<?php if ($image): ?>
    <aside>
        <figure>
            <img src="<?= $image ?>" alt="Image météo aléatoire">
            <figcaption>Image aléatoire : <?= basename($image) ?></figcaption>
        </figure>
    </aside>
<?php else: ?>
    <p>📷 Aucune image trouvée dans le dossier <code>/img/photos/</code>.</p>
<?php endif; ?>

</main>

<?php include "include/footer.inc.php"; ?>