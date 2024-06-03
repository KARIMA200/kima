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

    // Requête SQL pour supprimer le produit de la table produits_vendeurs
    $sql_produit_vendeur = "DELETE FROM produits_vendeurs WHERE produit_id = ?";
    $stmt_produit_vendeur = $conn->prepare($sql_produit_vendeur);
    $stmt_produit_vendeur->bind_param("i", $product_id);
    $stmt_produit_vendeur->execute();

    // Vérifier si la suppression a réussi dans la table produits_vendeurs
    if ($stmt_produit_vendeur->affected_rows > 0) {
        // Maintenant, supprimez le produit de la table produits
        $sql_produit = "DELETE FROM produits WHERE id = ?";
        $stmt_produit = $conn->prepare($sql_produit);
        $stmt_produit->bind_param("i", $product_id);
        $stmt_produit->execute();

        // Vérifier si la suppression a réussi dans la table produits
        if ($stmt_produit->affected_rows > 0) {
            echo "Produit supprimé avec succès.";
        } else {
            echo "Erreur lors de la suppression du produit dans la table produits: " . $conn->error;
        }

        // Fermer la requête
        $stmt_produit->close();
    } else {
        echo "Erreur lors de la suppression du produit dans la table produits_vendeurs: " . $conn->error;
    }

    // Fermer la connexion à la base de données
    $stmt_produit_vendeur->close();
} else {
    echo "L'identifiant du produit n'a pas été spécifié.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
