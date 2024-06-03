<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    die("Vous n'êtes pas connecté.");
}

// Récupérer l'email de la session
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

// Rechercher l'utilisateur dans la table utilisateurs
$sql_user = "SELECT * FROM utilisateurs WHERE email = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $row_user = $result_user->fetch_assoc();
    $type_utilisateur = $row_user['type'];

    // Déterminer dans quelle table mettre à jour le champ user_image
    $table = '';
    if ($type_utilisateur == 'vendeur') {
        $table = 'vendeurs';
    } elseif ($type_utilisateur == 'client') {
        $table = 'clients';
    }

    // Mettre à jour le champ user_image dans la table appropriée
    if (!empty($table)) {
        // Vérifier si un fichier a été téléversé
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            // Répertoire de destination pour sauvegarder l'image
            $target_dir = "image/";

            // Générer un nom de fichier unique avec l'email de l'utilisateur et la date actuelle
            $image_name = $email . "_" . date("Y-m-d_H-i-s") . "_" . basename($_FILES['image']['name']);
            $target_file = $target_dir . $image_name;

            // Si tout est correct, téléverser le fichier
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Mettre à jour le chemin de l'image dans la base de données
                $image_path = $image_name;
                $sql_update = "UPDATE $table SET user_image = ? WHERE email = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ss", $image_path, $email);

                if ($stmt_update->execute()) {
                    echo "Image téléversée avec succès.";
                } else {
                    echo "Erreur lors de la mise à jour de l'image dans la base de données: " . $conn->error;
                }
            } else {
                echo "Désolé, une erreur s'est produite lors du téléversement de votre fichier.";
            }
        } else {
            echo "Aucun fichier sélectionné.";
        }
    } else {
        echo "Type d'utilisateur non pris en charge.";
    }
} else {
    echo "Utilisateur non trouvé.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
