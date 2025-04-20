<?php
// Sécurité : empêcher l'accès direct
if (!defined('PHP_EOL')) {
    die('Accès direct interdit');
}

// Inclure les fonctions utilitaires
if (!file_exists('include/functions.inc.php')) {
    die('Erreur : Fichier functions.inc.php manquant.');
}
require_once 'include/functions.inc.php';

// Définir le titre par défaut
$pageTitle = isset($pageTitle) ? $pageTitle : 'Climatech - Prévisions Météo &amp; Climat';

// Récupérer les informations du thème et des hits
$currentTheme = getCurrentTheme();
$nextTheme = ($currentTheme === 'night') ? 'standard' : 'night';
$pageName = basename($_SERVER['PHP_SELF']);
$isIndexPage = ($pageName === 'index.php');
$hits = getAndIncrementHits();
$backgroundImage = getBackgroundImage() ?: 'img/photos/degleex-ganzorig-wQImoykAwGs-unsplash-min-min.jpg'; // Image par défaut

// Définir le Content-Type pour HTML
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées auto-fermantes -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <!-- CSS dynamique selon le thème -->
    <link rel="stylesheet" href="css/<?php echo $currentTheme === 'night' ? 'style_night.css' : 'style.css'; ?>" id="theme-style" />
    <link rel="icon" type="image/png" href="img/favicon.png" />
    <!-- Style inline pour l'arrière-plan -->
    <style>
        body {
            background: url("<?php echo htmlspecialchars($backgroundImage, ENT_QUOTES, 'UTF-8'); ?>") no-repeat center center fixed;
            background-size: cover;
        }
    </style>
    <!-- Script avec defer explicite pour XML -->
    <script defer="defer" src="js/header.js"></script>
</head>
<body class="<?php echo htmlspecialchars($currentTheme === 'night' ? 'dark-mode' : 'light-mode', ENT_QUOTES, 'UTF-8') . ($isIndexPage ? ' index-page' : ''); ?>">
    <!-- Lien d'accessibilité -->
    <a id="top" href="#content" class="skip-link">Aller au contenu</a>
    <header>
        <div class="header-container">
            <a href="index.php?style=<?php echo htmlspecialchars($currentTheme, ENT_QUOTES, 'UTF-8'); ?>" style="text-decoration: none;">
                <div class="header-left">
                    <img src="img/<?php echo $currentTheme === 'night' ? 'logo_night.png' : 'logo.png'; ?>" alt="Logo Climatech" class="site-logo" width="50" height="50" loading="lazy" />
                    <h1>Climatech</h1>
                </div>
            </a>
            <div class="header-right">
                <nav aria-label="Navigation principale">
                    <a href="index.php?style=<?php echo htmlspecialchars($currentTheme, ENT_QUOTES, 'UTF-8'); ?>">Accueil</a>
                    <a href="meteo.php?style=<?php echo htmlspecialchars($currentTheme, ENT_QUOTES, 'UTF-8'); ?>">Prévisions</a>
                    <a href="tech.php?style=<?php echo htmlspecialchars($currentTheme, ENT_QUOTES, 'UTF-8'); ?>">Tech</a>
                    <a href="stats.php?style=<?php echo htmlspecialchars($currentTheme, ENT_QUOTES, 'UTF-8'); ?>">Statistiques</a>
                    <a href="contact.php?style=<?php echo htmlspecialchars($currentTheme, ENT_QUOTES, 'UTF-8'); ?>">Contact</a>
                </nav>
                <div class="theme-toggle-container">
                    <a href="?style=<?php echo htmlspecialchars($nextTheme, ENT_QUOTES, 'UTF-8'); ?>" title="Basculer en mode <?php echo htmlspecialchars($nextTheme === 'night' ? 'nuit' : 'jour', ENT_QUOTES, 'UTF-8'); ?>" aria-label="Basculer en mode <?php echo htmlspecialchars($nextTheme === 'night' ? 'nuit' : 'jour', ENT_QUOTES, 'UTF-8'); ?>">
                        <img src="img/<?php echo $nextTheme === 'night' ? 'moon' : 'sun'; ?>.svg" alt="Basculer en mode <?php echo htmlspecialchars($nextTheme === 'night' ? 'nuit' : 'jour', ENT_QUOTES, 'UTF-8'); ?>" width="24" height="24" loading="lazy" />
                    </a>
                </div>
            </div>
        </div>
    </header>
    <!-- Contenu principal commence dans index.php -->