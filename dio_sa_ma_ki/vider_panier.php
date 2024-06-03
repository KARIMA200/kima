


<?php
// Démarrer la session
session_start();

// Vérifier si l'email de l'utilisateur est stocké en session
if (isset($_SESSION['email'])) {
    // Récupérer l'email de l'utilisateur
    $email = $_SESSION['email'];

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ecommerce";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Requête pour obtenir l'ID du client à partir de son email
    $sql_client_id = "SELECT id FROM clients WHERE email = ?";
    $stmt_client_id = $conn->prepare($sql_client_id);
    $stmt_client_id->bind_param("s", $email);
    $stmt_client_id->execute();
    $result_client_id = $stmt_client_id->get_result();

    if ($result_client_id->num_rows > 0) {
        // Récupérer l'ID du client
        $row = $result_client_id->fetch_assoc();
        $user_id = $row["id"];

        // Requête pour supprimer les produits du panier pour ce client
        $sql_delete_panier = "DELETE FROM panier WHERE user_id = ?";
        $stmt_delete_panier = $conn->prepare($sql_delete_panier);
        $stmt_delete_panier->bind_param("i", $user_id);
        
        if ($stmt_delete_panier->execute()) {
            echo "Les produits du panier ont été supprimés avec succès pour l'utilisateur avec l'email : $email";
        } else {
            echo "Erreur lors de la suppression des produits du panier.";
        }
    } else {
        echo "Aucun client trouvé avec cet email : $email";
    }

    // Fermer la connexion à la base de données
    $conn->close();
} else {
    echo "Aucun email d'utilisateur trouvé en session.";
}
?>
