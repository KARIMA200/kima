<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    die("Vous n'êtes pas connecté.");
}

// Récupérer l'email de la session
$email = $_SESSION['email'];

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

// Rechercher l'utilisateur dans la table vendeurs
$sql_vendeur = "SELECT * FROM vendeurs WHERE email = ?";
$stmt_vendeur = $conn->prepare($sql_vendeur);
$stmt_vendeur->bind_param("s", $email);
$stmt_vendeur->execute();
$result_vendeur = $stmt_vendeur->get_result();

// Rechercher l'utilisateur dans la table clients si pas trouvé dans vendeurs
$sql_client = "SELECT * FROM clients WHERE email = ?";
$stmt_client = $conn->prepare($sql_client);
$stmt_client->bind_param("s", $email);
$stmt_client->execute();
$result_client = $stmt_client->get_result();

if ($result_vendeur->num_rows > 0) {
    $row = $result_vendeur->fetch_assoc();
    $table = 'vendeurs'; // Définir la table à mettre à jour
} elseif ($result_client->num_rows > 0) {
    $row = $result_client->fetch_assoc();
    $table = 'clients'; // Définir la table à mettre à jour
} else {
    die("Utilisateur non trouvé");
}

// Mettre à jour les informations dans la table appropriée
if (!empty($table)) {
    // Récupérer les valeurs des champs à modifier depuis $_POST
    $nom_prenom = $_POST['nom_prenom'];
    $pays = $_POST['pays'];
    $ville = $_POST['ville'];
    $telephone = $_POST['telephone'];

    // Séparer la chaîne nom_prenom en nom et prénom
    list($nom, $prenom) = explode(" ", $nom_prenom);

    // Préparer la requête d'update en fonction de la table
    $sql_update = "UPDATE $table SET nom=?, prenom=?, pays=?, ville=?, telephone=? WHERE email=?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssss", $nom, $prenom, $pays, $ville, $telephone, $email);

    // Exécuter la requête d'update
    if ($stmt_update->execute()) {
        echo "Informations mises à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour des informations: " . $conn->error;
    }
} else {
    echo "Type d'utilisateur non pris en charge.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
