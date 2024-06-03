<?php
// Démarrer la session
session_start();

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

// Vérifier si l'email de l'utilisateur est stocké en session
if (isset($_SESSION['email'])) {
    // Récupérer l'email de l'utilisateur
    $email = $_SESSION['email'];

    // Requête pour obtenir l'ID du client à partir de son email
    $sql_client_id = "SELECT id FROM clients WHERE email = ?";
    $stmt_client_id = $conn->prepare($sql_client_id);
    $stmt_client_id->bind_param("s", $email);
    $stmt_client_id->execute();
    $result_client_id = $stmt_client_id->get_result();

    if ($result_client_id->num_rows > 0) {
        // Récupération de l'ID du client
        $row = $result_client_id->fetch_assoc();
        $user_id = $row["id"];

        // Insérer une nouvelle commande dans la table commandes
        $sql_insert_commande = "INSERT INTO commande (email, date_commande) VALUES (?, NOW())";
        $stmt_insert_commande = $conn->prepare($sql_insert_commande);
        $stmt_insert_commande->bind_param("s", $email);
        $stmt_insert_commande->execute();

        // Obtenir l'ID de la nouvelle commande insérée
        $commande_id = $conn->insert_id;

        // Requête pour obtenir les produits du panier pour ce client
        $sql_panier_produits = "SELECT pa.product_id, pa.quantite FROM panier pa WHERE pa.user_id = ?";
        $stmt_panier_produits = $conn->prepare($sql_panier_produits);
        $stmt_panier_produits->bind_param("i", $user_id);
        $stmt_panier_produits->execute();
        $result_panier_produits = $stmt_panier_produits->get_result();

        // Insérer les détails de la commande dans la table commande_details
        $sql_insert_commande_details = "INSERT INTO commande_details (id_commande, id_produit, quantite) VALUES (?, ?, ?)";
        $stmt_insert_commande_details = $conn->prepare($sql_insert_commande_details);

        while ($row_produit = $result_panier_produits->fetch_assoc()) {
            $product_id = $row_produit["product_id"];
            $quantite = $row_produit["quantite"];
            $stmt_insert_commande_details->bind_param("iii", $commande_id, $product_id, $quantite);
            $stmt_insert_commande_details->execute();
        }

        // Supprimer les produits du panier après avoir passé la commande
        
        
        // Redirection vers voir_panier.php
        header("Location: voir_panier.php");
        exit();
    } else {
        echo "Aucun client trouvé avec cet email : $email";
    }
} else {
    echo "Aucun email d'utilisateur trouvé en session.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
