<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";
session_start(); // Démarrer la session
$utilisateur_email=$_SESSION['email'];
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si un identifiant de produit est présent dans la requête POST
if (isset($_POST['produit_id'])) {
    // Récupérer l'identifiant du produit depuis la requête POST
    $produit_id = $_POST['produit_id'];

    // Requête SQL pour récupérer tous les commentaires associés à ce produit
    $sql = "SELECT * FROM commentaires WHERE produit_id = $produit_id";
    $result = $conn->query($sql);
} elseif (isset($_GET['id_notification'])) {
    // Récupérer l'identifiant de la notification depuis la requête GET
    $id_notification = $_GET['id_notification'];

    // Requête SQL pour récupérer le comment_id associé à la notification
    $comment_id_sql = "SELECT comment_id FROM notifications WHERE id = $id_notification";
    $comment_id_result = $conn->query($comment_id_sql);

    if ($comment_id_result->num_rows > 0) {
        $comment_id_row = $comment_id_result->fetch_assoc();
        $comment_id = $comment_id_row['comment_id'];

        // Requête SQL pour récupérer les commentaires associés à ce comment_id
        $sql = "SELECT * FROM commentaires WHERE id = $comment_id";
        $result = $conn->query($sql);
    } else {
        echo "Aucun commentaire trouvé pour cette notification.";
    }
} else {
    echo "Erreur: Aucun identifiant de produit ou de notification fourni.";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commentaires hg du produit</title>
    <!-- Inclure les styles CSS -->
    <link rel="stylesheet" href="css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .comment {
    background-color: #4bcdA2; /* Changement de la couleur de fond pour les commentaires */
    border: 2px solid #155724; /* Ajout d'une bordure verte */
    padding: 10px;
    margin-bottom: 20px;
}

