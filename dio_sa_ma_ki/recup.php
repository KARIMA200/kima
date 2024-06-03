<?php
// Démarrez la session PHP
session_start();

// Inclure l'autoloader de Composer
include __DIR__ . '/vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Inclure Exception.php
include __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Générer un nombre aléatoire à six chiffres
    $verificationCode = rand(100000, 999999);
    
    // Récupérer l'email du destinataire à partir de la requête POST
    $recipientEmail = $_POST['email'];
    
    // Valider l'email
    if (filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        // Création de l'objet PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Paramètres du serveur SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Serveur SMTP de Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'chougri.kar.fst@uhp.ac.ma'; // Votre adresse email
            $mail->Password = '0NB25ERTRAQ'; // Votre mot de passe ou mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Ou PHPMailer::ENCRYPTION_SMTPS pour SSL
            $mail->Port = 587; // 587 pour TLS, 465 pour SSL

            // Destinataires
            $mail->setFrom('chougri.kar.fst@uhp.ac.ma', 'kya'); // Votre adresse email et votre nom
            $mail->addAddress($recipientEmail); // Adresse email du destinataire

            // Contenu de l'email
            $mail->isHTML(true); // Définit le format de l'email en HTML
            $mail->Subject = 'Code de vérification';
            $mail->Body = 'Votre code de vérification est : ' . $verificationCode;
            $mail->AltBody = 'Votre code de vérification est : ' . $verificationCode;

            // Envoyer l'email
            if ($mail->send()) {
                // Stocker le code dans une variable de session
                $_SESSION['verification_code'] = $verificationCode;
                // Redirection vers la page verif.php si l'email est envoyé avec succès
                header('Location: verif.php');
                exit();
            } else {
                // Affichage d'un message si l'email n'a pas pu être envoyé
                echo "L'email n'a pas pu être envoyé.";
            }
        } catch (Exception $e) {
            // Affichage d'un message en cas d'erreur
            echo "L'email n'a pas pu être envoyé. Erreur de Mailer : {$mail->ErrorInfo}";
        }
    } else {
        // Affichage d'un message si l'adresse email est invalide
        echo 'Adresse email invalide.';
    }
} else {
    // Affichage d'un message si le formulaire n'a pas été soumis
    echo 'Formulaire non soumis.';
}
?>
