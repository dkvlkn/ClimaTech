<?php
require_once "include/functions.inc.php";
include "include/header.inc.php";

// Récupérer les stats et filtrer : min 7 recherches, max 7 villes
$cityStats = getCityStats();
$filteredStats = array_filter($cityStats, fn($count) => $count >= 7);
arsort($filteredStats); // Trier par recherches décroissantes
$filteredStats = array_slice($filteredStats, 0, 7, true); // Limiter à 7 villes

// Détection du mode nuit
$isNightMode = isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark';
$cssFile = $isNightMode ? 'styles/stats-night.css' : 'styles/stats-day.css';
?>

<link rel="stylesheet" href="<?= htmlspecialchars($cssFile) ?>"/>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="stats-container">
    <section class="stats-block">
        <h2>Statistiques des 7 villes les plus consultées</h2>
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
                        <li class="stats-item" data-city="<?= htmlspecialchars($city) ?>" data-count="<?= $count ?>" style="--bullet-color: <?= $bulletColor ?>;">
                        <?= htmlspecialchars($city) ?> : <span class="count"><?= $count ?></span> consultations
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Débogage : afficher l'état du mode nuit
                    console.log('isNightMode (PHP):', <?php echo json_encode($isNightMode); ?>);
                    console.log('Cookie theme:', '<?php echo isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'non défini'; ?>');

                    const ctx = document.getElementById('cityChart').getContext('2d');
                    const isNightMode = <?php echo json_encode($isNightMode); ?>;
                    const cities = <?php echo json_encode(array_keys($filteredStats)); ?>;
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
    </section>
</main>

<?php include "include/footer.inc.php"; ?>