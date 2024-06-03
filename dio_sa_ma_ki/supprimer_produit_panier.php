<?php

    // Récupérer les valeurs du formulaire
    $panier_id = $_POST['panier_id'];
  
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

    // Requête pour supprimer le produit du panier
    $sql_delete_product = "DELETE FROM panier WHERE id = ?";
    $stmt_delete_product = $conn->prepare($sql_delete_product);
    $stmt_delete_product->bind_param("i", $panier_id);

    // Exécution de la requête
    if ($stmt_delete_product->execute()) {
      header('Location: voir_panier.php');
    } else {
        echo "Une erreur s'est produite lors de la suppression du produit du panier.";
    }

    // Fermer la connexion
    $conn->close();


?>
