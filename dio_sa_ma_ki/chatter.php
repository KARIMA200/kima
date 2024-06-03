<?php
session_start();
// Vérifie si le paramètre "other" est présent dans l'URL
// Vérification de la présence du paramètre "other"
$utilisateur1 = $_GET['email'];
$nom = $_GET['nom'];
$prenom = $_GET['prenom'];
$image = $_GET['user_image'];

// Utilisez ces valeurs comme vous le souhaitez dans votre code


// Vérification si l'utilisateur est connecté
if (!isset($_SESSION["email"])) {
    header("Location: login.php"); // Redirection vers la page de connexion si non connecté
    exit;
}

// Récupération de l'email de l'utilisateur à partir de la session
$utilisateur2 = $_SESSION["email"];

// Fonction pour récupérer les informations utilisateur
function getUserInfo($conn, $email) {
    $sql_client = "SELECT nom, prenom, user_image FROM clients WHERE email = '$email'";
    $sql_vendeur = "SELECT nom, prenom, user_image FROM vendeurs WHERE email = '$email'";
    
    $result_client = $conn->query($sql_client);
    if ($result_client->num_rows > 0) {
        return $result_client->fetch_assoc();
    }
    
    $result_vendeur = $conn->query($sql_vendeur);
    if ($result_vendeur->num_rows > 0) {
        return $result_vendeur->fetch_assoc();
    }
    
    return null;
}

