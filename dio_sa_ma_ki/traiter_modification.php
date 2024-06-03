<?php
// Placez ce code au début du fichier PHP pour initialiser la connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $product_id = $_POST['product_id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];

    // Valider les données si nécessaire

    // Mettre à jour le produit dans la base de données
    $sql = "UPDATE produits SET nom='$nom', description='$description', prix='$prix' WHERE id='$product_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Produit mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour du produit: " . $conn->error;
    }
} else {
    echo "Méthode non autorisée.";
}

$conn->close();
?>
