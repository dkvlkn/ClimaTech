<?php
/**
 * Traitement du formulaire de contact.
 *
 * Ce script nettoie, valide les entrées POST du formulaire de contact, puis
 * envoie un email à l'adresse de destination. En cas de succès, l'utilisateur
 * est redirigé vers la page de contact avec un indicateur de réussite.
 *
 * PHP version 8+
 *
 * @category Formulaires
 * @package  Climatech\ContactFormHandler
 * @author   VotreNom
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://climatech.example.com/process_contact.php
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyer les entrées utilisateur
    $name = filter_var(trim($_POST['name'] ?? ''), FILTER_UNSAFE_RAW);
    $name = strip_tags($name);

    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

    $subject = filter_var(trim($_POST['subject'] ?? ''), FILTER_UNSAFE_RAW);
    $subject = strip_tags($subject);

    $message = filter_var(trim($_POST['message'] ?? ''), FILTER_UNSAFE_RAW);
    $message = strip_tags($message);

    // Validation des champs requis
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        die('Tous les champs sont requis.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Email invalide.');
    }

    // Configuration de l'email (à adapter selon ton serveur SMTP)
    $to = 'climatech@gmail.com';
    $headers = "From: $email\r\n";
    $body = "Nom: $name\nEmail: $email\nSujet: $subject\n\nMessage:\n$message";

    // Envoi de l'email
    if (mail($to, $subject, $body, $headers)) {
        header('Location: contact.php?success=1');
    } else {
        die('Erreur lors de l\'envoi.');
    }
} else {
    // Redirection si accès direct sans POST
    header('Location: contact.php');
}
?>
