<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce"; // Remplacez par le nom de votre base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php
session_start();
// Inclure le fichier de configuration de la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérifier si les mots de passe correspondent
    if ($new_password !== $confirm_password) {
        header('Location: success.php');
        exit();
    }

    // Vérifier les conditions de sécurité du mot de passe
    if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        header('Location: success.php');
        exit();
    }

    // Récupérer l'email de la session
    if (!isset($_SESSION['email'])) {
        header('Location: success.php');
        exit();
    }
    $email = $_SESSION['email'];

    // Hash le nouveau mot de passe
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Connexion à la base de données
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Mettre à jour le mot de passe dans la table clients
    $sql_clients = "UPDATE clients SET password = ? WHERE email = ?";
    $stmt_clients = $conn->prepare($sql_clients);
    $stmt_clients->bind_param("ss", $hashed_password, $email);
    $stmt_clients->execute();

    // Mettre à jour le mot de passe dans la table vendeurs
    $sql_vendeurs = "UPDATE vendeurs SET password = ? WHERE email = ?";
    $stmt_vendeurs = $conn->prepare($sql_vendeurs);
    $stmt_vendeurs->bind_param("ss", $hashed_password, $email);
    $stmt_vendeurs->execute();

    // Fermer les connexions
    $stmt_clients->close();
    $stmt_vendeurs->close();
    $conn->close();

    // Rediriger vers une page de succès ou de connexion
    header('Location: index.html.html'); // Changez 'login.php' par la page de votre choix
    exit();
} else {
    // Rediriger vers la page de création de mot de passe si le formulaire n'a pas été soumis
    header('Location: success.php');
    exit();
}
?>
