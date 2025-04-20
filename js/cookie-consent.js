document.addEventListener("DOMContentLoaded", function() {
    const cookieConsent = document.getElementById('cookie-consent');
    const acceptButton = document.getElementById('accept-cookies');
    const declineButton = document.getElementById('decline-cookies');
    const manageLink = document.getElementById('manage-cookies');

    // Fonction pour parser les cookies
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
        return null;
    }

    // Fonction pour afficher la bannière
    function showCookieConsent() {
        if (cookieConsent) {
            cookieConsent.style.display = 'block';
        }
    }

    // Vérifier si le consentement existe déjà
    if (!getCookie('cookie_consent')) {
        showCookieConsent();
    }

    // Accepter les cookies
    if (acceptButton) {
        acceptButton.addEventListener('click', function() {
            document.cookie = 'cookie_consent=accepted; path=/; max-age=' + (365 * 24 * 60 * 60);
            if (cookieConsent) {
                cookieConsent.style.display = 'none';
            }
            applyNonEssentialCookies();
        });
    }

    // Refuser les cookies
    if (declineButton) {
        declineButton.addEventListener('click', function() {
            document.cookie = 'cookie_consent=declined; path=/; max-age=' + (365 * 24 * 60 * 60);
            if (cookieConsent) {
                cookieConsent.style.display = 'none';
            }
            removeNonEssentialCookies();
        });
    }

    // Gérer les cookies (réafficher la bannière)
    if (manageLink) {
        manageLink.addEventListener('click', function(e) {
            e.preventDefault();
            showCookieConsent();
        });
    }

    // Fonction pour appliquer les cookies non essentiels
    function applyNonEssentialCookies() {
        const lastCity = getCookie('last_city');
        if (lastCity) {
            document.cookie = 'last_city=' + encodeURIComponent(lastCity) + '; path=/; max-age=' + (30 * 24 * 60 * 60);
        }
        // Ajoutez ici d'autres cookies non essentiels si nécessaire
    }

    // Fonction pour supprimer les cookies non essentiels
    function removeNonEssentialCookies() {
        document.cookie = 'last_city=; path=/; max-age=0';
        document.cookie = 'theme=; path=/; max-age=0';
        // Ajoutez ici d'autres cookies non essentiels à supprimer
    }
});