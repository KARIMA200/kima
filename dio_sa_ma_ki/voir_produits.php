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

session_start();

// Vérifier si l'email est stocké dans la session
if(isset($_SESSION['email'])) {
    // Récupérer l'email de la session
    $email = $_SESSION['email'];

    // Requête SQL pour trouver l'ID du vendeur par email
    $sql_vendeur = "SELECT id FROM vendeurs WHERE email = ?";
    $stmt_vendeur = $conn->prepare($sql_vendeur);
    $stmt_vendeur->bind_param("s", $email);
    $stmt_vendeur->execute();
    $result_vendeur = $stmt_vendeur->get_result();

    // Vérifier s'il y a des résultats
    if ($result_vendeur->num_rows > 0) {
        // Récupérer l'ID du vendeur
        $row_vendeur = $result_vendeur->fetch_assoc();
        $vendeur_id = $row_vendeur['id'];

        // Requête SQL pour récupérer les produits du vendeur
        $sql_produits = "SELECT * FROM produits WHERE id IN (SELECT produit_id FROM produits_vendeurs WHERE vendeur_id = ?)";
        $stmt_produits = $conn->prepare($sql_produits);
        $stmt_produits->bind_param("i", $vendeur_id);
        $stmt_produits->execute();
        $result_produits = $stmt_produits->get_result();
    } else {
        echo "Aucun vendeur trouvé avec l'email $email.";
    }
} else {
    echo "Aucun email trouvé dans la session.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Produits</title>
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
        }
        .product {
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            width: 200px;
            text-align: center;
        }
        .product img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <h2>Liste des Produits</h2>
    <div class="container">
        <?php
        // Vérifier s'il y a des produits à afficher
        if ($result_produits->num_rows > 0) {
            // Parcourir chaque produit
            while($row_produit = $result_produits->fetch_assoc()) {
                // Afficher les détails du produit
                ?>
                <div class="product">
                    <?php
                    // Chemin de l'image du produit
                    $image_path = "image/" . $row_produit['image']; 
                    // Vérifier si le fichier image existe
                    if (file_exists($image_path)) {
                        // Vérifier si le fichier est lisible
                        if (!is_readable($image_path)) {
                            // Ajouter les droits de lecture
                            chmod($image_path, 0644);
                        }
                        // Afficher l'image du produit
                        echo '<img src="' . $image_path . '" alt="' . $row_produit['nom'] . '">';
                    } else {
                        // Si le fichier n'existe pas, afficher une image par défaut
                        echo '<img src="chemin_vers_image_par_defaut/default_image.jpg" alt="Image par défaut">';
                    }
                    ?>
                    <h3><?php echo $row_produit['nom']; ?></h3>
                    <!-- Boutons de modification, supprimer et voir détails -->
                    <form action="modifier_produit.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $row_produit['id']; ?>">
                        <button type="submit">Modifier</button>
                    </form>
                    <form action="supprimer_produit.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $row_produit['id']; ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                    <form action="details_produit.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $row_produit['id']; ?>">
                        <button type="submit">Voir Détails</button>
                    </form>
                </div>
                <?php
            }
        } else {
            echo "Aucun produit trouvé.";
        }
        ?>
    </div>
</body>
</html>

<?
