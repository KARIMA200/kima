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
session_start();
$email = $_SESSION['email'];

// Récupérer tous les produits de la table produits
$sql1 = "SELECT * FROM produits";
$result1 = $conn->query($sql1);



// Requête SQL pour compter le nombre de messages non lus pour ce vendeur
$sql2 = "SELECT COUNT(*) AS count
         FROM messages
         WHERE lu = 0 AND destinataire = '$email'";

$result2 = $conn->query($sql2);

// Nombre total de messages non lus pour ce vendeur


if ($result2->num_rows > 0) {
    $row = $result2->fetch_assoc();
    $total_unread_messages = $row['count'];
}
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
    <form action="filtrer.php" method="post">
        <div class="filter">
            <label for="category">Filtrer par catégorie :</label>
            
                <select id="categorie" name="categorie">
                <option value="tous">tous</option>
                    <option value="vetements">Vêtements</option>
                    <option value="chaussures">Chaussures</option>
                    <option value="accessoires">Accessoires</option>
                    <option value="electroniques">Électroniques</option>
                    <option value="maison">Maison</option>
                    <option value="jouets">Jouets</option>
                    <option value="beaute">Beauté</option>
                    
                </select>
                <label for="prix">Prix du Produit:</label>
                <select id="prix" name="prix">
                <option value="tous">tous</option>
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
        </div>
         <!-- Barre de panier -->
 
    </form>
    </form>     

    <div id="notification-icon">
            
    <!-- Icône de notification -->
    <img src="chemin_vers_icone_notification.png" alt="Notification">
    <!-- Nombre de notifications -->
    <span id="notification-count"> <?php echo $total_unread_messages; ?></span>
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



    <form action="voir_panier.php" method="post" class="cart">
        <button type="submit">Voir Panier</button>
    </form>

    <div class="container">
        <?php
        // Vérifier s'il y a des produits à afficher
        if ($result1->num_rows > 0) {
            // Parcourir chaque produit
            while($row = $result1->fetch_assoc()) {
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
                    <div id="notification-icon2">
            
            <!-- Icône de notification -->
            <img src="chemin_vers_icone_notification.png" alt="Notification ">
            <!-- Nombre de notifications -->
            <span id="notification-count"> <?php echo $commente_nb; ?></span>
        </div>
        <!-- Bouton d'ajout au panier -->
                    <form action="ajouter_panier.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <button type="submit">Ajouter au panier</button>
                    </form>




                    
                



                    
                
<form action="envoyer_message.php" method="POST">



    <input type="hidden" name="produit_id" value="<?php echo $row['id']; ?>">
    <button type="submit">Envoyer un message</button>
</form>


<form action="commenter.php" method="POST">



    <input type="hidden" name="produit_id" value="<?php echo $row['id']; ?>">
    <button type="submit">commenter</button>
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
