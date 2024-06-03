<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu déroulant</title>
   
    <style>
      /* Styles pour les cartes de produit */
.container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 100px; /* Pour compenser le header fixe */
}

.product {
    width: 250px;
    margin: 15px;
    padding: 15px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    position: relative;
}

.product img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
}

.product h3 {
    font-size: 1.5em;
    margin: 10px 0;
}

.product p {
    font-size: 1em;
    margin: 5px 0;
}

.product .icon-container {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.product .icon-container i {
    font-size: 1.5em;
    cursor: pointer;
    transition: color 0.3s;
}

.product .icon-container i:hover {
    color: #007bff;
}

.product .add-to-cart-button {
    display: inline-block;
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    margin-top: 10px;
    transition: background-color 0.3s;
}

.product .add-to-cart-button:hover {
    background-color: #0056b3;
}

/* Styles pour le cœur favori */
.product .favorite {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 1.5em;
    color: #ff0000;
    cursor: pointer;
    transition: color 0.3s;
}

.product .favorite.clicked {
    color: #000;
}
  /* Ajoutez le CSS ici (utilisez le code CSS donné ci-dessus) */
    </style>
</head>
<body>
    <div class="header">
        <!-- Conteneur pour les éléments à gauche -->
        <div class="left-container">
            <!-- Logos -->
            <div class="container">
                <img class="logo1" src="diosa.jpeg" alt="Logo 1" style="margin-right: 10px;" id="logo-clickable">
                <ul class="sub-menu" id="liste-cachée">
                    <li><a href="#">voir profile</a></li>
                    <li><a href="#">notification</a></li>
                    <li><a href="#">se deconnecter</a></li>
                </ul>
            </div>
            <!-- Liens "My Account" -->
            <div class="wsshopmyaccount-container">
                <ul>
                    <li class="wsshopmyaccount">
                        <span class="q"><a href="#">categorie</a></span>
                        <ul class="sub-menu">
                            <li><a href="#">Vêtements</a></li>
                            <li><a href="#">Chaussures</a></li>
                            <li><a href="#">Accessoires</a></li>
                            <li><a href="#"> Électroniques</a></li>
                            <li><a href="#">Beauté et sante</a></li>
                        </ul>
                    </li>
                    <li class="wsshopmyaccount">
                      <span class="q"><a href="#">prix</a></span>
                        <ul class="sub-menu">
                            <li><a href="#"> 0-50</a></li>
                            <li><a href="#">51-100</a></li>
                            <li><a href="#">101-150</a></li>
                            <li><a href="#">151-200</a></li>
                            <li><a href="#">plus de 200</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Barre de recherche -->
        <div class="search-container">
            <input type="text" placeholder="Search...">
            <button>Search</button>
        </div>
        <!-- Logos -->
        <div class="right-container">
            <img class="logo3" src="logo2.jpeg" alt="Logo 3" style="margin-left: 10px;">
            <img class="logo4" src="logo3.jpeg" alt="Logo 4" style="margin-left: 10px;">
            <img class="logo2" src="diosa.jpeg" alt="Logo 2" style="margin-right: 10px;">
        </div>
    </div>
    <script>
        document.getElementById("logo-clickable").addEventListener("click", function() {
            var listeCachée = document.getElementById("liste-cachée");
            if (listeCachée.style.display === "none") {
                listeCachée.style.display = "block";
            } else {
                listeCachée.style.display = "none";
            }
        });
    </script>

    <div class="container">
        <?php
        // Vérifier s'il y a des produits à afficher
        if ($result1->num_rows > 0) {
            // Parcourir chaque produit
            while($row = $result1->fetch_assoc()) {
                // Afficher les détails du produit
                ?>
                <div class="product">
                    <?php
                    // Chemin de l'image du produit
                    $image_path = "image/" . $row['image']; 
                    // Vérifier si le fichier image existe
                    if (file_exists($image_path)) {
                        // Vérifier si le fichier est lisible
                        if (!is_readable($image_path)) {
                            // Ajouter les droits de lecture
                            chmod($image_path, 0644);
                        }
                        // Afficher l'image du produit
                        echo '<img src="' . $image_path . '" alt="' . $row['nom'] . '">';
                    } else {
                        // Si le fichier n'existe pas, afficher une image par défaut
                        echo '<img src="chemin_vers_image_par_defaut/default_image.jpg" alt="Image par défaut">';
                    }
                    ?>
                    <h3><?php echo $row['nom']; ?></h3>
                    <p><?php echo $row['description']; ?></p>
                    <p>Prix: <?php echo $row['prix']; ?>€</p>
                    <div class="icon-container">
                        <i class="fas fa-shopping-cart"></i>
                        <i class="fas fa-envelope"></i>
                    </div>
                    <button class="add-to-cart-button">Ajouter au panier</button>
                    <i class="far fa-heart favorite"></i>
                </div>
                <?php
            }
        } else {
            echo "Aucun produit trouvé.";
        }
        ?>
    </div>
    <script>
        document.querySelectorAll('.favorite').forEach(function(favorite) {
            favorite.addEventListener('click', function() {
                this.classList.toggle('clicked');
            });
        });
    </script>
</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
