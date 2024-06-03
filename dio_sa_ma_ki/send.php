<?php
session_start();

// Vérifiez si l'utilisateur est connecté
$expediteur = $_SESSION["email"];



// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);
;
// Vérification de la connexion

    // Vérifier si un message texte a été envoyé
    if(isset($_POST['message'])) {
        // Récupérer le message texte
        $message_text = $_POST['message'];
    
        // Traitement du message texte...
        echo "Message texte : " . $message_text;
    }
    
    // Vérifier si un fichier audio a été téléchargé
    if(isset($_FILES['audio'])) {
        // Chemin du répertoire pour les fichiers audio
        $audio_directory = "uploads/";
    
        // Chemin où enregistrer le fichier audio
        $audio_file = $audio_directory . basename($_FILES['audio']['name']);
    
        // Déplacer le fichier audio téléchargé vers sa destination
        if(move_uploaded_file($_FILES['audio']['tmp_name'], $audio_file)) {
            // Fichier audio téléchargé avec succès
    
            // Stocker le nom du fichier audio dans une variable
            $message = basename($_FILES['audio']['name']);
    
            // Ensuite, vous pouvez utiliser $audio_name comme vous le souhaitez
    
            // Exemple d'insertion dans la base de données pour le fichier audio
            // $sql = "INSERT INTO votre_table (nom_audio) VALUES ('$audio_name')";
            // Executez votre requête SQL ici
        } else {
            // Erreur lors du déplacement du fichier audio
            echo "Une erreur s'est produite lors du téléchargement du fichier audio.";
        }
    }
    
    // Vérifier si une image a été téléchargée
    if(isset($_FILES['image'])) {
        // Chemin du répertoire pour les images
        $image_directory = "image/";
    
        // Chemin où enregistrer l'image téléchargée
        $image_file = $image_directory . basename($_FILES['image']['name']);
    
        // Déplacer l'image téléchargée vers sa destination
        if(move_uploaded_file($_FILES['image']['tmp_name'], $image_file)) {
            // Image téléchargée avec succès
    
            // Stocker le nom de l'image dans une variable
            $message = basename($_FILES['image']['name']);
    
            // Ensuite, vous pouvez utiliser $image_name comme vous le souhaitez
    
            // Exemple d'insertion dans la base de données pour l'image
            // $sql = "INSERT INTO votre_table (nom_image) VALUES ('$image_name')";
            // Executez votre requête SQL ici
        } else {
            // Erreur lors du déplacement de l'image
            echo "Une erreur s'est produite lors du téléchargement de l'image.";
        }
    }
    
    

// Récupérez les données du message et du destinataire

$destinataire = $_POST["email_v"];
$lu = 0;

// Connexion à la base de données


$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion à la base de données
if ($conn->connect_error) {
    http_response_code(500);
    exit("Erreur de connexion à la base de données: " . $conn->connect_error);
}

// Vérifier si une conversation entre l'expéditeur et le destinataire existe dans la table conversations
$sql_check_conversation = "SELECT id FROM conversations WHERE (utilisateur1 = ? AND utilisateur2 = ?) OR (utilisateur1 = ? AND utilisateur2 = ?)";
$stmt_check_conversation = $conn->prepare($sql_check_conversation);

// Vérification de la préparation de la requête
if (!$stmt_check_conversation) {
    http_response_code(500);
    exit("Erreur de préparation de la requête: " . $conn->error);
}

// Liaison des paramètres et exécution de la requête pour vérifier l'existence de la conversation
$stmt_check_conversation->bind_param("ssss", $expediteur, $destinataire, $destinataire, $expediteur);
$stmt_check_conversation->execute();
$stmt_check_conversation->store_result();
$row_count = $stmt_check_conversation->num_rows;
$stmt_check_conversation->bind_result($conversation_id);
$stmt_check_conversation->fetch();

