<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION["email"])) {
    http_response_code(401);
    exit("Utilisateur non authentifié.");
}
 $t= $_SESSION['audio_path'];



$expediteur = $_SESSION["email"];
$expediteur = $_SESSION["email"];

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    http_response_code(500);
    exit("Erreur de connexion à la base de données: " . $conn->connect_error);
}

// Initialiser les variables pour les fichiers
$message_text = null;
$audio_path = null;
$image_path = null;
$date_prefix = date("YmdHis") . '.' . $expediteur;

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

// Récupérer les autres informations du formulaire
$destinataire = $_POST["email_v"];
$lu = 0;

// Vérifier si une conversation entre l'expéditeur et le destinataire existe
$sql_check_conversation = "SELECT id FROM conversations WHERE (utilisateur1 = ? AND utilisateur2 = ?) OR (utilisateur1 = ? AND utilisateur2 = ?)";
$stmt_check_conversation = $conn->prepare($sql_check_conversation);

if (!$stmt_check_conversation) {
    http_response_code(500);
    exit("Erreur de préparation de la requête: " . $conn->error);
}

$stmt_check_conversation->bind_param("ssss", $expediteur, $destinataire, $destinataire, $expediteur);
$stmt_check_conversation->execute();
$stmt_check_conversation->store_result();
$row_count = $stmt_check_conversation->num_rows;
$stmt_check_conversation->bind_result($conversation_id);
$stmt_check_conversation->fetch();

if ($row_count > 0) {
    // La conversation existe
    $sql_message = "INSERT INTO messages (conversation_id, expediteur, destinataire, contenu, lu) VALUES (?, ?, ?, ?, ?)";
    $stmt_message = $conn->prepare($sql_message);

    if (!$stmt_message) {
        http_response_code(500);
        exit("Erreur de préparation de la requête: " . $conn->error);
    }

    $stmt_message->bind_param("isssi", $conversation_id, $expediteur, $destinataire, $message, $lu);
    if ($stmt_message->execute()) {
        $sql_update_conversation = "UPDATE conversations SET dernier_message = ?, date_dernier_message = NOW(), utilisateur1_delete = ?, utilisateur2_delete = ? WHERE id = ?";
        $stmt_update_conversation = $conn->prepare($sql_update_conversation);
        
        if (!$stmt_update_conversation) {
            http_response_code(500);
            exit("Erreur de préparation de la requête: " . $conn->error);
        }
        
        $d = 0;
        $stmt_update_conversation->bind_param("siii", $message, $d, $d, $conversation_id);
        if ($stmt_update_conversation->execute()) {
            http_response_code(200);
            echo "Message envoyé avec succès.";
        } else {
            http_response_code(500);
            echo "Erreur lors de la mise à jour de la conversation: " . $conn->error;
        }

        $stmt_update_conversation->close();
    } else {
        http_response_code(500);
        echo "Erreur lors de l'envoi du message: " . $conn->error;
    }

    $stmt_message->close();
} else {
    // La conversation n'existe pas
    $sql_conversation = "INSERT INTO conversations (utilisateur1, utilisateur2, dernier_message, date_dernier_message) VALUES (?, ?, ?, NOW())";
    $stmt_conversation = $conn->prepare($sql_conversation);

    if (!$stmt_conversation) {
        http_response_code(500);
        exit("Erreur de préparation de la requête: " . $conn->error);
    }

    $stmt_conversation->bind_param("sss", $expediteur, $destinataire, $message);
    if ($stmt_conversation->execute()) {
        $conversation_id = $stmt_conversation->insert_id;

        $sql_message = "INSERT INTO messages (conversation_id, expediteur, destinataire, contenu, lu) VALUES (?, ?, ?, ?, ?)";
        $stmt_message = $conn->prepare($sql_message);

        if (!$stmt_message) {
            http_response_code(500);
            exit("Erreur de préparation de la requête: " . $conn->error);
        }

        $stmt_message->bind_param("isssi", $conversation_id, $expediteur, $destinataire, $message, $lu);
        if ($stmt_message->execute()) {
            http_response_code(200);
            echo "Message envoyé avec succès.";
        } else {
            http_response_code(500);
            echo "Erreur lors de l'insertion de la conversation: " . $conn->error;
        }

        $stmt_message->close();
    } else {
        http_response_code(500);
        echo "Erreur lors de l'envoi du message: " . $conn->error;
    }

    $stmt_conversation->close();
}

$stmt_check_conversation->close();
$conn->close();
?>