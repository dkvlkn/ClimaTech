<?php
/**
 * Page des statistiques de consultation des villes.
 *
 * Cette page affiche les 7 villes les plus consultées sur le site Climatech
 * sous forme de graphique, de liste et de tableau détaillé.
 * Elle prend en compte le thème jour/nuit et inclut une bannière de consentement aux cookies.
 *
 * PHP version 8+
 *
 * @category WebApp
 * @package  Climatech\Stats
 * @author   VotreNom
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://climatech.example.com/stats.php
 */

// Sécurité : empêcher l'accès direct
if (!defined('PHP_EOL')) {
    die('Accès direct interdit');
}

// Activer le buffering de sortie
ob_start();

// Inclure les fonctions utilitaires
if (!file_exists('include/functions.inc.php')) {
    die('Erreur : Fichier functions.inc.php manquant.');
}
require_once 'include/functions.inc.php';

// Définir le titre de la page
$pageTitle = 'Statistiques de Consultation | Climatech';

/**
 * Récupération et filtrage des statistiques.
 *
 * @var array<string, int> $cityStats      Toutes les statistiques brutes
 * @var array<string, int> $filteredStats  Statistiques filtrées (>=7 consultations, max 7)
 */
$cityStats = getCityStats();
$filteredStats = array_filter($cityStats, fn($count) => $count >= 7);
arsort($filteredStats);
$filteredStats = array_slice($filteredStats, 0, 7, true);

// Détection du mode nuit via getCurrentTheme()
$currentTheme = getCurrentTheme();
$isNightMode = $currentTheme === 'night';

// Vérifier l'existence du fichier d'en-tête
if (!file_exists('include/header.inc.php')) {
    die('Erreur : Fichier header.inc.php manquant.');
}
include 'include/header.inc.php';

// Définir le Content-Type
header('Content-Type: text/html; charset=UTF-8');
?>

