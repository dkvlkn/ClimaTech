<?php
/**
 * Page de contact du site Climatech.
 *
 * Affiche un formulaire de contact avec gestion multilingue via $_LANG.
 * Affiche un message de confirmation si l'envoi a été effectué.
 * Intègre également la bannière de consentement aux cookies.
 *
 * PHP version 8+
 *
 * @category WebApp
 * @package  Climatech\ContactPage
 * @author   VotreNom
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://climatech.example.com/contact.php
 */

// Inclusion des fonctions
require_once 'include/functions.inc.php';

/**
 * Titre de la page (multilingue)
 *
 * @var string $pageTitle
 */
$pageTitle = ($_LANG['contact_title'] ?? 'Contactez-nous') . ' | Climatech';

/**
 * Style graphique choisi par l'utilisateur
 *
 * @var string $style
 */
$style = $_GET['style'] ?? ($_COOKIE['theme'] ?? 'standard');

// Inclusion de l'en-tête HTML
include 'include/header.inc.php';
?>

<main class="main-content">
    <section class="contact-section">
        <h2><?php echo $_LANG['contact_title'] ?? 'Contactez-nous'; ?></h2>
        <p class="contact-intro">
            <?php echo $_LANG['contact_intro'] ?? 'Vous avez une question ou une suggestion ? Remplissez le formulaire ci-dessous, et nous vous répondrons dès que possible.'; ?>
        </p>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <p class="success-message">
                <?php echo $_LANG['contact_success'] ?? 'Votre message a été envoyé avec succès !'; ?>
            </p>
        <?php endif; ?>

        <!-- Formulaire de contact -->
        <form action="process_contact.php" method="post" class="contact-form">
            <div class="form-group">
                <label for="name"><?php echo $_LANG['contact_name'] ?? 'Nom'; ?></label>
                <input type="text" id="name" name="name" required="required" />
            </div>

            <div class="form-group">
                <label for="email"><?php echo $_LANG['contact_email'] ?? 'Email'; ?></label>
                <input type="email" id="email" name="email" required="required" />
            </div>

            <div class="form-group">
                <label for="subject"><?php echo $_LANG['contact_subject'] ?? 'Sujet'; ?></label>
                <input type="text" id="subject" name="subject" required="required" />
            </div>

            <div class="form-group">
                <label for="message"><?php echo $_LANG['contact_message'] ?? 'Message'; ?></label>
                <textarea id="message" name="message" required="required"></textarea>
            </div>

            <button type="submit" class="submit-button">
                <?php echo $_LANG['contact_submit'] ?? 'Envoyer'; ?>
            </button>
        </form>
    </section>

    <!-- Bannière de consentement aux cookies -->
    <div id="cookie-consent" class="cookie-consent<?php echo function_exists('shouldShowCookieConsent') && shouldShowCookieConsent() ? '' : ' hidden'; ?>" role="dialog" aria-label="<?php echo $_LANG['cookie_consent_label'] ?? 'Consentement aux cookies'; ?>">
        <p><?php echo $_LANG['cookie_consent_text'] ?? 'Nous utilisons des cookies pour améliorer votre expérience et analyser notre trafic. Acceptez-vous leur utilisation ?'; ?></p>
        <div class="cookie-buttons">
            <button id="accept-cookies"><?php echo $_LANG['accept'] ?? 'Accepter'; ?></button>
            <button id="decline-cookies"><?php echo $_LANG['decline'] ?? 'Refuser'; ?></button>
        </div>
    </div>
</main>

<!-- Script JS pour gestion des cookies -->
<script src="js/cookie-consent.js"></script>

<?php
// Pied de page
include 'include/footer.inc.php';
?>
