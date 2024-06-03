<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    die("Vous n'êtes pas connecté.");
}

// Récupérer l'email de l'utilisateur depuis la session
$email = $_SESSION['email'];

// Récupérer le product_id depuis la requête GET
if (!isset($_GET['product_id'])) {
    die("Product ID non spécifié.");
}

$product_id = $_GET['product_id'];

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

// Préparer et exécuter la requête SQL pour supprimer l'entrée de la table favoris
$sql = "DELETE FROM favoris WHERE user_email = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $email, $product_id);
$stmt->execute();

// Redirection vers la page voir_favorie.php
header("Location: voir_favorie.php");
exit();
?>
