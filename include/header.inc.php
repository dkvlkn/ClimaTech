<?php
require_once 'include/functions.inc.php';
$pageTitle = isset($pageTitle) ? $pageTitle : 'Climatech - Prévisions Météo & Climat';

$currentTheme = getCurrentTheme();
$nextTheme = ($currentTheme === 'night') ? 'standard' : 'night';
$pageName = basename($_SERVER['PHP_SELF']);
$isIndexPage = ($pageName === 'index.php');
$hits = getAndIncrementHits();
$backgroundImage = getBackgroundImage();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="css/<?= $currentTheme === 'night' ? 'style_night.css' : 'style.css' ?>" id="theme-style">
    <link rel="icon" type="image/png" href="img/favicon.png">
    <style>
        body {
            background: url("<?= htmlspecialchars($backgroundImage) ?>") no-repeat center center fixed;
            background-size: cover;
        }
    </style>
    <script defer src="js/header.js"></script>
</head>
<body class="<?= ($currentTheme === 'night' ? 'dark-mode' : 'light-mode') . ($isIndexPage ? ' index-page' : '') ?>">
    <a id="top" href="#content" class="skip-link">Aller au contenu</a>
    <header>
        <div class="header-container">
            <a href="index.php?style=<?= htmlspecialchars($currentTheme) ?>" style="text-decoration: none;">
                <div class="header-left">
                    <img src="img/<?= $currentTheme === 'night' ? 'logo_night.png' : 'logo.png' ?>" alt="Logo Climatech" class="site-logo" width="50" height="50" loading="lazy">
                    <h1>Climatech</h1>
                </div>
            </a>
            <div class="header-right">
                <nav aria-label="Navigation principale">
                    <a href="index.php?style=<?= htmlspecialchars($currentTheme) ?>">Accueil</a>
                    <a href="meteo.php?style=<?= htmlspecialchars($currentTheme) ?>">Prévisions</a>
                    <a href="tech.php?style=<?= htmlspecialchars($currentTheme) ?>">Tech</a>
                    <a href="stats.php?style=<?= htmlspecialchars($currentTheme) ?>">Statistiques</a>
                    <a href="contact.php?style=<?= htmlspecialchars($currentTheme) ?>">Contact</a>
                </nav>
                <div class="theme-toggle-container">
                    <a href="?style=<?= htmlspecialchars($nextTheme) ?>" title="Basculer en mode <?= $nextTheme === 'night' ? 'nuit' : 'jour' ?>" aria-label="Basculer en mode <?= $nextTheme === 'night' ? 'nuit' : 'jour' ?>">
                        <img src="img/<?= $nextTheme === 'night' ? 'moon' : 'sun' ?>.svg" alt="Basculer en mode <?= $nextTheme === 'night' ? 'nuit' : 'jour' ?>" width="24" height="24" loading="lazy">
                    </a>
                </div>
            </div>
        </div>
    </header>