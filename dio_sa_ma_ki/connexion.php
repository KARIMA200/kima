<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Traitement des données de formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérification dans la table clients
    $sql_clients = "SELECT * FROM clients WHERE email='$email'";
    $result_clients = $conn->query($sql_clients);

    if ($result_clients->num_rows > 0) {
        $row_clients = $result_clients->fetch_assoc();
        $hashed_password_clients = $row_clients['password'];

        // Vérification du mot de passe avec password_verify()
        if (password_verify($password, $hashed_password_clients)) {
            // Démarrez la session
            session_start();

            // Stockez l'email dans une variable de session
            $_SESSION['email'] = $email;

            // Redirigez l'utilisateur vers la page d'accueil appropriée
            header('Location: uu.php');
            exit(); // Assure que le script s'arrête après la redirection
        }
    }

    // Vérification dans la table vendeurs
    $sql_vendeurs = "SELECT * FROM vendeurs WHERE email='$email'";
    $result_vendeurs = $conn->query($sql_vendeurs);

    if ($result_vendeurs->num_rows > 0) {
        $row_vendeurs = $result_vendeurs->fetch_assoc();
        $hashed_password_vendeurs = $row_vendeurs['password'];

        // Vérification du mot de passe avec password_verify()
        if (password_verify($password, $hashed_password_vendeurs)) {
            // Démarrez la session
            session_start();

            // Stockez l'email dans une variable de session
            $_SESSION['email'] = $email;

            // Redirigez l'utilisateur vers la page d'accueil appropriée
            header('Location: uuv.php');
            exit(); // Assure que le script s'arrête après la redirection
        }
    }

    // Si les informations de connexion ne correspondent à aucun enregistrement dans les tables clients et vendeurs
    echo "Adresse e-mail ou mot de passe incorrect.";
}

$conn->close();
?>
