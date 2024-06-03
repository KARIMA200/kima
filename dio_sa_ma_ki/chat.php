<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION["email"])) {
    header("Location: login.php"); // Redirection vers la page de connexion si non connecté
    exit;
}

// Récupérer l'email de l'utilisateur à partir de la session
$mon_email = $_SESSION["email"];

// Vérification de la présence de l'email de l'autre utilisateur dans les paramètres GET
if (!isset($_POST["email_autre"])) {
    // Rediriger vers une page d'erreur ou une autre page appropriée si l'email de l'autre utilisateur n'est pas présent
    header("Location: erreur.php");
    exit;
}

// Récupérer l'email de l'autre utilisateur à partir des paramètres GET
$email_autre = $_POST["user"];

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

// Requête SQL pour sélectionner tous les messages entre l'utilisateur et l'autre utilisateur, triés par date ascendant
$sql = "SELECT contenu, date_message FROM messages WHERE (expediteur = '$mon_email' AND destinataire = '$email_autre') OR (expediteur = '$email_autre' AND destinataire = '$mon_email') ORDER BY date_message ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
</head>
<body>
    <h1>Chat avec <?php echo $email_autre; ?></h1>
    <div id="messages">
        <?php
        // Vérifier s'il y a des résultats
        if ($result->num_rows > 0) {
            // Afficher chaque message
            while ($row = $result->fetch_assoc()) {
                ?>
                <div>
                    <p><?php echo $row["contenu"]; ?></p>
                    <p>Date: <?php echo $row["date_message"]; ?></p>
                </div>
                <?php
            }
        } else {
            echo "<p>Aucun message trouvé.</p>";
        }
        ?>
    </div>
</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
