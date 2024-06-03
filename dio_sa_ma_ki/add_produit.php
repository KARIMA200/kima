<?php
// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérez les données du formulaire
    $nom_produit = $_POST['nom_produit'];
    $description_produit = $_POST['description_produit'];
    $prix_produit = $_POST['prix_produit'];
    $stock_produit = $_POST['stock'];
    $categorie_produit = $_POST['categorie'];

    // la base de donner 

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ecommerce";

    $conn = new mysqli($servername, $username, $password, $dbname);

   
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Récupérer le nom du fichier
        $image_name = $_FILES['image']['name'];
        // Définir le chemin de destination pour enregistrer l'image
        $upload_directory = "image/"; // Choisissez le répertoire où vous souhaitez enregistrer les images
        // Déplacer le fichier téléchargé vers le répertoire de destination
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_directory . $image_name)) {
            // Requête SQL pour insérer le produit dans la table produits
            $sql_produit = "INSERT INTO produits (nom, description, prix, stock, categorie, image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_produit = $conn->prepare($sql_produit);
            $stmt_produit->bind_param("ssssss", $nom_produit, $description_produit, $prix_produit, $stock_produit, $categorie_produit, $image_name);
            $stmt_produit->execute();

            // Vérifier si l'insertion dans la table produits a réussi
            if ($stmt_produit->affected_rows > 0) {
                // Récupérer l'ID du produit inséré
                $produit_id = $stmt_produit->insert_id;

                // Récupérer l'email du vendeur depuis la session
                session_start();
                $email = $_SESSION['email'];

                // Requête SQL pour trouver l'ID du vendeur par email
                $sql_vendeur = "SELECT id FROM vendeurs WHERE email = ?";
                $stmt_vendeur = $conn->prepare($sql_vendeur);
                $stmt_vendeur->bind_param("s", $email);
                $stmt_vendeur->execute();
                $result_vendeur = $stmt_vendeur->get_result();

                // Vérifier s'il y a des résultats
                if ($result_vendeur->num_rows > 0) {
                    // Récupérer l'ID du vendeur
                    $row_vendeur = $result_vendeur->fetch_assoc();
                    $vendeur_id = $row_vendeur['id'];

                    // Requête SQL pour insérer le produit dans la table produits_vendeurs
                    $sql_produit_vendeur = "INSERT INTO produits_vendeurs (vendeur_id, produit_id) VALUES (?, ?)";
                    $stmt_produit_vendeur = $conn->prepare($sql_produit_vendeur);
                    $stmt_produit_vendeur->bind_param("ii", $vendeur_id, $produit_id);
                    $stmt_produit_vendeur->execute();

                    // Vérifier si l'insertion dans la table produits_vendeurs a réussi
                    if ($stmt_produit_vendeur->affected_rows > 0) {
                        echo "Nouveau produit ajouté avec succès.";
                    } else {
                        echo "Erreur lors de l'ajout du produit dans la table produits_vendeurs: " . $conn->error;
                    }
                } else {
                    echo "Aucun vendeur trouvé avec l'email $email.";
                }
            } else {
                echo "Erreur lors de l'ajout du produit dans la table produits: " . $conn->error;
            }

            // Fermer les connexions et libérer les ressources
            $stmt_produit->close();
            $stmt_vendeur->close();
            $stmt_produit_vendeur->close();
        } else {
            echo "Une erreur s'est produite lors de l'enregistrement de l'image.";
        }
    } else {
        echo "Une erreur s'est produite lors du téléchargement de l'image.";
    }

    $conn->close();
} else {
    // Si le formulaire n'est pas soumis, redirigez l'utilisateur vers une autre page ou affichez un message d'erreur
    echo "Le formulaire n'a pas été soumis.";
}
?>
