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

// Récupérer l'ID du commentaire à partir de la requête GET
$comment_id = $_GET['id'];

// Requête pour obtenir la liste des utilisateurs qui ont cliqué sur ce commentaire
$sql_users = "SELECT user_email FROM clics_utilisateurs WHERE comment_id = ?";
$stmt_users = $conn->prepare($sql_users);
$stmt_users->bind_param("i", $comment_id);
$stmt_users->execute();
$result_users = $stmt_users->get_result();

if ($result_users->num_rows > 0) {
    while ($row_user = $result_users->fetch_assoc()) {
        $user_email = $row_user['user_email'];

        // Requête pour obtenir les informations sur l'utilisateur à partir de son email
        $sql_user_info = "SELECT nom, prenom, user_image FROM vendeurs WHERE email = ? UNION ALL SELECT nom, prenom, user_image FROM clients WHERE email = ?";
        $stmt_user_info = $conn->prepare($sql_user_info);
        $stmt_user_info->bind_param("ss", $user_email, $user_email);
        $stmt_user_info->execute();
        $result_user_info = $stmt_user_info->get_result();

        if ($result_user_info->num_rows > 0) {
            while ($row_info = $result_user_info->fetch_assoc()) {
                // Afficher les informations sur l'utilisateur
                $nom = $row_info['nom'];
                $prenom = $row_info['prenom'];
                $user_image = $row_info['user_image'];

                echo '<div style="display: flex; align-items: center; margin-bottom: 10px;">';
                echo '<a href="pro.php?email=' . $user_email . '">';
                echo '<img src="image/' . $user_image . '" alt="User Image" style="width: 1cm; height: 1cm; border-radius: 50%; margin-right: 5px;">';
                echo '</a>';
                echo '<p>' . $prenom . ' ' . $nom . '</p>';
                echo '</div>';
            }
        }
    }
} else {
    echo "Aucun utilisateur n'a cliqué sur ce commentaire.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
