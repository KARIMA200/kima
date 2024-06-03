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

// Démarrer la session pour récupérer l'email de l'utilisateur
session_start();

// Vérifier si l'email de l'utilisateur est stocké en session
if (isset($_SESSION['email'])) {
    // Récupérer l'email de l'utilisateur
    $email = $_SESSION['email'];

    // Requête pour obtenir l'ID du client à partir de son email
    $sql_client_id = "SELECT id FROM clients WHERE email = ?";
    $stmt_client_id = $conn->prepare($sql_client_id);
    $stmt_client_id->bind_param("s", $email);
    $stmt_client_id->execute();
    $result_client_id = $stmt_client_id->get_result();

    if ($result_client_id->num_rows > 0) {
        // Récupération de l'ID du client
        $row = $result_client_id->fetch_assoc();
        $user_id = $row["id"];

        // Requête pour obtenir les produits du panier pour ce client
        $sql_panier_produits = "SELECT pa.product_id as product_id, pa.id as panier_id, p.nom, p.description, p.prix, p.image FROM panier pa JOIN produits p ON pa.product_id = p.id WHERE pa.user_id = ?";
        $stmt_panier_produits = $conn->prepare($sql_panier_produits);
        $stmt_panier_produits->bind_param("i", $user_id);
        $stmt_panier_produits->execute();
        $result_panier_produits = $stmt_panier_produits->get_result();

        if ($result_panier_produits->num_rows > 0) {
            $total = 0;
            ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil</title>

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->

</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-12 col-md-10 col-md-offset-1">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th class="text-center">Prix</th>
                        <th class="text-center">Total</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row_produit = $result_panier_produits->fetch_assoc()) {
                        $panier_id = $row_produit['panier_id'];
                        $product_id = $row_produit["product_id"];
                        $quantite = 1; // Vous pouvez ajuster la quantité selon vos besoins
                        $total_par_produit = $row_produit["prix"] * $quantite;
                        $total += $total_par_produit;
                    ?>
                    <tr>
                        <td class="col-sm-8 col-md-6">
                        <div class="media">
                            <a class="thumbnail pull-left" href="#"> <img class="media-object" src="images/<?php echo $row_produit['image']; ?>" style="width: 72px; height: 72px;"> </a>
                            <div class="media-body">
                                <h4 class="media-heading"><a href="#"><?php echo $row_produit["nom"]; ?></a></h4>
                                <h5 class="media-heading">par <a href="#"><?php echo $row_produit["description"]; ?></a></h5>
                                <span>Status: </span><span class="text-success"><strong>En stock</strong></span>
                            </div>
                        </div></td>
                        <td class="col-sm-1 col-md-1" style="text-align: center">
                        <input type="number" class="form-control" name="quantite" value="<?php echo $quantite; ?>" min="1">
                        </td>
                        <td class="col-sm-1 col-md-1 text-center"><strong><?php echo $row_produit["prix"]; ?> €</strong></td>
                        <td class="col-sm-1 col-md-1 text-center"><strong><?php echo $total_par_produit; ?> €</strong></td>
                        <td class="col-sm-1 col-md-1">
                        <form action="supprimer_produit_panier.php" method="post">
                            <input type="hidden" name="panier_id" value="<?php echo $panier_id; ?>">
                            <button type="submit" class="btn btn-danger">
                                <span class="glyphicon glyphicon-remove"></span> Annuler
                            </button>
                        </form>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td><h3>Total</h3></td>
                        <td class="text-right"><h3><strong><?php echo $total; ?> €</strong></h3></td>
                    </tr>
                    <tr>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>
                        <button type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-shopping-cart"></span> Retourner à la page d'accueil
                        </button></td>
                        <td>
                        <button type="button" class="btn btn-success">
                            Valider <span class="glyphicon glyphicon-play"></span>
                        </button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
            <?php
        } else {
            echo "Panier vide.";
        }
    } else {
        echo "Aucun client trouvé avec cet email : $email";
    }
} else {
    echo "Aucun email d'utilisateur trouvé en session.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
