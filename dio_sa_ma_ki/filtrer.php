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

// Récupérer les valeurs de filtrage depuis le formulaire
$categorie = isset($_POST['categorie']) ? $_POST['categorie'] : 'tous';
$prix = isset($_POST['prix']) ? $_POST['prix'] : 'tous';

// Construire la requête SQL en fonction des valeurs de filtrage
$sql = "SELECT * FROM produits WHERE 1 ";

if ($categorie != 'tous') {
    $sql .= " AND categorie = '$categorie'";
}

if ($prix != 'tous') {
    switch ($prix) {
        case '0-50':
            $sql .= " AND prix BETWEEN 0 AND 50";
            break;
        case '51-100':
            $sql .= " AND prix BETWEEN 51 AND 100";
            break;
        case '101-150':
            $sql .= " AND prix BETWEEN 101 AND 150";
            break;
        case '151-200':
            $sql .= " AND prix BETWEEN 151 AND 200";
            break;
        case '201-250':
            $sql .= " AND prix > 200";
            break;
       
    }
}

// Exécuter la requête SQL pour récupérer les produits filtrés
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
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
    <form action="" method="post">
        <div class="filter">
            <label for="categorie">Filtrer par catégorie :</label>
            <select id="categorie" name="categorie">
            <option value="tous">tous</option>
                    <option value="vetements">Vêtements</option>
                    <option value="chaussures">Chaussures</option>
                    <option value="accessoires">Accessoires</option>
                    <option value="electroniques">Électroniques</option>
                    <option value="maison">beaute et sante</option>
               
                    
       
            </select>
            <label for="prix">Filtrer par prix :</label>
            <select id="prix" name="prix">
           
            <option value="tout">tout</option>
                    <option value="0-50">0 - 50</option>
                    <option value="51-100">51 - 100</option>
                    <option value="101-150">101 - 150</option>
                    <option value="151-200">151 - 200</option>
                    <option value="201-250">plus de 200</option>
                    
                </select>
                <!-- Ajoutez d'autres options de prix selon vos besoins -->
            </select>
            <button type="submit">Filtrer</button>
        </div>
    </form>
    <form action="voir_panier.php" method="post" class="cart">
        <button type="submit">Voir Panier</button>
    </form>

       
    <div class="container">
    <?php
    // Vérifier s'il y a des produits à afficher
    if ($result->num_rows > 0) {
        // Parcourir chaque produit
        while($row = $result->fetch_assoc()) {
            // Afficher les détails du produit
            ?>
            <div class="product">
                <?php
                // Chemin de l'image du produit
                $image_path = "image/" . $row['image']; 
                // Vérifier si le fichier image existe
                if (file_exists($image_path)) {
                    // Vérifier si le fichier est lisible
                    if (!is_readable($image_path)) {
                        // Ajouter les droits de lecture
                        chmod($image_path, 0644);
                    }
                    // Afficher l'image du produit
                    echo '<img src="' . $image_path . '" alt="' . $row['nom'] . '">';
                } else {
                    // Si le fichier n'existe pas, afficher une image par défaut
                    echo '<img src="chemin_vers_image_par_defaut/default_image.jpg" alt="Image par défaut">';
                }
                ?>
                <h3><?php echo $row['nom']; ?></h3>
                <p><?php echo $row['description']; ?></p>
                <p>Prix: <?php echo $row['prix']; ?>€</p>
                <!-- Bouton d'ajout au panier -->
                <form action="ajouter_panier.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <button type="submit">Ajouter au panier</button>
                </form>
                <!-- Boutons de modification et suppression -->
               
            <?php
        }
    } else {
        echo "Aucun produit trouvé.";
    }
    ?>
</div>
</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>

                