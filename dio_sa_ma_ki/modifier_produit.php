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
    // Récupérer l'identifiant du produit à modifier
    $product_id = $_POST['product_id'];

    // Récupérer les détails du produit depuis la base de données
    $sql = "SELECT * FROM produits WHERE id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Afficher un formulaire pré-rempli avec les détails du produit
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Modifier Produit</title>
        </head>
        <body>
            <h2>Modifier Produit</h2>
            <form action="traiter_modification.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" value="<?php echo $row['nom']; ?>"><br><br>
                <label for="description">Description:</label>
                <textarea id="description" name="description"><?php echo $row['description']; ?></textarea><br><br>
                <label for="prix">Prix:</label>
                <input type="text" id="prix" name="prix" value="<?php echo $row['prix']; ?>"><br><br>
              
                <button type="submit">Valider</button>
            </form>
        </body>
        </html>
        <?php
    } else {
        echo "Produit non trouvé.";
    }
} else {
    echo "Méthode non autorisée.";
}
$conn->close();
?>
