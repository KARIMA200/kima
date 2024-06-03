<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}

// Récupérer l'email de l'utilisateur à partir de la session
$email = $_SESSION["email"];

// Préparer la requête SQL pour trouver l'ID du vendeur correspondant à l'email de l'utilisateur
$stmt = $conn->prepare("SELECT id FROM vendeurs WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Vérifier s'il y a des résultats
if ($result->num_rows > 0) {
    // Récupérer la première ligne de résultats
    $row = $result->fetch_assoc();
    // L'ID du vendeur correspondant à l'email de l'utilisateur
    $vendeur_id = $row['id'];
    // Utilisez $vendeur_id comme vous le souhaitez
} else {
    // Aucun vendeur trouvé pour cet email
    echo "Aucun vendeur trouvé pour cet email.";
}

// Requête SQL pour compter le nombre de messages non lus pour ce vendeur
$sql1 = "SELECT COUNT(*) AS count
         FROM messages
         WHERE lu = 0 AND destinataire = '$email'";

$result1 = $conn->query($sql1);

// Nombre total de messages non lus pour ce vendeur


if ($result1->num_rows > 0) {
    $row = $result1->fetch_assoc();
    $total_unread_messages = $row['count'];
}

// Récupérer tous les produits de la table produits
$sql = "SELECT * FROM produits";
$result = $conn->query($sql);
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
    <form action="filtrer.php" method="post">
        <div class="filter">
            <label for="category">Filtrer par catégorie :</label>
            
                <select id="categorie" name="categorie">
                    <option value="vetements">Vêtements</option>
                    <option value="chaussures">Chaussures</option>
                    <option value="accessoires">Accessoires</option>
                    <option value="electroniques">Électroniques</option>
                    <option value="maison">Maison</option>
                    <option value="jouets">Jouets</option>
                    <option value="beaute">Beauté</option>
                    <option value="sports">Sports</option>
                    <option value="livres">Livres</option>
                    <option value="musique">Musique</option>
                    <option value="films">Films</option>
                    <option value="jeux">Jeux</option>
                    <option value="alimentation">Alimentation</option>
                    <option value="boissons">Boissons</option>
                    <option value="artisanat">Artisanat</option>
                </select>
                <label for="prix">Prix du Produit:</label>
                <select id="prix" name="prix">
                    <option value="0-50">0 - 50</option>
                    <option value="51-100">51 - 100</option>
                    <option value="101-150">101 - 150</option>
                    <option value="151-200">151 - 200</option>
                    <option value="201-250">201 - 250</option>
                    <option value="251-300">251 - 300</option>
                    <option value="301-350">301 - 350</option>
                    <option value="plus">Plus de 400</option>
                </select>
            <button type="submit" id="button">Filtrer</button>
        </div></form>
        <div id="notification-icon">
            
    <!-- Icône de notification -->
    <img src="chemin_vers_icone_notification.png" alt="Notification">
    <!-- Nombre de notifications -->
    <span id="notification-count"> <?php echo $total_unread_messages ; ?></span>
</div>
<script>
    document.getElementById("notification-icon").addEventListener("click", function() {
        // Redirection vers la page de dashboard
        window.location.href = "dashboard2.php";
    });
</script>

<style>
    #notification-icon {
        position: relative;
        cursor: pointer;
    }
    #notification-icon img {
        width: 30px; /* Ajustez la taille de l'icône selon vos besoins */
        height: auto;
    }
    #notification-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 2px 5px;
        font-size: 12px;
    }
</style>



              

        <form action="add_produit.html" method="post">
        <button type="submit">Ajouter un produit</button>
    </form>
    <form action="voir_produits.php" method="post">
        <button type="submit">voir mes produits</button>
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
                    
                    <!-- Boutons de modification et suppression -->
                   
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

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
