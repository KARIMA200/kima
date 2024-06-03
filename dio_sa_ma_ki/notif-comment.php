<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    die("Vous n'êtes pas connecté.");
}

// Récupérer l'email de l'utilisateur depuis la session
$email = $_SESSION['email'];

// Récupérer le product_id à partir des données du formulaire POST
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ecommerce";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Préparer la requête d'insertion
    $stmt = $conn->prepare("INSERT INTO favoris (product_id, user_email) VALUES (?, ?)");
    $stmt->bind_param("is", $product_id, $email);

    // Exécuter la requête
    if ($stmt->execute()) {
        echo "Produit ajouté aux favoris avec succès.";
    } else {
        echo "Erreur lors de l'ajout du produit aux favoris: " . $conn->error;
    }

    // Fermer la connexion et la déclaration préparée
    $stmt->close();
    $conn->close();
} else {
    echo "Le product_id n'a pas été fourni.";
}
?>
