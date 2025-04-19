document.addEventListener('DOMContentLoaded', () => {
    const regionSelect = document.getElementById('region');
    const deptSelect = document.getElementById('departement');
    const villeSelect = document.getElementById('ville');
    const btn = document.getElementById('rechercher');
    const meteoDiv = document.getElementById('resultat-meteo');
    const formTitle = document.getElementById('form-title');

    // Initialiser les régions
    fetch('api.php?action=regions')
        .then(res => res.json())
        .then(regions => {
            regions.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r;
                opt.textContent = r;
                regionSelect.appendChild(opt);
            });
        });

    // Gérer clics sur la carte
    document.querySelectorAll('area').forEach(area => {
        area.addEventListener('click', e => {
            e.preventDefault();
            const selectedRegion = area.getAttribute('alt');
            if (!selectedRegion) return;

            for (let i = 0; i < regionSelect.options.length; i++) {
                if (regionSelect.options[i].text === selectedRegion) {
                    regionSelect.selectedIndex = i;
                    break;
                }
            }
            regionSelect.dispatchEvent(new Event('change'));
            if (formTitle) {
                formTitle.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Quand une région est sélectionnée
    regionSelect.addEventListener('change', () => {
        const region = regionSelect.value;
        deptSelect.innerHTML = '<option value="">-- Choisir un département --</option>';
        villeSelect.innerHTML = '<option value="">-- Choisir une ville --</option>';
        deptSelect.disabled = true;
        villeSelect.disabled = true;
        btn.disabled = true;

        if (!region) return;

        fetch('api.php?action=departements&region=' + encodeURIComponent(region))
            .then(res => res.json())
            .then(departements => {
                departements.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d;
                    opt.textContent = d;
                    deptSelect.appendChild(opt);
                });
                deptSelect.disabled = false;
            });
    });

    // Quand un département est sélectionné
    deptSelect.addEventListener('change', () => {
        const dept = deptSelect.value;
        villeSelect.innerHTML = '<option value="">-- Choisir une ville --</option>';
        villeSelect.disabled = true;
        btn.disabled = true;

        if (!dept) return;

        fetch('api.php?action=villes&departement=' + encodeURIComponent(dept))
            .then(res => res.json())
            .then(villes => {
                villes.forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v;
                    opt.textContent = v;
                    villeSelect.appendChild(opt);
                });
                villeSelect.disabled = false;
            });
    });

    // Activer bouton quand une ville est choisie
    villeSelect.addEventListener('change', () => {
        btn.disabled = !villeSelect.value;
    });

    // Afficher la météo avec prévisions
    btn.addEventListener('click', () => {
        const ville = villeSelect.value;
        meteoDiv.innerHTML = '<p class="weather-loading">Chargement...</p>';
        fetch('meteo_ajax.php?ville=' + encodeURIComponent(ville))
            .then(res => res.text())
            .then(html => {
                meteoDiv.innerHTML = html;
                // Ajouter l'événement de clic pour les cartes de prévision
                document.querySelectorAll('.weather-forecast-card').forEach(card => {
                    card.addEventListener('click', () => {
                        const details = card.querySelector('.forecast-details');
                        if (details.style.display === 'block') {
                            details.style.display = 'none';
                        } else {
                            // Masquer les autres détails ouverts
                            document.querySelectorAll('.forecast-details').forEach(d => d.style.display = 'none');
                            details.style.display = 'block';
                        }
                    });
                });
            })
            .catch(error => {
                meteoDiv.innerHTML = '<p class="weather-error">Erreur lors du chargement de la météo.</p>';
            });
    });
});