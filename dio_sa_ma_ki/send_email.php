<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $code = rand(100000, 999999);  // Génère un code de vérification de 6 chiffres

    // Stocker le code de vérification et l'horodatage dans la session
    $_SESSION['verification_code'] = $code;
    $_SESSION['verification_code_timestamp'] = time();

    // Configurer PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Paramètres du serveur
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com';  // Remplacez par votre serveur SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'your@example.com';  // Remplacez par votre adresse e-mail
        $mail->Password = 'your_password';    // Remplacez par votre mot de passe
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinataires
        $mail->setFrom('chougri.kar.fst@uhp.ac.ma', 'kya');
        $mail->addAddress($email);

        // Contenu de l'e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Code de vérification';
        $mail->Body    = 'Votre code de vérification est : <b>' . $code . '</b>';
        $mail->AltBody = 'Votre code de vérification est : ' . $code;

        $mail->send();
        echo 'Le message a été envoyé';
    } catch (Exception $e) {
        echo "Le message n'a pas pu être envoyé. Erreur Mailer : {$mail->ErrorInfo}";
    }
}
?>
