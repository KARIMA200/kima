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

// Définition des valeurs par défaut pour le filtrage
$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : 'tous';
$prix = isset($_GET['prix']) ? $_GET['prix'] : 'tous';

// Construire la requête SQL en fonction des valeurs de filtrage
$sql = "SELECT * FROM produits WHERE 1 ";

if ($categorie != 'tous') {
    $sql .= " AND categorie = '$categorie'";
}

if ($prix != 'tous') {
    switch ($prix) {
        case '0-20':
            $sql .= " AND prix BETWEEN 0 AND 20";
            break;
        case '20-50':
            $sql .= " AND prix BETWEEN 20 AND 50";
            break;
        case '50-100':
            $sql .= " AND prix BETWEEN 50 AND 100";
            break;
        case 'plus':
            $sql .= " AND prix > 100";
            break;
    }
}

// Exécuter la requête SQL pour récupérer les produits filtrés
$result = $conn->query($sql);

// Vérifier s'il y a des produits à afficher
if ($result->num_rows > 0) {
    // Afficher les produits
    while ($row = $result->fetch_assoc()) {
        // Affichage de chaque produit
        echo "<div class='product'>";
        echo "<img src='{$row['image']}' alt='{$row['nom_produit']}'>";
        echo "<h3>{$row['nom_produit']}</h3>";
        echo "<p>{$row['description']}</p>";
        echo "<p>Prix: {$row['prix']}€</p>";
        echo "<form action='ajouter_panier.php' method='post'>";
        echo "<input type='hidden' name='product_id' value='{$row['id']}'>";
        echo "<button type='submit'>Ajouter au panier</button>";
        echo "</form>";
        echo "</div>";
    }
} else {
    // Si aucun produit n'est trouvé
    echo "Aucun produit trouvé.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
