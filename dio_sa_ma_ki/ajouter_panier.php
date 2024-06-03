                                                                                                                 <?php
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

// Traitement des données de formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si l'identifiant du produit a été envoyé
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];

        // Démarrer la session pour récupérer l'email de l'utilisateur
        session_start();
        
        // Vérifier si l'email de l'utilisateur est stocké en session
    
            // Récupérer l'email de l'utilisateur
         // Assurez-vous de démarrer la session

            // Récupérez l'email de l'utilisateur à partir du tableau associatif des variables de session avec l'identifiant de session comme clé
            $email = $_SESSION['email'];
            
            // Utilisez l'email comme nécessaire
        
            

            // Requête pour obtenir l'ID du client à partir de son email
            $sql_client_id = "SELECT id FROM clients WHERE email = ?  ";
            $stmt_client_id = $conn->prepare($sql_client_id);
            $stmt_client_id->bind_param("s", $email);
            $stmt_client_id->execute();
            $result_client_id = $stmt_client_id->get_result();

            if ($result_client_id->num_rows > 0) {
                // Récupération de l'ID du client
                $row = $result_client_id->fetch_assoc();
                $client_id = $row["id"];

                // Vérifier si le produit existe déjà dans le panier du client
                $sql_check_product = "SELECT id FROM panier WHERE product_id = ? AND user_id = ?";
                $stmt_check_product = $conn->prepare($sql_check_product);
                $stmt_check_product->bind_param("ii", $product_id, $client_id);
                $stmt_check_product->execute();
                $result_check_product = $stmt_check_product->get_result();

                if ($result_check_product->num_rows > 0) {
                    // Le produit existe déjà dans le panier du client
                    echo "Ce produit est déjà dans votre panier.";
                } else {
                    // Le produit n'existe pas encore dans le panier du client, l'ajouter
                    $sql_insert_panier = "INSERT INTO panier (product_id, user_id) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql_insert_panier);

                    if ($stmt) {
                        // Liaison des paramètres
                        $stmt->bind_param("ii", $product_id, $client_id);

                        // Exécution de la requête
                        if ($stmt->execute()) {
                            echo "Le produit a été ajouté au panier avec succès.";
                        } else {
                            echo "Erreur lors de l'ajout du produit au panier : " . $stmt->error;
                        }

                        // Fermer la requête préparée
                        $stmt->close();
                    } else {
                        echo "Erreur de préparation de la requête : " . $conn->error;
                    }
                }
            } else {
                echo "Aucun client trouvé avec cet email : $email";
            }
        } else {
            echo "Aucun email d'utilisateur trouvé en session.";
        }
    }


// Fermer la connexion à la base de données
$conn->close();
?>
