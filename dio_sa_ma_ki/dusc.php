<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoyer un message</title>
    <link rel="stylesheet" href="css/all.min.css">
    <style>
        /* Votre CSS existant */
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <div class="user-avatar">
                <?php
                // Connexion à la base de données
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "ecommerce";
                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Récupération de l'ID du vendeur
                $id_du_produit = $_POST['produit_id'];
                $stmt = $conn->prepare("SELECT vendeur_id FROM produits_vendeurs WHERE produit_id = ?");
                $stmt->bind_param("i", $id_du_produit);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $id_du_vendeur = $row['vendeur_id'];
                } else {
                    echo "Aucun vendeur trouvé pour le produit avec l'ID $id_du_produit.";
                    exit;
                }
                $stmt->close();

                // Récupérer les informations du vendeur
                $sql = "SELECT nom, prenom, user_image, email FROM vendeurs WHERE id = '$id_du_vendeur'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $nom_vendeur = $row['nom'];
                    $prenom_vendeur = $row['prenom'];
                    $email_v = $row['email'];
                    $image_path = "image/" . $row['user_image'];
                    if (file_exists($image_path)) {
                        if (!is_readable($image_path)) {
                            chmod($image_path, 0644);
                        }
                        echo '<img src="' . $image_path . '" alt="' . $row['nom'] . '">';
                    } else {
                        echo '<img src="chemin_vers_image_par_defaut/default_image.jpg" alt="Image par défaut">';
                    }
                    $vendeur_nom = "$nom_vendeur";
                    $vendeur_prenom = "$prenom_vendeur";
                }
                ?>
            </div>
            <span><?php echo $vendeur_nom . " " . $vendeur_prenom; ?></span>
        </div>
        <div class="chat-messages">
            <!-- Messages à afficher seront ajoutés ici via JavaScript -->
        </div>
        <div class="chat-input">
            <form id="messageForm" action="upload1.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="123">
                <input type="hidden" name="vendeur_id" value="<?php echo $id_du_vendeur; ?>">
                <input type="hidden" name="email_v" value="<?php echo $email_v; ?>">
                <textarea id="messageInput" name="message" placeholder="Type your message..."></textarea>
                <div id="recordingsList"></div>        
                <i id="startRecording" class="fas fa-microphone"></i>
                <i id="image" class="fa-solid fa-file"></i>
                <input type="file" name="audio" id="audioInput" accept="audio/*" style="display: none;">
                <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;">
                <button id="sendRecording" type="submit">Send</button>
            </form>
        </div>
    </div>
    <script>
        // Votre JavaScript existant pour la gestion des enregistrements audio et des images
    </script>
</body>
</html>

<?php
$conn->close();
?>
