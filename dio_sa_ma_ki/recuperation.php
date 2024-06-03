<?php
echo "bonjour";
include __DIR__ . '/vendor/autoload.php'; // Inclure l'autoloader de Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Utilisez le chemin complet vers Exception.php
include __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';


// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer l'email du destinataire à partir de la requête POST
    $recipientEmail = $_POST['email'];
    
    // Afficher l'email récupéré pour vérification
    echo "Email récupéré depuis le formulaire : " . $recipientEmail . "<br>";

    // Valider l'email
    if (filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        // Afficher un message indiquant que l'email est valide
        echo "L'adresse email est valide.<br>";

        $mail = new PHPMailer(true);

        try {
            // Paramètres du serveur SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Serveur SMTP de Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'karima.chougri@gmail.com'; // Votre adresse email
            $mail->Password = '0NB25ERTRAQ'; // Votre mot de passe ou mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Ou PHPMailer::ENCRYPTION_SMTPS pour SSL
            $mail->Port = 465; // 587 pour TLS, 465 pour SSL

            // Destinataires
            $mail->setFrom('karima.chougri@gmail.com', 'karima'); // Votre adresse email et votre nom
            $mail->addAddress($recipientEmail); // Adresse email du destinataire

            // Contenu de l'email
            $mail->isHTML(true); // Définit le format de l'email en HTML
            $mail->Subject = 'Sujet de l\'email';
            $mail->Body = 'Contenu de l\'email en <b>HTML</b>';
            $mail->AltBody = 'Contenu de l\'email en texte brut';

            // Envoyer l'email
            if ($mail->send()) {
                echo 'Email envoyé avec succès à ' . htmlspecialchars($recipientEmail) . '!';
            } else {
                echo "L'email n'a pas pu être envoyé.";
            }
        } catch (Exception $e) {
            echo "L'email n'a pas pu être envoyé. Erreur de Mailer : {$mail->ErrorInfo}";
        }
    } else {
        echo 'Adresse email invalide.';
    }
} else {
    echo 'Formulaire non soumis.';
}
?>
