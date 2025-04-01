<?php
// Récupération du thème
$styleParam = $_GET['style'] ?? null;
date_default_timezone_set('Europe/Paris');
$currentHour = (int)date('H');
$isDay = ($currentHour >= 6 && $currentHour < 18);
$style = $styleParam ?? ($isDay ? 'standard' : 'night');
$style = ($style === 'night') ? 'night' : 'standard';

$nextStyle = ($style === 'night') ? 'standard' : 'night';
$toggleIcon = ($style === 'night') ? '☀️' : '🌙';

$pageName = basename($_SERVER['PHP_SELF']);
$isIndexPage = ($pageName === 'index.php');
// Compteur de visites (exécuté une fois par chargement de page)
$hits = getAndIncrementHits();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Climatech - Prévisions Météo & Climat</title>
    <link rel="stylesheet" href="css/<?= $style === 'night' ? 'style_night.css' : 'style.css' ?>">
    <link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body class="<?= ($style === 'night' ? 'dark-mode' : 'light-mode') . ($isIndexPage ? ' index-page' : '') ?>">
    <a id="top"></a>

    <header>
        <div class="header-container">
            <!-- Gauche : logo + nom -->
            <div class="header-left">
                <img src="img/logo.png" alt="Logo Climatech" class="site-logo">
                <h1>Climatech</h1>
            </div>

            <!-- Droite : nav + toggle -->
            <div class="header-right">
                <nav>
                    <a href="index.php?style=<?= $style ?>">Accueil</a>
                    <a href="meteo.php?style=<?= $style ?>">Prévisions</a>
                    <a href="tech.php?style=<?= $style ?>">Tech</a>
                    <a href="stats.php?style=<?= $style ?>">Statistiques</a>
                </nav>
                <form method="get" action="" class="theme-toggle-form">
                    <input type="hidden" name="style" value="<?= $nextStyle ?>">
                    <button type="submit" class="toggle-theme-btn" title="Changer de thème"><?= $toggleIcon ?></button>
                </form>
            </div>
        </div>
    </header>

    <main>
