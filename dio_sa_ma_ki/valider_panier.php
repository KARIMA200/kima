<?php
// Placez ce code au début du fichier PHP pour initialiser la connexion à la base de données

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

// Récupérer l'identifiant du client et du panier de l'utilisateur (supposons que vous les ayez stockés dans des variables $_POST)
$clientId = $_POST['client_id'];
$panierId = $_POST['panier_id'];

// Insérer les produits du panier dans la table des commandes
$sql = "INSERT INTO commande (client_id, panier_id) VALUES ('$clientId', '$panierId')";

$stmt = $conn->prepare($sql);

if ($stmt) {
    if ($stmt->execute()) {
        echo " La commande a été insérée avec succès";
      
    } else {
        echo "Erreur lors de l'insertion de la commande: " . $stmt->error;
    }
    // Fermer la requête préparée
    $stmt->close();
} else {
    echo "Erreur de préparation de la requête d'insertion de la commande: " . $conn->error;
}

// Fermer la connexion à la base de données
$conn->close();
?>