// Vérifier le résultat de la requête
if ($row_count > 0) {
    // La conversation existe, insérez le message dans la table messages uniquement
    // Préparez la requête SQL pour insérer le message dans la base de données
    $sql_message = "INSERT INTO messages (conversation_id, expediteur, destinataire, contenu, lu) VALUES (?, ?, ?, ?, ?)";
    $stmt_message = $conn->prepare($sql_message);

    // Vérification de la préparation de la requête
    if (!$stmt_message) {
        http_response_code(500);
        exit("Erreur de préparation de la requête: " . $conn->error);
    }

    // Liaison des paramètres et exécution de la requête pour insérer le message
    $stmt_message->bind_param("isssi", $conversation_id, $expediteur, $destinataire, $message, $lu);
    if ($stmt_message->execute()) {
        // Mettre à jour le dernier message et sa date dans la table des conversations
        $sql_update_conversation = "UPDATE conversations SET dernier_message = ?, date_dernier_message = NOW() WHERE id = ?";
        $stmt_update_conversation = $conn->prepare($sql_update_conversation);

        // Vérification de la préparation de la requête
        if (!$stmt_update_conversation) {
            http_response_code(500);
            exit("Erreur de préparation de la requête: " . $conn->error);
        }

        // Liaison des paramètres et exécution de la requête pour mettre à jour la conversation
        $stmt_update_conversation->bind_param("si", $message, $conversation_id);
        if ($stmt_update_conversation->execute()) {
            http_response_code(200);
            echo "Message envoyé avec succès.";
        } else {
            http_response_code(500);
            echo "Erreur lors de la mise à jour de la conversation: " . $conn->error;
        }

        // Fermez le statement pour la mise à jour de la conversation
        $stmt_update_conversation->close();
    } else {
        http_response_code(500);
        echo "Erreur lors de l'envoi du message: " . $conn->error;
    }

    // Fermez le statement pour l'insertion du message
    $stmt_message->close();
} else {
    // La conversation n'existe pas, insérez le message dans les deux tables conversations et messages
    // Préparez la requête SQL pour insérer la conversation dans la base de données
    $sql_conversation = "INSERT INTO conversations (utilisateur1, utilisateur2, dernier_message, date_dernier_message) VALUES (?, ?, ?, NOW())";
    $stmt_conversation = $conn->prepare($sql_conversation);

    // Vérification de la préparation de la requête
    if (!$stmt_conversation) {
        http_response_code(500);
        exit("Erreur de préparation de la requête: " . $conn->error);
    }

    // Liaison des paramètres et exécution de la requête pour insérer la conversation
    $stmt_conversation->bind_param("sss", $expediteur, $destinataire, $message);
    if ($stmt_conversation->execute()) {
        // Récupérez l'ID de la nouvelle conversation insérée
        $conversation_id = $stmt_conversation->insert_id;

        // Insérez également le message dans la table messages
        $sql_message = "INSERT INTO messages (conversation_id, expediteur, destinataire, contenu, lu) VALUES (?, ?, ?, ?, ?)";
        $stmt_message = $conn->prepare($sql_message);

        // Vérification de la préparation de la requête
        if (!$stmt_message) {
            http_response_code(500);
            exit("Erreur de préparation de la requête: " . $conn->error);
        }

        // Liaison des paramètres et exécution de la requête pour insérer le message
        $stmt_message->bind_param("isssi", $conversation_id, $expediteur, $destinataire, $message, $lu);
        $sql_message = "INSERT INTO conversations_utilisateurs (conversation_id, utilisateur_email) VALUES (?, ?)";
        $stmt_message = $conn->prepare($sql_message);
    
        // Vérification de la préparation de la requête
        if (!$stmt_message) {
            http_response_code(500);
            exit("Erreur de préparation de la requête: " . $conn->error);
        }
    
        // Liaison des paramètres et exécution de la requête pour insérer le message
        $stmt_message->bind_param("is", $conversation_id, $expediteur );
  $sql_message = "INSERT INTO conversations_utilisateurs (conversation_id, utilisateur_email) VALUES (?, ?)";
        $stmt_message = $conn->prepare($sql_message);
    
        // Vérification de la préparation de la requête
        if (!$stmt_message) {
            http_response_code(500);
            exit("Erreur de préparation de la requête: " . $conn->error);
        }
    
        // Liaison des paramètres et exécution de la requête pour insérer le message
        $stmt_message->bind_param("is", $conversation_id, $expediteur );
        // Fermez le statement pour l'insertion du message
        $stmt_message->close();
    } else {
        http_response_code(500);
        echo "Erreur lors de l'insertion de la conversation: " . $conn->error;
    }

    // Fermez le statement pour l'insertion de la conversation
    $stmt_conversation->close();
}

// Fermez le statement de vérification de la conversation
$stmt_check_conversation->close();

// Fermez la connexion à la base de données
$conn->close();
?>
