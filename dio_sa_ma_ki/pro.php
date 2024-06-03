

    <?php
// Vérification de l'existence de l'email dans la requête GET
if(isset($_GET['email'])) {
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

    // Récupération de l'email de la requête GET
    $email = $_GET['email'];

    // Requête SQL pour récupérer les informations de l'utilisateur
    $sql = "SELECT nom, prenom, ville, pays, user_image FROM clients WHERE email = '$email'
            UNION
            SELECT nom, prenom, ville, pays, user_image FROM vendeurs WHERE email = '$email'";

    $result = $conn->query($sql);

    // Vérification s'il y a des résultats
    if ($result->num_rows > 0) {
        // Récupération des données de l'utilisateur
        $row = $result->fetch_assoc();
        $nom = $row["nom"];
        $prenom = $row["prenom"];
        $ville = $row["ville"];
        $pays = $row["pays"];
        $user_image = $row["user_image"];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="css/all.min.css">
    <style>
       .container {
  display: grid;
  grid-template-columns: 300px 300px 300px;
  grid-gap: 50px;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: #f5f5f5;
  font-family: 'Baloo Paaji 2', cursive;


 
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}





.card {
  background-color: #222831;
  height: 37rem;
  border-radius: 5px;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-shadow: rgba(0, 0, 0, 0.7);
  color: white;
}

.card__name {
  margin-top: 15px;
  font-size: 1.5em;
}

.card__image {
  height: 160px;
  width: 160px;
  border-radius: 50%;
  border: 5px solid #272133;
  margin-top: 20px;
  box-shadow: 0 10px 50px rgba(235, 25, 110, 1);
}


.draw-border {
  box-shadow: inset 0 0 0 4px #58cdd1;
  color: #58afd1;
  -webkit-transition: color 0.25s 0.0833333333s;
  transition: color 0.25s 0.0833333333s;
  position: relative;
}

.draw-border::before,
.draw-border::after {
  border: 0 solid transparent;
  box-sizing: border-box;
  content: '';
  pointer-events: none;
  position: absolute;
  width: 0rem;
  height: 0;
  bottom: 0;
  right: 0;
}

.draw-border::before {
  border-bottom-width: 4px;
  border-left-width: 4px;
}

.draw-border::after {
  border-top-width: 4px;
  border-right-width: 4px;
}

.draw-border:hover {
  color: #ffe593;
}

.draw-border:hover::before,
.draw-border:hover::after {
  border-color: #eb196e;
  -webkit-transition: border-color 0s, width 0.25s, height 0.25s;
  transition: border-color 0s, width 0.25s, height 0.25s;
  width: 100%;
  height: 100%;
}

.draw-border:hover::before {
  -webkit-transition-delay: 0s, 0s, 0.25s;
  transition-delay: 0s, 0s, 0.25s;
}

.draw-border:hover::after {
  -webkit-transition-delay: 0s, 0.25s, 0s;
  transition-delay: 0s, 0.25s, 0s;
}

.btn {
  background: none;
  border: none;
  cursor: pointer;
  line-height: 1.5;
  font: 700 1.2rem 'Roboto Slab', sans-serif;
  padding: 0.75em 2em;
  letter-spacing: 0.05rem;
  margin: 1em;
  width: 13rem;
}

.btn:focus {
  outline: 2px dotted #55d7dc;
}


.social-icons {
  padding: 0;
  list-style: none;
  margin: 1em;
}

.social-icons li {
  display: inline-block;
  margin: 0.15em;
  position: relative;
  font-size: 1em;
}

.social-icons i {
  color: #fff;
  position: absolute;
  top: 0.95em;
  left: 0.96em;
  transition: all 265ms ease-out;
}

.social-icons a {
  display: inline-block;
}

.social-icons a:before {
  transform: scale(1);
  -ms-transform: scale(1);
  -webkit-transform: scale(1);
  content: " ";
  width: 45px;
  height: 45px;
  border-radius: 100%;
  display: block;
  background: linear-gradient(45deg, #ff003c, #c648c8);
  transition: all 265ms ease-out;
}

.social-icons a:hover:before {
  transform: scale(0);
  transition: all 265ms ease-in;
}

.social-icons a:hover i {
  transform: scale(2.2);
  -ms-transform: scale(2.2);
  -webkit-transform: scale(2.2);
  color: #ff003c;
  background: -webkit-linear-gradient(45deg, #ff003c, #c648c8);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  transition: all 265ms ease-in;
}

.grid-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-gap: 20px;
  font-size: 1.2em;
}
 /* Votre CSS ici */
    </style>
</head>
<body>
    <div class="container">
        <?php
            // Affichage de l'image de l'utilisateur
            echo "<div class='card'>";
            echo "<img src='image/$user_image' alt='$prenom $nom' class='card__image'>";
            
            // Affichage du nom et prénom de l'utilisateur
            echo "<p class='card__name'>$prenom $nom</p>";

            // Affichage de la ville et du pays de l'utilisateur
            echo "<p>Ville: $ville</p>";
            echo "<p>Pays: $pays</p>";

            echo '<a class="btn draw-border" href="pro_chat.php?email=' . urlencode($email) . '">Message</a>';

            echo "</div>";
        ?>
    </div>
</body>
</html>

<?php
    } else {
        echo "Aucun utilisateur trouvé avec cet email.";
    }

    // Fermeture de la connexion à la base de données
    $conn->close();
} else {
    echo "Aucun email fourni.";
}
?>
