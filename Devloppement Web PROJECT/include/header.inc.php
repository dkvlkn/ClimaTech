<?php
// Récupération du thème
$styleParam = $_GET['style'] ?? null;
date_default_timezone_set('Europe/Paris');
$currentHour = (int)date('H');
$isDay = ($currentHour >= 6 && $currentHour < 18);

// Vérifier le cookie existant
$styleCookie = $_COOKIE['theme'] ?? null;
if ($styleCookie && !in_array($styleCookie, ['standard', 'night'])) {
    setcookie('theme', '', time() - 3600, '/'); // Supprimer cookie invalide
    $styleCookie = null;
}

$style = $styleParam ?? $styleCookie; // Priorité : GET > Cookie
if ($style === null) {
    $style = $isDay ? 'standard' : 'night';
}
$style = ($style === 'night') ? 'night' : 'standard';

// Mettre à jour le cookie si un nouveau style est choisi via GET
if ($styleParam && $styleParam !== $styleCookie) {
    setcookie('theme', $style, time() + (30 * 24 * 3600), '/'); // Valide 30 jours
}

$nextStyle = ($style === 'night') ? 'standard' : 'night';
$toggleIcon = ($style === 'night') ? '☀️' : '🌙';

// Suggestion de mode en fonction de l’heure
$suggestedStyle = null;
$suggestionMessage = null;
if ($currentHour >= 20 && $style === 'night') {
    $suggestedStyle = 'standard';
    $suggestionMessage = "Passer au mode jour ?";
} elseif ($currentHour >= 6 && $currentHour < 20 && $style === 'standard') {
    $suggestedStyle = 'night';
    $suggestionMessage = "Passer au mode nuit ?";
}

$pageName = basename($_SERVER['PHP_SELF']);
$isIndexPage = ($pageName === 'index.php');
$hits = getAndIncrementHits();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Climatech - Prévisions Météo & Climat</title>
    <link rel="stylesheet" href="css/<?= $style === 'night' ? 'style_night.css' : 'style.css' ?>" id="theme-style">
    <link rel="icon" type="image/png" href="img/favicon.png">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const themeStyle = document.getElementById('theme-style');
            const logoImg = document.querySelector('.site-logo');
            const header = document.querySelector('header');
            const backToTop = document.querySelector('.back-to-top');

            // Gestion du clic sur le bouton de bascule (plus besoin de localStorage)
            document.querySelector('.toggle-theme-btn').addEventListener('click', function() {
                // Le changement est géré par PHP via le formulaire
            });

            // Gestion du scroll pour le header
            let lastScrollTop = 0;
            window.addEventListener('scroll', function() {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                if (scrollTop > lastScrollTop) {
                    header.classList.add('hidden');
                } else {
                    header.classList.remove('hidden');
                }
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;

                if (scrollTop > 200) {
                    backToTop.classList.add('visible');
                } else {
                    backToTop.classList.remove('visible');
                }
            });
        });
    </script>
</head>

<body class="<?= ($style === 'night' ? 'dark-mode' : 'light-mode') . ($isIndexPage ? ' index-page' : '') ?>">
    <a id="top"></a>

    <header>
        <div class="header-container">
            <div class="header-left">
                <img src="img/<?= $style === 'night' ? 'logo_night.png' : 'logo.png' ?>" alt="Logo Climatech" class="site-logo">
                <h1>Climatech</h1>
            </div>
            <div class="header-right">
                <nav>
                    <a href="index.php?style=<?= $style ?>">Accueil</a>
                    <a href="meteo.php?style=<?= $style ?>">Prévisions</a>
                    <a href="tech.php?style=<?= $style ?>">Tech</a>
                    <a href="stats.php?style=<?= $style ?>">Statistiques</a>
                </nav>
                <div class="theme-toggle-container">
                    <form method="get" action="" class="theme-toggle-form">
                        <input type="hidden" name="style" value="<?= $nextStyle ?>">
                        <button type="submit" class="toggle-theme-btn" title="Changer de thème"><?= $toggleIcon ?></button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main>