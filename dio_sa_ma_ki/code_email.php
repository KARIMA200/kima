<?php
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer le code de vérification stocké dans la session
    $storedVerificationCode = $_SESSION['verification_code'];

    // Récupérer le code entré par l'utilisateur
    $enteredCode = '';
    for ($i = 1; $i <= 6; $i++) {
        $enteredCode .= $_POST['code' . $i];
    }

    // Comparer les deux codes
    if ($storedVerificationCode == $enteredCode) {
        // Rediriger vers la page de succès si les codes correspondent
        header('Location: success.php');
        exit();
    } else {
        // Afficher un message d'erreur si les codes ne correspondent pas
        echo 'Code de vérification incorrect. Veuillez réessayer.';
    }
} else {
    // Rediriger vers la page de vérification si le formulaire n'a pas été soumis
    header('Location: verif.php');
    exit();
}
?>
