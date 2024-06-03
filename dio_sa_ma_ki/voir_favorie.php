<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    die("Vous n'êtes pas connecté.");
}

// Récupérer l'email de l'utilisateur depuis la session
$email = $_SESSION['email'];

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

// Préparer et exécuter la requête SQL pour récupérer les product_id depuis la table favoris
$sql = "SELECT product_id FROM favoris WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu déroulant</title>
    <link rel="stylesheet" href="css/all.min.css">
</head>
<body>
<?php
// Vérifier s'il y a des résultats
if ($result->num_rows > 0) {
    // Parcourir les résultats et afficher les détails de chaque produit
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        // Récupérer les détails du produit depuis la table produits
        $sql_product = "SELECT * FROM produits WHERE id = ?";
        $stmt_product = $conn->prepare($sql_product);
        $stmt_product->bind_param("i", $product_id);
        $stmt_product->execute();
        $result_product = $stmt_product->get_result();

        // Vérifier s'il y a des résultats
        if ($result_product->num_rows > 0) {
            // Afficher les détails du produit
            while ($product = $result_product->fetch_assoc()) {
                // Affichage stylé des détails du produit
                echo "<div class='product'>";

                // Chemin de l'image du produit
                $image_path = "image/" . $product['image'];
                // Vérifier si le fichier image existe
                if (file_exists($image_path)) {
                    // Vérifier si le fichier est lisible
                    if (!is_readable($image_path)) {
                        // Ajouter les droits de lecture
                        chmod($image_path, 0644);
                    }
                    // Afficher l'image du produit
                    echo '<img src="' . $image_path . '" alt="' . $product['nom'] . '">';
                } else {
                    // Si le fichier n'existe pas, afficher une image par défaut
                    echo '<img src="chemin_vers_image_par_defaut/default_image.jpg" alt="Image par défaut">';
                }

                echo "<h3 class='product-name'>" . $product['nom'] . "</h3>";
                echo "<p class='product-description'>" . $product['description'] . "</p>";
                // Si vous avez un champ 'prenom' dans la table produits, vous pouvez l'afficher ici
                // echo "<p class='product-seller'>" . $product['prenom'] . "</p>";
             
                echo "<a href='supprimer_favorie.php?product_id=$product_id'><i class=\"fa-solid fa-trash\"></i></a>";
                echo "<a href='favorie_vers_panier.php?product_id=$product_id'><i class=\"fa-solid fa-cart-shopping\"></i></a>";
                echo "</div>";
            }
        } else {
            echo "Aucun produit trouvé pour cet utilisateur.";
        }
    }
} else {
    echo "Aucun produit favori trouvé pour cet utilisateur.";
}

// Fermer les requêtes et la connexion
$stmt->close();
$stmt_product->close();
$conn->close();
?>
</body>
</html>
