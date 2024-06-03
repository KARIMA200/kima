<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION["email"])) {
    header("Location: login.php"); // Redirection vers la page de connexion si non connecté
    exit;
}

// Récupérer l'email de l'utilisateur à partir de la session
$email = $_SESSION["email"];

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Requête SQL pour récupérer les conversations liées à l'email de l'utilisateur
$sql = "SELECT utilisateur1, utilisateur2, dernier_message, date_dernier_message
        FROM conversations
        WHERE (utilisateur1 = ? AND utilisateur1_delete = 0) OR 
              (utilisateur2 = ? AND utilisateur2_delete = 0)";

// Ajouter la condition de recherche si un terme de recherche est fourni
if (!empty($_GET["search"])) {
    $search = '%' . $_GET["search"] . '%';
    $sql .= " AND (utilisateur1 LIKE ? OR utilisateur2 LIKE ?)";
}
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    exit("Erreur de préparation de la requête: " . $conn->error);
}

if (!empty($_GET["search"])) {
    $stmt->bind_param("ssss", $email, $email, $search, $search);
} else {
    $stmt->bind_param("ss", $email, $email);
}

// Exécuter la requête pour récupérer les conversations
$stmt->execute();
$result = $stmt->get_result();

$conversations = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Trouver l'autre utilisateur
        $other_user = $row['utilisateur1'] === $email ? $row['utilisateur2'] : $row['utilisateur1'];

        // Requête SQL pour compter le nombre de messages non lus de cet autre utilisateur
        $count_unread_sql = "SELECT COUNT(*) AS count_unread FROM messages WHERE expediteur = ? AND destinataire = ? AND lu = 0";
        $stmt_count_unread = $conn->prepare($count_unread_sql);
        
        if (!$stmt_count_unread) {
            http_response_code(500);
            exit("Erreur de préparation de la requête pour compter les messages non lus: " . $conn->error);
        }

        $stmt_count_unread->bind_param("ss", $other_user, $email);
        $stmt_count_unread->execute();
        $count_result = $stmt_count_unread->get_result();

        $unread_count = 0; // Initialisation par défaut

        if ($count_row = $count_result->fetch_assoc()) {
            $unread_count = $count_row['count_unread'];
        }

        $stmt_count_unread->close();

        // Requête pour récupérer les informations de l'autre utilisateur
        $user_info_sql = "SELECT * FROM clients WHERE email = ? 
                          UNION
                          SELECT * FROM vendeurs WHERE email = ?";
        $stmt_user_info = $conn->prepare($user_info_sql);
        
        if (!$stmt_user_info) {
            http_response_code(500);
            exit("Erreur de préparation de la requête pour récupérer les informations de l'utilisateur: " . $conn->error);
        }

        $stmt_user_info->bind_param("ss", $other_user, $other_user);
        $stmt_user_info->execute();
        $user_info_result = $stmt_user_info->get_result();

        if ($user_info_result->num_rows > 0) {
            $user_info = $user_info_result->fetch_assoc();
            $conversations[] = [
                'nom' => $user_info['nom'],
                'email' => $user_info['email'],
                'prenom' => $user_info['prenom'],
                'user_image' => $user_info['user_image'],
                'dernier_message' => $row['dernier_message'],
                'date_dernier_message' => $row['date_dernier_message'],
                'messages_non_lus' => $unread_count // Nombre de messages non lus pour cet autre utilisateur
            ];

            // Debugging: Afficher les détails des conversations
            echo "<script>console.log('Conversation with " . $user_info['email'] . ": " . json_encode($conversations) . "');</script>";
        }
        $stmt_user_info->close();
    }
} 
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversations à droite</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
        }

        .right-div {
            position: fixed;
            top: 0;
            right: 0;
            width: 8cm;
            height: 100%;
            overflow-y: auto;
            background-color: #fff;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
            border-left: 5px solid #4bcdA2;
        }

        .search-bar {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 5px;
            border: 1px solid #4bcdA2;
            border-radius: 20px;
        }

        .search-bar input {
            flex-grow: 1;
            border: none;
            padding: 5px;
            outline: none;
            border-radius: 20px;
        }

        .search-bar .search-icon {
            font-size: 20px;
            color: #4bcdA2;
            margin-right: 10px;
        }

        .conversation {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #4bcdA2;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.3s;
            cursor: pointer;
        }

        .unread-notification {
            background-color: pink;
            color: red;
            font-weight: bold;
            border-radius: 50%;
            padding: 5px 10px;
            position: absolute;
            top: 5px;
            right: 5px;
        }

        .conversation:hover {
            background-color: #e4e6eb;
            transform: translateX(5px);
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            border: 2px solid #4bcdA2;
        }

        .conversation-details {
            flex-grow: 1;
        }

        .last-message {
            margin: 0;
            font-size: 14px;
            color: #050505;
            font-weight: 500;
        }

        .message-date {
            font-size: 12px;
            color: #65676b;
        }

        .conversation-options {
            display: none;
        }

        .conversation:hover .conversation-options {
            display: block;
            margin-left: auto;
            color: #65676b;
        }

        .options-icon {
            font-size: 18px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="right-div">

    <div class="search-bar">
        <form action="" method="GET">
        <input type="text" id="searchInput" placeholder="Rechercher..." oninput="filterConversations()">
        <button type="submit" class="search-icon">🔍</button>
        </form>
    </div>
    
    <?php foreach ($conversations as $conversation) { ?>
       
        <div class="conversation">
            
        <?php
$image_path = "image/" . $conversation['user_image'];
if (file_exists($image_path) && is_readable($image_path)) {
    echo '<img src="' . $image_path . '" alt="' . htmlspecialchars($conversation['prenom']) . '" class="avatar" id="logo-clickable">';
} else {
    echo '<img src="image/pardefaut.jpg" alt="Image par défaut" class="logo1" id="logo-clickable">';
}
?>

            <p class="user-info"><?php echo htmlspecialchars($conversation['nom'] . ' ' . $conversation['prenom']); ?></p>
            <div class="conversation-details">
            <?php 
        $dernier_message = $conversation['dernier_message'];
        
        if (strpos($dernier_message, 'images/') === 0) {
            echo htmlspecialchars("image");
        } elseif (strpos($dernier_message, 'uploads/') === 0) {
            echo htmlspecialchars("vocal");
        
    } else {
        echo htmlspecialchars($dernier_message);
    }
    ?>
            <span class="message-date"><?php echo htmlspecialchars($conversation['date_dernier_message']); ?></span>
        </div>
        <div class="conversation-options">
            <a href="chatter.php?email=<?php echo urlencode($conversation['email']); ?>&nom=<?php echo urlencode($conversation['nom']); ?>&prenom=<?php echo urlencode($conversation['prenom']); ?>&user_image=<?php echo urlencode($conversation['user_image']); ?>">
                <i class="fa-solid fa-comment-dots"></i>
            </a>
            <a href="delete_conversation.php?nom=<?php echo urlencode($email); ?>&user1=<?php echo urlencode($other_user); ?>" class="options-icon">
                <i class="fa-solid fa-trash"></i>
            </a>
            <?php if ($conversation['messages_non_lus'] > 0) { ?>
                <span class="unread-notification"><?php echo $conversation['messages_non_lus']; ?></span>
            <?php } ?>
        </div>
    </div>

<?php } ?>
</div>
</body>
</html>
