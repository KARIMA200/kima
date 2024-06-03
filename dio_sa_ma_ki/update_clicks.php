<?php
// Vérifier si la requête GET contient les données nécessaires
if (isset($_GET['comment_id'])) {
    // Récupérer les données de la requête GET
    $commentId = $_GET['comment_id'];

    // Récupérer l'email de l'utilisateur depuis la session
    session_start();
    $email = $_SESSION['email'];

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

    // Vérifier si l'utilisateur a déjà cliqué sur ce commentaire
    $sql = "SELECT * FROM clics_utilisateurs WHERE user_email = '$email' AND comment_id = $commentId";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        // L'utilisateur n'a pas encore cliqué sur ce commentaire, donc nous pouvons mettre à jour le nombre de clics
        $sql_update = "UPDATE commentaires SET nombre_clics = nombre_clics + 1 WHERE id = $commentId";

        if ($conn->query($sql_update) === TRUE) {
            // Enregistrer le clic de l'utilisateur dans la table clics_utilisateurs
            $sql_insert = "INSERT INTO clics_utilisateurs (user_email, comment_id) VALUES ('$email', $commentId)";
            $conn->query($sql_insert);

            // Récupérer les informations sur le commentaire pour l'insertion dans la table de notifications
            $sql_commentaire = "SELECT email FROM commentaires WHERE id = $commentId";
            $result_commentaire = $conn->query($sql_commentaire);
            $row_commentaire = $result_commentaire->fetch_assoc();
            $id_utilisateur_commentaire = $row_commentaire['email'];

            // Récupérer les informations sur l'utilisateur qui a commenté
            $sql_utilisateur = "";
            $type_utilisateur = ""; // Définir le type d'utilisateur (client ou vendeur)

            // Vérifier si l'utilisateur est un client ou un vendeur
            $sql_client = "SELECT nom, prenom FROM clients WHERE email = '$email'";
            $result_client = $conn->query($sql_client);
            if ($result_client->num_rows > 0) {
                $row_client = $result_client->fetch_assoc();
                $nom_utilisateur = $row_client['nom'];
                $prenom_utilisateur = $row_client['prenom'];
                $type_utilisateur = "Client";
            } else {
                // Si l'utilisateur n'est pas un client, vérifier s'il est un vendeur
                $sql_vendeur = "SELECT nom, prenom FROM vendeurs WHERE email = '$email'";
                $result_vendeur = $conn->query($sql_vendeur);
                if ($result_vendeur->num_rows > 0) {
                    $row_vendeur = $result_vendeur->fetch_assoc();
                    $nom_utilisateur = $row_vendeur['nom'];
                    $prenom_utilisateur = $row_vendeur['prenom'];
                    $type_utilisateur = "Vendeur";
                }
            }

            // Insérer dans la table de notifications si l'utilisateur n'est pas lui-même l'auteur du commentaire
            if ($email != $id_utilisateur_commentaire) {
                // Vérifier si une notification similaire existe déjà
                $sql_verification_notification = "SELECT * FROM notifications WHERE user_1 = '$email' AND user_2 = '$id_utilisateur_commentaire' AND comment_id = $commentId";
                $result_verification_notification = $conn->query($sql_verification_notification);
                if ($result_verification_notification->num_rows == 0) {
                    // Insertion dans la table de notifications
                    $sql_insert_notification = "INSERT INTO notifications (user_1, user_2, notification, comment_id) VALUES ('$email', '$id_utilisateur_commentaire', '$nom_utilisateur $prenom_utilisateur a ajouté une favori sur votre commentaire', $commentId)";
                    $conn->query($sql_insert_notification);
                }
            }

            echo "Le nombre de clics a été mis à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour du nombre de clics: " . $conn->error;
        }
    } else {
        // L'utilisateur a déjà cliqué sur ce commentaire, donc ne rien faire
        echo "L'utilisateur a déjà cliqué sur ce commentaire.";
    }

    // Fermer la connexion à la base de données
    $conn->close();
} else {
    echo "Erreur : données manquantes.";
}
?>
