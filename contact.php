<?php
require_once 'include/functions.inc.php';
$pageTitle = 'Contactez-nous | Climatech';
$style = $_GET['style'] ?? ($_COOKIE['theme'] ?? 'standard');
?>

<?php include 'include/header.inc.php'; ?>

<main class="main-content">
    <section class="contact-section">
        <h2>Contactez-nous</h2>
        <p class="contact-intro">Vous avez une question ou une suggestion ? Remplissez le formulaire ci-dessous, et nous vous répondrons dès que possible.</p>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <p class="success-message">Votre message a été envoyé avec succès !</p>
        <?php endif; ?>
        
        <form action="process_contact.php" method="post" class="contact-form">
            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" required placeholder="Votre nom">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="votre@email.com">
            </div>
            <div class="form-group">
                <label for="subject">Sujet</label>
                <input type="text" id="subject" name="subject" required placeholder="Objet de votre message">
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" required placeholder="Votre message..."></textarea>
            </div>
            <button type="submit" class="submit-button">Envoyer</button>
        </form>
    </section>
</main>

<?php include 'include/footer.inc.php'; ?>