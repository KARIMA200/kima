
<?php
session_start();

// Vérification si une variable de session d'erreur existe
if (isset($_SESSION['error_message'])) {
    // Stocker le message d'erreur dans une variable locale
    $error_message = $_SESSION['error_message'];
    // Supprimer la variable de session d'erreur pour qu'elle ne s'affiche pas à nouveau
    unset($_SESSION['error_message']);
}?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer Mot de Passe</title>
    <style>
        .change-password-container  {
            width: 5cm;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-color:red;
        }
        .change-password-container h2 {
            text-align: center;
        }
        .password-form {
            margin-top: 20px;
        }
        .password-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .password-form button {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #4bcdA2;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .password-form button:hover {
            background-color: #3ba88e;
        }
    </style>
</head>
<body>
    <div class="change-password-container">
        <h2>Changer Mot de Passe</h2>
        <!-- Vérification si le message est défini dans l'URL -->
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <!-- Formulaire de changement de mot de passe -->
        <form class="password-form" action="changer_password.php" method="POST">
            <input type="password" name="ancien" placeholder="Entrer votre ancien mot de passe" required>
            <input type="password" name="nouveau" placeholder="Entrer votre nouveau mot de passe" required>
            <input type="password" name="confirmer" placeholder="Confirmer votre mot de passe" required>
            <button type="submit">Changer Mot de Passe</button>
        </form>
    </div>
</body>
</html>
