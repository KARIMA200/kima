<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs favoris</title>
    <style>
        .user-container {
            width: 5cm;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .user-container img {
            width: 1cm;
            height: 1cm;
            margin-right: 5px;
            border-radius: 50%;
            cursor: pointer; /* Ajout du curseur pointer */
        }
    </style>
</head>
<body>
    <div class="container">
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

        // Récupérer l'ID du produit à partir de la requête GET
        $product_id = $_GET['id'];

        // Requête pour obtenir la liste des utilisateurs qui ont ajouté ce produit à leurs favoris
        $sql_favoris = "SELECT user_email FROM favoris WHERE product_id = ?";
        $stmt_favoris = $conn->prepare($sql_favoris);
        $stmt_favoris->bind_param("i", $product_id);
        $stmt_favoris->execute();
        $result_favoris = $stmt_favoris->get_result();

        if ($result_favoris->num_rows > 0) {
            while ($row_favoris = $result_favoris->fetch_assoc()) {
                // Requête pour obtenir les informations sur l'utilisateur à partir de son email dans la table clients
                $user_email = $row_favoris['user_email'];
                $sql_user_info_clients = "SELECT nom, prenom, email, user_image FROM clients WHERE email = ?";
                $stmt_user_info_clients = $conn->prepare($sql_user_info_clients);
                $stmt_user_info_clients->bind_param("s", $user_email);
                $stmt_user_info_clients->execute();
                $result_user_info_clients = $stmt_user_info_clients->get_result();

                // Requête pour obtenir les informations sur l'utilisateur à partir de son email dans la table vendeurs
                $sql_user_info_vendeurs = "SELECT nom, prenom, email, user_image FROM vendeurs WHERE email = ?";
                $stmt_user_info_vendeurs = $conn->prepare($sql_user_info_vendeurs);
                $stmt_user_info_vendeurs->bind_param("s", $user_email);
                $stmt_user_info_vendeurs->execute();
                $result_user_info_vendeurs = $stmt_user_info_vendeurs->get_result();

                if ($result_user_info_clients->num_rows > 0) {
                    $row_user_info = $result_user_info_clients->fetch_assoc();
                } elseif ($result_user_info_vendeurs->num_rows > 0) {
                    $row_user_info = $result_user_info_vendeurs->fetch_assoc();
                }

                if (!empty($row_user_info)) {
                    $nom = $row_user_info['nom'];
                    $prenom = $row_user_info['prenom'];
                    $user_image = $row_user_info['user_image'];
                    $user_email = $row_user_info['email'];

                    // Afficher les informations sur l'utilisateur
                    echo '<div class="user-container">';
                    // Ajout du lien sur l'image
                    echo '<a href="pro.php?email=' . urlencode($user_email) . '"><img src="image/' . $user_image . '" alt="User Image"></a>';
                    echo '<p>' . $prenom . ' ' . $nom . '</p>';
                    echo '</div>';
                }
            }
        } else {
            echo "Aucun utilisateur n'a ajouté ce produit à ses favoris.";
        }

        // Fermer la connexion à la base de données
        $conn->close();
        ?>
    </div>
</body>
</html>

    </div>
</body>
</html>
