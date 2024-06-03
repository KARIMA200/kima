<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Mot de Passe</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
        }

        .container {
            width: 300px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            text-align: center;
        }

        .container h2 {
            margin-bottom: 20px;
            color: #343a40;
            font-size: 1.4em;
        }

        .container input {
            width: 60%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #4bcdA2;
            border-radius: 5px;
            font-size: 16px;
        }

        .container button {
            width: 40%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4bcdA2;
            color: #ffffff;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .container button:hover {
            background-color: #39a089;
        }

        .container .conditions {
            font-size: 12px;
            color: #6c757d;
            text-align: left;
            margin-top: -10px;
         color:red;
        }

        .container .link {
            font-size: 14px;
            color: #4bcdA2;
            margin-top: 10px;
        }

        .container .link a {
            text-decoration: none;
            color: #4bcdA2;
            font-weight: bold;
            transition: color 0.3s;
        }

        .container .link a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Créer un Nouveau Mot de Passe</h2>
        <form action="process_password.php" method="POST">
            <input type="password" name="new_password" placeholder="Nouveau Mot de Passe" required>
            <input type="password" name="confirm_password" placeholder="Confirmer Mot de Passe" required>
            <div class="conditions">
                * Le mot de passe doit contenir au moins 8 caractères, avec des majuscules, des minuscules et des chiffres.
            </div>
            <button type="submit">Soumettre</button>
        </form>
    </div>
</body>
</html>
