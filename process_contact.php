<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyer les entrées
    $name = filter_var(trim($_POST['name'] ?? ''), FILTER_UNSAFE_RAW);
    $name = strip_tags($name);
    
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    
    $subject = filter_var(trim($_POST['subject'] ?? ''), FILTER_UNSAFE_RAW);
    $subject = strip_tags($subject);
    
    $message = filter_var(trim($_POST['message'] ?? ''), FILTER_UNSAFE_RAW);
    $message = strip_tags($message);

    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        die('Tous les champs sont requis.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Email invalide.');
    }

    // Exemple : envoyer un email (configure ton serveur SMTP)
    $to = 'malikhk2006@gmail.com'; 
    $headers = "From: $email\r\n";
    $body = "Nom: $name\nEmail: $email\nSujet: $subject\n\nMessage:\n$message";
    
    if (mail($to, $subject, $body, $headers)) {
        header('Location: contact.php?success=1');
    } else {
        die('Erreur lors de l\'envoi.');
    }
} else {
    header('Location: contact.php');
}
?>