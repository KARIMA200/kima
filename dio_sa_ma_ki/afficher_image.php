<?php
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

// Récupérer l'ID du produit depuis l'URL


// Préparer et exécuter la requête pour récupérer le chemin de l'image du produit
$sql = "SELECT image FROM produits WHERE ;
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->store_result();
// Vérifier s'il y a une image pour ce produit
if ($stmt->num_rows > 0) {
    // Récupérer le chemin de l'image
    $stmt->bind_result($image_path);
    $stmt->fetch();
    
    // Vérifier si le fichier image existe
    if (file_exists($image_path)) {
        // Afficher l'image
        header("Content-type: image/jpeg"); // Indique que le contenu est une image JPEG
        readfile($image_path);
    } else {
        // Si l'image n'existe pas, afficher une image par défaut ou un message d'erreur
        // Exemple avec une image par défaut :
        $default_image_path = "image/default.jpg";
        if (file_exists($default_image_path)) {
            header("Content-type: image/jpeg");
            readfile($default_image_path);
        } else {
            echo "Image par défaut introuvable.";
        }
    }
} else {
    // Si aucune image n'est trouvée, afficher une image par défaut ou un message d'erreur
    // Exemple avec une image par défaut :
    $default_image_path = "image/default.jpg";
    if (file_exists($default_image_path)) {
        header("Content-type: image/jpeg");
        readfile($default_image_path);
    } else {
        echo "Image par défaut introuvable.";
    }
}

// Fermer la requête préparée
$stmt->close();

// Fermer la connexion à la base de données
$conn->close();
?>
