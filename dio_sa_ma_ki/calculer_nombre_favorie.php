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

// Vérifier si l'ID du produit est passé en tant que paramètre
if(isset($_GET['produit_id'])) {
    // Récupérer l'ID du produit depuis le paramètre GET
    $product_id = $_GET['produit_id'];

    // Requête SQL pour compter le nombre de favoris pour ce produit
    $favoris_sql = "SELECT COUNT(*) AS favoris_count FROM favoris WHERE product_id = ?";
    $favoris_stmt = $conn->prepare($favoris_sql);
    $favoris_stmt->bind_param("i", $product_id);
    $favoris_stmt->execute();
    $favoris_result = $favoris_stmt->get_result();
    $favoris_row = $favoris_result->fetch_assoc();
    $favoris_count = $favoris_row['favoris_count'];

    // Retourner le nombre de favoris au format JSON
    echo json_encode(['favoris_count' => $favoris_count]);
} else {
    // Si l'ID du produit n'est pas passé en paramètre, retourner une erreur
    echo json_encode(['error' => 'ID du produit non spécifié']);
}

// Fermer la connexion à la base de données
$conn->close();
?>