// Vérification si l'autre utilisateur est envoyé par POST
if (isset($_GET["email"])) {
    // Récupération de l'autre utilisateur à partir de POST
    $utilisateur1 =  $_GET['email'];

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ecommerce";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("Erreur de connexion à la base de données: " . $conn->connect_error);
    }

    // Récupération des informations utilisateur
    $info_utilisateur1 = getUserInfo($conn, $utilisateur1);
    $info_utilisateur2 = getUserInfo($conn, $utilisateur2);

    // Requête SQL pour sélectionner tous les messages entre l'utilisateur actuel et l'autre utilisateur, triés par date ascendant
    $sql = "SELECT contenu, date_envoi, expediteur FROM messages WHERE (expediteur = '$utilisateur1' AND destinataire = '$utilisateur2') OR (expediteur = '$utilisateur2' AND destinataire = '$utilisateur1') ORDER BY date_envoi ASC";

    $result = $conn->query($sql);
    $update_sql = "UPDATE messages SET lu = 1 WHERE destinataire = '$utilisateur2' AND expediteur = '$utilisateur1' AND lu = 0";
    $conn->query($update_sql);
    ?>
                        
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chat</title>
        <link rel="stylesheet" href="css/all.min.css">
        <style>
          body {
            background: #222;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Pour masquer la barre de défilement horizontale */
        }

        .container {
            max-width: 9cm; /* Réduire la largeur de la page */
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto; /* Ajouter une barre de défilement verticale si le contenu dépasse */
            max-height: 80vh; /* Limiter la hauteur de la page */
        }
            .message {
                display: flex;
                align-items: center;
                margin-bottom: 10px;
            }

            .message .user-info {
                display: flex;
                align-items: center;
            }

            .message .user-info img {
                border-radius: 50%;
                width: 40px;
                height: 40px;
                margin-right: 10px;
            }

            .message .user-info .details {
                display: flex;
                flex-direction: column;
            }

            .message .messageText {
                background-color: #FF0044;
                color: #fff;
                padding: 10px;
                border-radius: 10px;
                position: relative;
                max-width: 70%;
            }

            .message.sol {
                justify-content: flex-start;
            }

            .message.sag {
                justify-content: flex-end;
            }

            .message.sol .messageText {
                border-bottom-left-radius: 0;
            }

            .message.sag .messageText {
                border-bottom-right-radius: 0;
            }

            .message .messageText:before {
                content: '';
                position: absolute;
                border-style: solid;
            }

            .message.sol .messageText:before {
                top: 0;
                left: -10px;
                border-width: 10px 10px 10px 0;
                border-color: transparent #FF0044 transparent transparent;
            }

            .message.sag .messageText:before {
                top: 0;
                right: -10px;
                border-width: 10px 0 10px 10px;
                border-color: transparent transparent transparent #FF0044;
            }

            .message .messageText:after {
                content: attr(data-time);
                display: block;
                font-size: 0.8em;
                color: rgba(255, 255, 255, 0.7);
                margin-top: 5px;
            }

            textarea {
                width: calc(100% - 20px);
                margin-bottom: 10px;
                padding: 10px;
                border-radius: 5px;
                border: 1px solid #ccc;
                resize: vertical;
            }

            button {
                padding: 10px 20px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            button:hover {
                background-color: #45a049;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div id="messages">
                <div class="message">
                    <div class="user-info">
                        <?php
                        $image_path = "image/" . $image;
                        if (file_exists($image_path) && is_readable($image_path)) {
                            echo '<img src="' . $image_path . '" alt="' . $info_utilisateur1['prenom'] . '" class="logo1" id="logo-clickable">';
                        } else {
                            echo '<img src="chemin_vers_image_par_defaut/default_image.jpg" alt="Image par défaut" class="logo1" id="logo-clickable">';
                        }
                        ?>
                        <div class="details">
                            <p><?php echo $nom . ' ' . $prenom; ?></p>
                        </div>
                    </div>
                </div>

                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $classe_message = ($row['expediteur'] == $utilisateur1) ? "sol" : "sag";
                        $info_utilisateur = ($row['expediteur'] == $utilisateur1) ? $info_utilisateur1 : $info_utilisateur2;
                        ?>
                        <div class="message <?php echo $classe_message; ?>">
                            <div class="user-info">
                                <?php
                                $image_path = "image/" . $info_utilisateur['user_image'];
                                if (file_exists($image_path) && is_readable($image_path)) {
                                    echo '<img src="' . $image_path . '" alt="' . $info_utilisateur['prenom'] . '" class="logo1" id="logo-clickable">';
                                } else {
                                    echo '<img src="chemin_vers_image_par_defaut/default_image.jpg" alt="Image par défaut" class="logo1" id="logo-clickable">';
                                }
                                ?>
                                <div class="details">
                                    <!-- Affichage du nom et prénom -->
                                 
                                </div>
                            </div>
                            <div class="messageText" data-time="<?php echo $row['date_envoi']; ?>">
    <?php 
    $contenu = $row['contenu'];
    if (strpos($contenu, 'images/') === 0) {
        // Le message est une image
        echo '<img src="' . $contenu . '" alt="Image">';
    } elseif (strpos($contenu, 'uploads/') === 0) {
        // Le message est un fichier audio
        $audio_path = 'uploads/' . basename($contenu); // Récupérer le nom de fichier audio
        if (file_exists($audio_path) && is_readable($audio_path)) {
            echo '<audio controls>
                    <source src="' . $audio_path . '" type="audio/mpeg">
                    Your browser does not support the audio element.
                  </audio>';
        } else {
            echo 'Le fichier audio n\'est pas disponible.';
        }
    } else {
        // Le message est du texte
        echo $contenu;
    }
    ?>
</div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>Aucun message trouvé.</p>";
                }
                ?>
            </div>
            <form id="messageForm" action="envoyer_message2.php" method="post">
          
                <textarea name="message" id="message" rows="4" placeholder="Écrire un message"></textarea>
                <input type="hidden" name="other_user" value="<?php echo htmlspecialchars($utilisateur1); ?>">
                <div id="recordingsList"></div>        
    <i id="startRecording" class="fas fa-microphone"></i>
    <i id="image" class="fa-solid fa-file"></i>
    <input type="file" name="audio" id="audioInput" accept="audio/*" style="display: none;">
    <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;">
                <button id="sendRecording" type="submit">send</button>
            </form>
        </div>
    </body>
    <script>
        let mediaRecorder;
        let recordedChunks = [];
        let isRecording = false;

        document.getElementById('startRecording').addEventListener('click', function() {
            if (!isRecording) {
                startRecording();
            } else {
                stopRecording();
            }
        });

        document.getElementById('sendRecording').addEventListener('click', sendRecording);

        function startRecording() {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(function(stream) {
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.start();

                    isRecording = true;
                    document.getElementById('startRecording').classList.add('recording');
                    document.getElementById('startRecording').classList.remove('fa-microphone');
                    document.getElementById('startRecording').classList.add('fa-stop');

                    mediaRecorder.addEventListener('dataavailable', function(event) {
                        recordedChunks.push(event.data);
                    });

                    mediaRecorder.addEventListener('stop', function() {
                        isRecording = false;
                        document.getElementById('startRecording').classList.remove('recording');
                        document.getElementById('startRecording').classList.add('fa-microphone');
                        document.getElementById('startRecording').classList.remove('fa-stop');

                        let blob = new Blob(recordedChunks, { type: 'audio/wav' });
                        let url = URL.createObjectURL(blob);
                        let audio = document.createElement('audio');
                        audio.controls = true;
                        audio.src = url;

                        // Créer un élément pour chaque enregistrement avec une icône de poubelle
                        let recordingItem = document.createElement('div');
                        recordingItem.classList.add('recording-item');
                        recordingItem.appendChild(audio);
                        
                        let deleteIcon = document.createElement('i');
                        deleteIcon.classList.add('fas', 'fa-trash', 'delete-icon');
                        deleteIcon.addEventListener('click', function() {
                            // Supprimer l'enregistrement de la liste
                            recordingItem.remove();
                        });
                        recordingItem.appendChild(deleteIcon);

                        document.getElementById('recordingsList').appendChild(recordingItem);
                    });
                });
        }

        function stopRecording() {
            mediaRecorder.stop();
        }

        function sendRecording() {
            if (recordedChunks.length === 0) {
                alert("No recording to send!");
                return;
            }

            let blob = new Blob(recordedChunks, { type: 'audio/wav' });

            let formData = new FormData();
            formData.append('audio', blob, 'recording.wav');

            // Set session variable for the audio path
            let currentDate = new Date();
            let formattedDate = currentDate.getFullYear() + 
                                ("0" + (currentDate.getMonth() + 1)).slice(-2) + 
                                ("0" + currentDate.getDate()).slice(-2) + 
                                ("0" + currentDate.getHours()).slice(-2) + 
                                ("0" + currentDate.getMinutes()).slice(-2);
            let audioPath = formattedDate + '.' + '<?php echo $_SESSION["email"]; ?>';
            formData.append('audio_path', audioPath);

            let xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        let filePath = xhr.responseText;
                        console.log("File path:", filePath);
                        // Stocker le chemin du fichier dans une variable de session
                        <?php $_SESSION['audio_path'] = '<script>document.write(audioPath)</script>'; ?>
                    } else {
                        console.error('Error:', xhr.status);
                    }
                }
            };

            xhr.open('POST', 'upload.php', true);
            xhr.send(formData);
        }

        document.getElementById('image').addEventListener('click', function() {
            document.getElementById('imageInput').click();
        });

        document.getElementById('imageInput').addEventListener('change', function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function() {
                    displayImage(reader.result);
                };
                reader.readAsDataURL(file);
            }
        });

        function displayImage(imageUrl) {
            let image = document.createElement('img');
            image.src = imageUrl;

            let deleteIcon = document.createElement('i');
            deleteIcon.classList.add('fa-solid', 'fa-trash', 'delete-icon');
            deleteIcon.addEventListener('click', function() {
                // Supprimer l'image
                image.parentNode.removeChild(image);
                deleteIcon.parentNode.removeChild(deleteIcon);
            });

            let recordItem = document.createElement('div');
            recordItem.classList.add('record-item');
            recordItem.appendChild(image);
            recordItem.appendChild(deleteIcon);

            document.getElementById('recordingsList').appendChild(recordItem);
        }

        document.getElementById('sendRecording').addEventListener('click', function() {
            let image = document.querySelector('#recordingsList img');
            if (image) {
                // Envoi de l'image au serveur
                let formData = new FormData();
                formData.append('image', image.src);

                let xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            console.log('Image envoyée avec succès');
                            // Réinitialiser la liste des enregistrements après l'envoi
                            document.getElementById('recordingsList').innerHTML = '';
                        } else {
                            console.error('Erreur lors de l\'envoi de l\'image:', xhr.status);
                        }
                    }
                };

                xhr.open('POST', 'upload_image.php', true);
                xhr.send(formData);
            } else {
                console.log('Aucune image à envoyer.');
            }
        });
    </script>
    </html>
    <?php
} else {
    // Rediriger vers une page d'erreur si l'autre utilisateur n'est pas fourni
    echo "Erreur : l'autre utilisateur n'est pas spécifié.";
    exit;
}
$conn->close();
?>
