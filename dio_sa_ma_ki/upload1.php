<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fonction pour
$date_email_concatenated = date('Y-m-d H:i:s') . "_" . $_POST['email_v'];
$stmt = $conn->prepare("INSERT INTO messages (produit_id, vendeur_id, contenu ) VALUES (50, 22, $date_email_concatenated)");


if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();


$conn->close();
?>