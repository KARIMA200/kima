<?php
// getUnreadMessagesCount.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email']; // Supposons que l'utilisateur est connecté et que son email est stocké dans la session

$sql = "SELECT COUNT(*) as unread_count FROM messages WHERE destinataire = ? AND lu = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(['unread_count' => $row['unread_count']]);

$stmt->close();
$conn->close();
?>
