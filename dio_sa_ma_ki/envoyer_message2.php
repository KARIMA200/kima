<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION["email"])) {
    header("Location: login.php"); // Redirection vers la page de connexion si non connecté
    exit;
}
$t= $_SESSION['audio_path'];

// Récupération de l'email de l'utilisateur à partir de la session
$utilisateur1 = $_SESSION["email"];

// Vérification si l'autre utilisateur et le message sont envoyés par POST
if (isset($_POST["other_user"]) && isset($_POST["message"])) {
    // Récupération de l'autre utilisateur et du message à partir de POST
    $utilisateur2 = $_POST["other_user"];
    $message = $_POST["message"];
    
// Initialiser les variables pour les fichiers
$message_text = null;
$audio_path = null;
$image_path = null;
$date_prefix = date("YmdHis") . '.' . $utilisateur1;

// Vérifier si un message texte a été envoyé
if (isset($_POST['message']) && !empty(trim($_POST['message']))) {
    $message_text = $_POST['message'];
}

// Vérifier si un fichier audio a été téléchargé
if (isset($_FILES['audio'])) {
    if ($_FILES['audio']['error'] == UPLOAD_ERR_OK) {
        $audio_directory = "uploads/";
        $audio_extension = pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION);
        $audio_file = $audio_directory . $date_prefix . '.' . $audio_extension;
        if (move_uploaded_file($_FILES['audio']['tmp_name'], $audio_file)) {
            $audio_path = $audio_file;
            $_SESSION['audio_path'] = $audio_path; // Stocker le chemin dans la variable de session
        }
    }
}

// Vérifier si une image a été téléchargée
// Vérifier si une image a été téléchargée
if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $image_directory = "images/";
    $image_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_file = $date_prefix . '_' . $expediteur . '.' . $image_extension; // Nom du fichier avec date et email
    $image_path = $image_directory . $image_file; // Chemin complet du fichier

    if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
        // Image déplacée avec succès, pas besoin de stocker dans la session
    } else {
        echo "Erreur lors du déplacement de l'image.";
    }
}







// Déterminer le contenu à insérer dans la base de données
$message = $message_text ? $message_text : ($image_path ? $image_path : ($t ? $t : null));

if ($message === null) {
    die("Aucun contenu à insérer dans la base de données.");
}

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ecommerce";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("Erreur de connexion à la base de données: " . $conn->connect_error);
    }

    // Requête SQL pour sélectionner l'ID de la conversation
    $sql = "SELECT id FROM conversations WHERE (utilisateur1 = '$utilisateur1' AND utilisateur2 = '$utilisateur2') OR (utilisateur1 = '$utilisateur2' AND utilisateur2 = '$utilisateur1')";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Récupération de l'ID de la conversation
        $row = $result->fetch_assoc();
        $conversation_id = $row["id"];

        // Requête d'insertion du message
        $insert_sql = "INSERT INTO messages (expediteur, destinataire, contenu, lu, conversation_id) 
                       VALUES ('$utilisateur1', '$utilisateur2', '$message', 0, '$conversation_id')";

        if ($conn->query($insert_sql) === TRUE) {
            // Mettre à jour la table de conversations
            $update_conversation_sql = "UPDATE conversations 
                                        SET dernier_message = '$message', date_dernier_message = NOW() 
                                        WHERE id = '$conversation_id'";

            if ($conn->query($update_conversation_sql) === TRUE) {
                echo "Message envoyé avec succès.";
            } else {
                echo "Erreur lors de la mise à jour de la conversation: " . $conn->error;
            }
        } else {
            echo "Erreur lors de l'envoi du message: " . $conn->error;
        }
    } else {
        echo "Conversation introuvable.";
    }

    // Fermeture de la connexion à la base de données
    $conn->close();
} else {
    // Rediriger vers une page d'erreur si l'autre utilisateur ou le message n'est pas fourni
    echo "ërreur";
    exit;
}
?>
