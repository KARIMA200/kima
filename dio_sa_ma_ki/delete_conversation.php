<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION["email"])) {
    http_response_code(401);
    exit("Utilisateur non connecté.");
}

$expediteur = $_SESSION["email"];

// Vérifiez si les paramètres requis sont présents dans l'URL
if (!isset($_GET['user1'])) {
    http_response_code(400);
    exit("Paramètre 'user1' manquant dans l'URL.");
}

$destinataire = $_GET['user1'];

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

// Rechercher l'ID de la conversation entre l'expéditeur et le destinataire
$sql_check_conversation = "SELECT id, utilisateur1, utilisateur2 FROM conversations WHERE (utilisateur1 = ? AND utilisateur2 = ?) OR (utilisateur1 = ? AND utilisateur2 = ?)";
$stmt_check_conversation = $conn->prepare($sql_check_conversation);
$stmt_check_conversation->bind_param("ssss", $expediteur, $destinataire, $destinataire, $expediteur);
$stmt_check_conversation->execute();
$result_check_conversation = $stmt_check_conversation->get_result();

if ($result_check_conversation->num_rows > 0) {
    // Si une conversation existe entre l'expéditeur et le destinataire, mettez à jour les marques de suppression
    $row = $result_check_conversation->fetch_assoc();
    $conversation_id = $row['id'];
    $utilisateur1 = $row['utilisateur1'];
    $utilisateur2 = $row['utilisateur2'];

    if ($expediteur === $utilisateur1) {
        // Utilisateur est utilisateur1, mettez utilisateur1_delete à true
        $sql_update_delete = "UPDATE conversations SET utilisateur1_delete = TRUE WHERE id = ?";
        $stmt_update_delete = $conn->prepare($sql_update_delete);
        $stmt_update_delete->bind_param("i", $conversation_id);
        $stmt_update_delete->execute();
    } elseif ($expediteur === $utilisateur2) {
        // Utilisateur est utilisateur2, mettez utilisateur2_delete à true
        $sql_update_delete = "UPDATE conversations SET utilisateur2_delete = TRUE WHERE id = ?";
        $stmt_update_delete = $conn->prepare($sql_update_delete);
        $stmt_update_delete->bind_param("i", $conversation_id);
        $stmt_update_delete->execute();
    } else {
        // L'utilisateur n'est pas associé à cette conversation
        http_response_code(403);
        exit("Vous n'êtes pas autorisé à supprimer cette conversation.");
    }

    echo "La conversation a été marquée comme supprimée.";
} else {
    // Aucune conversation trouvée entre l'expéditeur et le destinataire
    http_response_code(404);
    exit("Aucune conversation trouvée entre vous et cet utilisateur.");
}

$stmt_check_conversation->close();
$stmt_update_delete->close();
$conn->close();
?>

