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

        // Récupérer les quantités des produits envoyés par POST
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'quantite_') === 0) {
                $product_id = str_replace('quantite_', '', $key);
                $quantite = intval($value);

                $sql_update_panier = "UPDATE panier SET quantite = ? WHERE product_id = ? AND user_id = ?";
                $stmt_update_panier = $conn->prepare($sql_update_panier);
                $stmt_update_panier->bind_param("iii", $quantite, $product_id, $user_id);
                $stmt_update_panier->execute();
            }
        }

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
