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

// Récupérer les paramètres du formulaire
$quantites = $_POST['quantite'];

// Boucle à travers les quantités pour mettre à jour le panier
foreach ($quantites as $key => $quantite) {
    $product_id = $key + 1; // ID du produit (notez que c'est une approche simplifiée)

    // Requête pour mettre à jour la quantité du produit dans le panier
    $sql_update = "UPDATE panier SET quantite = ? WHERE user_id = ? AND product_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iii", $quantite, $client_id, $product_id);
    $stmt_update->execute();
}

echo "Commande validée avec succès.";

// Fermer la connexion à la base de données
$conn->close();
?>
