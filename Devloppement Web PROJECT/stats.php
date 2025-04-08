<?php
require_once "include/functions.inc.php";
include "include/header.inc.php";

$cityStats = getCityStats();
?>

<main class="container">
    <section class="stats-section">
        <h2>Statistiques des villes consultées</h2>
        <?php if (empty($cityStats)): ?>
            <p class="no-data">Aucune ville consultée pour le moment.</p>
        <?php else: ?>
            <div class="histogram-container">
                <canvas id="cityHistogram" width="900" height="600"></canvas>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const canvas = document.getElementById('cityHistogram');
                    const ctx = canvas.getContext('2d');
                    const cities = <?php echo json_encode(array_keys($cityStats)); ?>;
                    const counts = <?php echo json_encode(array_values($cityStats)); ?>;
                    const maxCount = Math.max(...counts);
                    const barWidth = 70;
                    const margin = 50;
                    const chartHeight = canvas.height - 100;
                    const chartWidth = canvas.width - 2 * margin;

                    // Couleurs selon le thème
                    const isNightMode = document.body.classList.contains('dark-mode');
                    const barColor = isNightMode ? '#00b894' : '#0984e3';
                    const barHoverColor = isNightMode ? '#55efc4' : '#74b9ff';
                    const textColor = isNightMode ? '#dfe6e9' : '#2d3436';
                    const gridColor = isNightMode ? 'rgba(255, 255, 255, 0.15)' : 'rgba(0, 0, 0, 0.15)';
                    const glowColor = isNightMode ? 'rgba(85, 239, 196, 0.5)' : 'rgba(116, 185, 255, 0.5)';

                    ctx.font = '13px "Roboto", Arial, sans-serif';

                    // Animation d’entrée
                    let animationFrame = 0;
                    const animateBars = () => {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        // Grille
                        ctx.strokeStyle = gridColor;
                        ctx.lineWidth = 1;
                        for (let i = 0; i <= 6; i++) {
                            const y = chartHeight - (i * chartHeight / 6);
                            ctx.beginPath();
                            ctx.moveTo(margin, y);
                            ctx.lineTo(canvas.width - margin, y);
                            ctx.stroke();
                            ctx.fillStyle = textColor;
                            ctx.textAlign = 'left';
                            ctx.fillText(Math.round((i / 6) * maxCount), margin - 40, y + 5);
                        }

                        // Barres animées
                        counts.forEach((count, index) => {
                            const x = margin + (chartWidth - cities.length * barWidth) / (cities.length + 1) * (index + 1) + barWidth * index;
                            const targetHeight = (count / maxCount) * chartHeight;
                            const height = Math.min(targetHeight, targetHeight * (animationFrame / 60));
                            const y = chartHeight - height;

                            // Dégradé
                            const gradient = ctx.createLinearGradient(x, y, x, chartHeight);
                            gradient.addColorStop(0, barColor);
                            gradient.addColorStop(1, isNightMode ? '#2d3436' : '#dfe6e9');
                            ctx.fillStyle = gradient;

                            // Barre avec bordure
                            ctx.fillRect(x, y, barWidth, height);
                            ctx.strokeStyle = barColor;
                            ctx.lineWidth = 2;
                            ctx.strokeRect(x, y, barWidth, height);

                            // Étiquettes
                            ctx.fillStyle = textColor;
                            ctx.textAlign = 'center';
                            ctx.save();
                            ctx.translate(x + barWidth / 2, chartHeight + 30);
                            ctx.fillText(cities[index], 0, 0);
                            ctx.restore();

                            ctx.fillText(count, x + barWidth / 2, y - 15);
                        });

                        if (animationFrame < 60) {
                            animationFrame++;
                            requestAnimationFrame(animateBars);
                        }
                    };

                    // Lancer l’animation
                    animateBars();

                    // Interaction au survol
                    canvas.addEventListener('mousemove', (e) => {
                        const rect = canvas.getBoundingClientRect();
                        const mouseX = e.clientX - rect.left;
                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        // Redessiner la grille
                        ctx.strokeStyle = gridColor;
                        for (let i = 0; i <= 6; i++) {
                            const y = chartHeight - (i * chartHeight / 6);
                            ctx.beginPath();
                            ctx.moveTo(margin, y);
                            ctx.lineTo(canvas.width - margin, y);
                            ctx.stroke();
                            ctx.fillStyle = textColor;
                            ctx.fillText(Math.round((i / 6) * maxCount), margin - 40, y + 5);
                        }

                        counts.forEach((count, index) => {
                            const x = margin + (chartWidth - cities.length * barWidth) / (cities.length + 1) * (index + 1) + barWidth * index;
                            const height = (count / maxCount) * chartHeight;
                            const y = chartHeight - height;
                            const isHovered = mouseX >= x && mouseX <= x + barWidth;

                            ctx.fillStyle = isHovered ? barHoverColor : barColor;
                            ctx.fillRect(x, y, barWidth, height);

                            if (isHovered) {
                                ctx.shadowColor = glowColor;
                                ctx.shadowBlur = 15;
                                ctx.fillRect(x, y, barWidth, height);
                                ctx.shadowBlur = 0;
                            }

                            ctx.strokeStyle = barColor;
                            ctx.strokeRect(x, y, barWidth, height);

                            ctx.fillStyle = textColor;
                            ctx.save();
                            ctx.translate(x + barWidth / 2, chartHeight + 30);
                            ctx.fillText(cities[index], 0, 0);
                            ctx.restore();

                            ctx.fillText(count, x + barWidth / 2, y - 15);
                        });
                    });
                });
            </script>
        <?php endif; ?>
    </section>
</main>

<?php include "include/footer.inc.php"; ?>