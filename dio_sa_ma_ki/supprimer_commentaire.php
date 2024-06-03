<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";
session_start(); // Démarrer la session

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si l'identifiant du commentaire est présent dans la requête POST
if (isset($_POST['commentaire_id'])) {
    // Récupérer l'identifiant du commentaire depuis la requête POST
    $commentaire_id = $_POST['commentaire_id'];

    // Requête SQL pour supprimer le commentaire spécifié
    $sql = "DELETE FROM commentaires WHERE id = $commentaire_id";
    if ($conn->query($sql) === TRUE) {
        echo "Commentaire supprimé avec succès";
    } else {
        echo "Erreur lors de la suppression du commentaire: " . $conn->error;
    }
} else {
    echo "Erreur: Aucun identifiant de commentaire fourni.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
