<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";
session_start();
$email = $_SESSION['email'];
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si les données nécessaires sont présentes dans la requête POST
if (isset($_POST['comment_id']) && isset($_POST['response'])) {
    $comment_id = $_POST['comment_id'];
    $response = $_POST['response'];

    // Échapper les caractères spéciaux pour éviter les injections SQL
    $comment_id = $conn->real_escape_string($comment_id);
    $response = $conn->real_escape_string($response);

    // Requête SQL pour insérer la réponse
    $sql = "INSERT INTO reponses_commentaires (commentaire_id, email, reponse) VALUES ('$comment_id', '$email' , '$response')";

    if ($conn->query($sql) === TRUE) {
        // Mettre à jour le nombre de réponses du commentaire
        $update_sql = "UPDATE commentaires SET nombre_reponses = nombre_reponses + 1 WHERE id = '$comment_id'";
        if ($conn->query($update_sql) === TRUE) {
            // Rechercher le nom et prénom de l'utilisateur connecté
            $sql_user_info = "SELECT nom, prenom FROM clients WHERE email = '$email' UNION SELECT nom, prenom FROM vendeurs WHERE email = '$email'";
            $result_user_info = $conn->query($sql_user_info);
            if ($result_user_info->num_rows > 0) {
                $row_user_info = $result_user_info->fetch_assoc();
                $user_nom = $row_user_info['nom'];
                $user_prenom = $row_user_info['prenom'];
            }

            // Rechercher l'email du propriétaire du commentaire
            $sql_comment_owner = "SELECT email FROM commentaires WHERE id = '$comment_id'";
            $result_comment_owner = $conn->query($sql_comment_owner);
            if ($result_comment_owner->num_rows > 0) {
                $row_comment_owner = $result_comment_owner->fetch_assoc();
                $comment_owner_email = $row_comment_owner['email'];
            }

            // Insérer dans la table notification
            if (isset($user_nom) && isset($user_prenom) && isset($comment_owner_email)) {
                $notification = "$user_nom $user_prenom a répondu sur votre commentaire";
                $sql_notification = "INSERT INTO notifications (user_1, user_2, notification, comment_id) VALUES ('$email', '$comment_owner_email', '$notification', '$comment_id')";
                if ($conn->query($sql_notification) === TRUE) {
                    echo "Réponse ajoutée avec succès et le nombre de réponses mis à jour. Notification ajoutée.";
                } else {
                    echo "Erreur lors de l'insertion de la notification: " . $conn->error;
                }
            }
        } else {
            echo "Erreur lors de la mise à jour du nombre de réponses: " . $conn->error;
        }
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Erreur: données nécessaires non fournies.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
