<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    die("Vous n'êtes pas connecté.");
}

// Récupérer l'email de l'utilisateur depuis la session
$email_utilisateur = $_SESSION['email'];

// Récupérer le product_id à partir des données du formulaire POST
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

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

    // Vérifier si le produit est déjà en favori pour cet utilisateur
    $stmt_check_favorite = $conn->prepare("SELECT * FROM favoris WHERE product_id = ? AND user_email = ?");
    $stmt_check_favorite->bind_param("is", $product_id, $email_utilisateur);
    $stmt_check_favorite->execute();
    $result_check_favorite = $stmt_check_favorite->get_result();

    if ($result_check_favorite->num_rows == 0) {
        // Le produit n'est pas déjà en favori pour cet utilisateur, procéder à l'insertion
        // Préparer la requête d'insertion dans la table des favoris
        $stmt_insert_favorite = $conn->prepare("INSERT INTO favoris (product_id, user_email) VALUES (?, ?)");
        $stmt_insert_favorite->bind_param("is", $product_id, $email_utilisateur);

        // Exécuter la requête d'insertion
        if ($stmt_insert_favorite->execute()) {
            echo "Produit ajouté aux favoris avec succès.";

            // Rechercher le vendeur du produit
            $stmt_vendeur = $conn->prepare("SELECT vendeur_id FROM produits_vendeurs WHERE produit_id = ?");
            $stmt_vendeur->bind_param("i", $product_id);
            $stmt_vendeur->execute();
            $result_vendeur = $stmt_vendeur->get_result();
            $row_vendeur = $result_vendeur->fetch_assoc();
            $vendeur_id = $row_vendeur['vendeur_id'];

            // Rechercher l'email du vendeur
            $stmt_email_vendeur = $conn->prepare("SELECT email FROM vendeurs WHERE id = ?");
            $stmt_email_vendeur->bind_param("i", $vendeur_id);
            $stmt_email_vendeur->execute();
            $result_email_vendeur = $stmt_email_vendeur->get_result();
            $row_email_vendeur = $result_email_vendeur->fetch_assoc();
            $email_vendeur = $row_email_vendeur['email'];

            // Rechercher le nom et le prénom de l'utilisateur
            $stmt_info_utilisateur = $conn->prepare("SELECT nom, prenom FROM clients WHERE email = ?");
            $stmt_info_utilisateur->bind_param("s", $email_utilisateur);
            $stmt_info_utilisateur->execute();
            $result_info_utilisateur = $stmt_info_utilisateur->get_result();
            $row_info_utilisateur = $result_info_utilisateur->fetch_assoc();
            $nom_utilisateur = $row_info_utilisateur['nom'];
            $prenom_utilisateur = $row_info_utilisateur['prenom'];

            // Vérifier si l'utilisateur est un client
            if (!$row_info_utilisateur) {
                // Utilisateur non trouvé dans la table clients, donc il doit être un vendeur
                $stmt_info_utilisateur = $conn->prepare("SELECT nom, prenom FROM vendeurs WHERE email = ?");
                $stmt_info_utilisateur->bind_param("s", $email_utilisateur);
                $stmt_info_utilisateur->execute();
                $result_info_utilisateur = $stmt_info_utilisateur->get_result();
                $row_info_utilisateur = $result_info_utilisateur->fetch_assoc();
                $nom_utilisateur = $row_info_utilisateur['nom'];
                $prenom_utilisateur = $row_info_utilisateur['prenom'];
            }

            // Insérer une notification pour le vendeur
            $notification = "$nom_utilisateur $prenom_utilisateur a ajouté une favorie sur votre produit.";
            $stmt_insert_notification = $conn->prepare("INSERT INTO notifications (user_1, user_2, notification, product_id) VALUES (?, ?, ?, ?)");
            $stmt_insert_notification->bind_param("sssi", $email_utilisateur, $email_vendeur, $notification, $product_id);
            $stmt_insert_notification->execute();

            // Compter le nombre de favoris pour ce produit et le stocker dans une variable de session
            $stmt_count_favorites = $conn->prepare("SELECT COUNT(*) AS favoris_count FROM favoris WHERE product_id = ?");
            $stmt_count_favorites->bind_param("i", $product_id);
            $stmt_count_favorites->execute();
            $result_count_favorites = $stmt_count_favorites->get_result();
            $row_count_favorites = $result_count_favorites->fetch_assoc();
            $_SESSION['favoris_count'][$product_id] = $row_count_favorites['favoris_count'];
        } else {
            echo "Erreur lors de l'ajout du produit aux favoris: " . $conn->error;
        }

        // Fermer les déclarations préparées
        $stmt_insert_favorite->close();
        $stmt_vendeur->close();
        $stmt_email_vendeur->close();
        $stmt_info_utilisateur->close();
        $stmt_insert_notification->close();
        $stmt_count_favorites->close();
    } else {
        echo "Le produit est déjà en favori pour cet utilisateur.";
    }

    // Fermer la connexion
    $conn->close();
} else {
    echo "Le product_id n'a pas été fourni.";
}
?>