<!-- Liens CSS externes et scripts -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" />
<script defer="defer" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer="defer" src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<main class="container">
    <section class="stats-block" aria-labelledby="stats-title">
        <h2 id="stats-title">Statistiques des 7 villes les plus consultées</h2>
        <?php if (empty($filteredStats)): ?>
            <p class="no-data">Aucune ville n'a été consultée 7 fois ou plus.</p>
        <?php else: ?>
            <div class="chart-container">
                <canvas id="cityChart"></canvas>
            </div>
            <div class="stats-list-container">
                <h3>Top des villes consultées</h3>
                <ul class="stats-list">
                    <?php 
                    $colors = ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6', '#1abc9c', '#e67e22'];
                    $i = 0;
                    foreach ($filteredStats as $city => $count):
                        $bulletColor = $colors[$i % count($colors)];
                        $i++;
                    ?>
                        <li class="stats-item" data-city="<?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?>" data-count="<?php echo htmlspecialchars($count, ENT_QUOTES, 'UTF-8'); ?>" style="--bullet-color: <?php echo htmlspecialchars($bulletColor, ENT_QUOTES, 'UTF-8'); ?>;">
                            <?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?> : <span class="count"><?php echo htmlspecialchars($count, ENT_QUOTES, 'UTF-8'); ?></span> consultations
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="stats-table-container">
                <h3>Statistiques détaillées</h3>
                <table id="stats-table" class="stats-table">
                    <thead>
                        <tr>
                            <th>Ville</th>
                            <th>Consultations</th>
                            <th>Pourcentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalConsultations = array_sum($filteredStats);
                        foreach ($filteredStats as $city => $count):
                            $percentage = $totalConsultations > 0 ? round(($count / $totalConsultations) * 100, 1) : 0;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($count, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($percentage, ENT_QUOTES, 'UTF-8'); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Initialiser le tableau triable
                    new Sortable(document.getElementById('stats-table').getElementsByTagName('tbody')[0], {
                        animation: 150,
                        sort: true
                    });

                    // Debug thème/cookies
                    console.log('isNightMode (PHP):', <?php echo json_encode($isNightMode); ?>);
                    console.log('Cookie theme:', '<?php echo isset($_COOKIE['theme']) ? htmlspecialchars($_COOKIE['theme'], ENT_QUOTES, 'UTF-8') : 'non défini'; ?>');
                    console.log('Cookie consent:', '<?php echo isset($_COOKIE['cookie_consent']) ? htmlspecialchars($_COOKIE['cookie_consent'], ENT_QUOTES, 'UTF-8') : 'non défini'; ?>');

                    const ctx = document.getElementById('cityChart').getContext('2d');
                    const isNightMode = <?php echo json_encode($isNightMode); ?>;
                    const cities = <?php echo json_encode(array_keys($filteredStats), JSON_HEX_QUOT | JSON_HEX_APOS); ?>;
                    const counts = <?php echo json_encode(array_values($filteredStats)); ?>;

                    const chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: cities,
                            datasets: [{
                                label: 'Nombre de consultations',
                                data: counts,
                                backgroundColor: isNightMode ? 'rgba(46, 204, 113, 0.6)' : 'rgba(52, 152, 219, 0.6)',
                                borderColor: isNightMode ? '#2ecc71' : '#3498db',
                                borderWidth: 1,
                                hoverBackgroundColor: isNightMode ? 'rgba(46, 204, 113, 0.8)' : 'rgba(52, 152, 219, 0.8)',
                                hoverBorderColor: isNightMode ? '#27ae60' : '#2980b9'
                            }]
                        },
                        options: {
                            animation: {
                                duration: 1000,
                                easing: 'easeOutQuart'
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Nombre de consultations',
                                        color: isNightMode ? '#ecf0f1' : '#2c3e50',
                                        font: { size: 14, family: 'Roboto' }
                                    },
                                    ticks: {
                                        color: isNightMode ? '#ecf0f1' : '#2c3e50',
                                        stepSize: Math.ceil(Math.max(...counts, 7) / 5)
                                    },
                                    grid: {
                                        color: isNightMode ? 'rgba(236, 240, 241, 0.2)' : 'rgba(44, 62, 80, 0.1)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Villes',
                                        color: isNightMode ? '#ecf0f1' : '#2c3e50',
                                        font: { size: 14, family: 'Roboto' }
                                    },
                                    ticks: {
                                        color: isNightMode ? '#ecf0f1' : '#2c3e50'
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    labels: {
                                        color: isNightMode ? '#ecf0f1' : '#2c3e50',
                                        font: { size: 12, family: 'Roboto' }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: isNightMode ? 'rgba(44, 62, 80, 0.95)' : 'rgba(236, 240, 241, 0.95)',
                                    titleColor: isNightMode ? '#ecf0f1' : '#2c3e50',
                                    bodyColor: isNightMode ? '#ecf0f1' : '#2c3e50',
                                    borderColor: isNightMode ? '#2ecc71' : '#3498db',
                                    borderWidth: 1,
                                    cornerRadius: 6
                                }
                            },
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });

                    // Animation liste
                    const statsItems = document.querySelectorAll('.stats-item');
                    statsItems.forEach((item, index) => {
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                            item.style.opacity = '1';
                            item.style.transform = 'translateY(0)';
                        }, index * 100);
                    });
                });
            </script>
        <?php endif; ?>

        <!-- Bannière de consentement aux cookies -->
        <div id="cookie-consent" class="cookie-consent<?php echo shouldShowCookieConsent() ? '' : ' hidden'; ?>" role="dialog" aria-label="Consentement aux cookies">
            <p>Nous utilisons des cookies pour améliorer votre expérience et analyser notre trafic. Acceptez-vous leur utilisation ?</p>
            <div class="cookie-buttons">
                <button id="accept-cookies">Accepter</button>
                <button id="decline-cookies">Refuser</button>
            </div>
        </div>
    </section>
</main>

<!-- Script pour la gestion des cookies -->
<script defer="defer" src="js/cookie-consent.js"></script>

<?php
// Inclure le pied de page
if (!file_exists('include/footer.inc.php')) {
    die('Erreur : Fichier footer.inc.php manquant.');
}
include 'include/footer.inc.php';

// Vider le buffer de sortie
ob_end_flush();
?>
