<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données: " . $conn->connect_error);
}


$sql = "SELECT * FROM produits";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h3>" . $row["nom"] . "</h3>";
        echo "<p>" . $row["description"] . "</p>";
        echo "<p>Prix: " . $row["prix"] . "</p>";
        echo "<button class='add_to_cart' data-id='" . $row["id"] . "'>Ajouter au panier</button>";
        echo "</div>";
    }
} else {
    echo "Aucun produit trouvé.";
}
$conn->close();
?>
