<?php
session_start();

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

// Vérifier si l'email est défini dans la session
if (!isset($_SESSION['email'])) {
    die("Email non défini dans la session");
}

$email = $_SESSION['email'];

// Récupérer les nouvelles informations du formulaire
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$motdepasse = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT); // Hachage du mot de passe

// Mettre à jour les informations dans la table vendeurs
$sql_update_vendeur = "UPDATE vendeurs SET nom = ?, prenom = ?, motdepasse = ? WHERE email = ?";
$stmt_update_vendeur = $conn->prepare($sql_update_vendeur);
$stmt_update_vendeur->bind_param("ssss", $nom, $prenom, $motdepasse, $email);
$stmt_update_vendeur->execute();

// Mettre à jour les informations dans la table clients
$sql_update_client = "UPDATE clients SET nom = ?, prenom = ?, motdepasse = ? WHERE email = ?";
$stmt_update_client = $conn->prepare($sql_update_client);
$stmt_update_client->bind_param("ssss", $nom, $prenom, $motdepasse, $email);
$stmt_update_client->execute();

// Rediriger vers la page de profil après la mise à jour
header("Location: profil.php");
exit();

$conn->close();
?>
