<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du Code</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            width: 7cm;
            height: 6cm;
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
            font-size: 1.2em;
        }

        .container .code-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .container .code-container input {
            width: 0.5cm; /* Taille ajustable selon vos besoins */
            height: 1cm;
            padding: 5px;
            margin: 0 0.1cm;
            border: 1px solid #4bcdA2;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
        }

        .container button {
            width: calc(100% - 20px);
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
        <h2>entrer le code de verification</h2>
        <form action="code_email.php" method="POST">
            <div class="code-container">
                <input type="text" name="code1" class="code-input" maxlength="1" required>
                <input type="text" name="code2" class="code-input" maxlength="1" required>
                <input type="text" name="code3" class="code-input" maxlength="1" required>
                <input type="text" name="code4" class="code-input" maxlength="1" required>
                <input type="text" name="code5" class="code-input" maxlength="1" required>
                <input type="text" name="code6" class="code-input" maxlength="1" required>
            </div>
            <button type="submit">Soumettre</button>
        </form>
        <div class="link">
            <a href="oublie.html"><i class="fa-solid fa-rotate-left"></i> Réessayer de nouveau</a>
        </div>
    </div>
</body>
</html>