.response {
    background-color: #DB7093; /* Changement de la couleur de fond pour les réponses */
    border: 2px solid #721C24; /* Ajout d'une bordure rouge */
    padding: 10px;
    margin-bottom: 10px;
    margin-left: 50px; /* Ajustement de la marge pour les réponses */
}

        .comment img, .response img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            float: left;
        }
        .user-name {
            font-weight: bold;
            font-size: 1.1em;
            color: #333;
            margin-top: 8px;
        }
        .content {
            margin-top: 10px;
            color: #333;
            clear: both;
        }
        .actions {
            margin-top: 10px;
            color: #666;
            display: flex;
            align-items: center;
        }
        .actions span {
            margin-right: 15px;
        }
        .heart-form, .reply-form, .delete-form {
            display: inline;
        }
        .heart-button, .comment-button, .delete-button {
            background: none;
            border: none;
            cursor: pointer;
            color: #007BFF;
            font-size: 1.2em;
        }
        .heart-button:hover, .comment-button:hover, .delete-button:hover {
            color: #0056b3;
        }
        .reply-form {
            display: none;
            margin-top: 10px;
        }
        .reply-form textarea {
            width: calc(100% - 50px);
            margin-right: 10px;
        }
        .submit-button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
        .response-user {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        .image-icon {
            cursor: pointer;
            font-size: 1.5em;
            margin-left: 10px;
        }
        .image-icon:hover {
            color: #007BFF;
        }.actions span {
    margin-right: 5px; /* Réduit la marge pour rapprocher le nombre de l'icône */
    display: inline-flex; /* Permet aux nombres d'être affichés en ligne avec les icônes */
    align-items: center; /* Centre verticalement les icônes et les nombres */
}

.actions span.clickCount {
    margin-left: 3px; /* Ajoute une petite marge entre l'icône et le nombre */
}
    </style>
</head>
<body>
    <h1></h1>
<div class="container">
    <!-- Ici seront affichés les commentaires -->
    <?php
    // Afficher les commentaires
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='comment'>";
            // Afficher l'image de l'utilisateur
            echo "<a href='pro.php?email={$row['email']}'><img class='petite-image' src='image/{$row['image']}' alt='{$row['nom']}'></a>";

            // Afficher le nom et prénom de l'utilisateur dans le même conteneur
            echo "<div class='user-name'>{$row['prenom']} {$row['nom']}</div>";
            echo "<div class='content'>{$row['commentaire']}</div>";
            // Afficher les icônes pour les actions (inspirées de Facebook)
            echo '<div class="actions">';
            echo '<a href="jaime_com.php?id=' . $row['id'] . '">' . '<span class="clickCount">' . $row['nombre_clics'] . '</span>' . '</a>';
            echo '<form action="update_clicks.php" method="GET" class="heart-form">';
            echo '  <input type="hidden" name="comment_id" value="' . $row['id'] . '">';
            echo '  <button type="submit" class="heart-button">';
            echo '      <i class="fas fa-heart"></i>';
            echo '  </button>';
            echo '</form>';

            echo '<span class="clickCount">' . $row['nombre_reponses'] . '</span>';
            echo '<button type="button" class="comment-button" onclick="toggleResponses(' . $row['id'] . ')">';
            echo '      <i class="fa-solid fa-comment"></i>';
            echo '</button> </div>';
            echo '<div id="responses-' . $row['id'] . '" style="display: none;">';
        
            $responses_sql = "SELECT reponses_commentaires.*, IFNULL(vendeurs.user_image, clients.user_image) AS user_image, IFNULL(vendeurs.nom, clients.nom) AS nom, IFNULL(vendeurs.prenom, clients.prenom) AS prenom FROM reponses_commentaires LEFT JOIN vendeurs ON reponses_commentaires.email = vendeurs.email LEFT JOIN clients ON reponses_commentaires.email = clients.email WHERE commentaire_id = {$row['id']}";
            $responses_result = $conn->query($responses_sql);
            if ($responses_result->num_rows > 0) {
                while ($response_row = $responses_result->fetch_assoc()) {
                    echo "<div class='response'>";
                    echo "<a href='page_php.php?email={$response_row['email']}'><img src='image/{$response_row['user_image']}' alt='{$response_row['prenom']} {$response_row['nom']}'></a>";

                    echo "<div class='response-content'>{$response_row['reponse']}</div>";
                    echo "<div class='response-user'>Répondu par: {$response_row['prenom']} {$response_row['nom']}</div>";
                    echo "</div>";
                }
            }
            echo '</div>';

            // Afficher le formulaire de réponse
            echo '<form class="reply-form" action="repondre_commentaire.php" method="POST">';
            echo '<textarea name="response" placeholder="Répondre au commentaire"></textarea>';
            echo '<input type="hidden" name="comment_id" value="' . $row['id'] . '">';
            echo '<button type="submit" class="submit-button">Envoyer</button>';
            echo '</form>';

            // Afficher l'icône de corbeille si l'utilisateur connecté est l'auteur du commentaire
            if ($row['email'] == $utilisateur_email) {
                echo '<form action="supprimer_commentaire.php" method="POST" class="delete-form">';
                echo '<input type="hidden" name="comment_id" value="' . $row['id'] . '">';
                echo '<button type="submit" class="delete-button">';
                echo '<i class="fa-solid fa-trash"></i>';
                echo '</button>';
                echo '</form>';
            }

            echo '</div>'; // Fermeture du div .comment
        }
    } else {
        echo "Aucun commentaire pour ce produit.";
    }
    ?>
    <!-- Formulaire pour ajouter un nouveau commentaire -->
    <form action="ajouter_commentaire.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="produit_id" value="<?php echo $produit_id; ?>">
        <textarea name="commentaire" placeholder="Écrire un commentaire"
        <textarea name="commentaire" placeholder="Écrire un commentaire" style="width: calc(100% - 40px); margin-right: 10px;"></textarea>
        <label for="file-upload" class="image-icon"><i class="fa fa-camera"></i></label>
        <input id="file-upload" type="file" name="image" accept="image/*" style="display: none;">
        <button type="submit" class="submit-button">Commenter</button>
    </form>
</div>
<!-- Script JavaScript pour afficher/masquer les réponses et le formulaire de réponse -->
<script>
function toggleResponses(commentId) {
    var responses = document.getElementById('responses-' + commentId);
    var replyForm = document.querySelector(`.reply-form input[name="comment_id"][value="${commentId}"]`).parentNode;
    if (responses.style.display === 'none' || responses.style.display === '') {
        responses.style.display = 'block';
        replyForm.style.display = 'block';
    } else {
        responses.style.display = 'none';
        replyForm.style.display = 'none';
    }
}
</script>
</body>
</html>
<?php


// Fermer la connexion à la base de données
$conn->close();
?>
