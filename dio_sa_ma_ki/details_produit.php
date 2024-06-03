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

// Vérifier si l'identifiant du produit est passé en paramètre
if(isset($_POST['product_id'])) {
    // Récupérer l'identifiant du produit depuis le formulaire
    $product_id = $_POST['product_id'];

    // Requête SQL pour récupérer les informations du produit
    $sql = "SELECT * FROM produits WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier s'il y a des résultats
    if ($result->num_rows > 0) {
        // Récupérer les données du produit
        $row = $result->fetch_assoc();
        $nom_produit = $row['nom'];
        $description_produit = $row['description'];
        $prix_produit = $row['prix'];
        $stock_produit = $row['stock'];
        $categorie_produit = $row['categorie'];

        // Afficher les détails du produit
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Détails du Produit</title>
        </head>
        <body>
            <h2>Détails du Produit</h2>
            <p><strong>Nom:</strong> <?php echo $nom_produit; ?></p>
            <p><strong>Description:</strong> <?php echo $description_produit; ?></p>
            <p><strong>Prix:</strong> <?php echo $prix_produit; ?>€</p>
            <p><strong>Stock:</strong> <?php echo $stock_produit; ?></p>
            <p><strong>Catégorie:</strong> <?php echo $categorie_produit; ?></p>

            <!-- Boutons de modification et suppression -->
            <form action="modifier_produit.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <button type="submit">Modifier</button>
            </form>
            <form action="supprimer_produit.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <button type="submit">Supprimer</button>
            </form>
        </body>
        </html>
        <?php
    } else {
        echo "Aucun produit trouvé avec l'identifiant $product_id.";
    }
} else {
    echo "L'identifiant du produit n'a pas été spécifié.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
