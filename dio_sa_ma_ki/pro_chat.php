<?php
session_start();

// Vérifier si l'email est présent dans la requête GET
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

    // Récupérer l'e-mail de la requête GET
    $email = $_GET['email'];

    // Requête SQL pour rechercher dans la table vendeurs
    $sql_vendeurs = "SELECT id, nom, prenom, user_image FROM vendeurs WHERE email = '$email'";
    $result_vendeurs = $conn->query($sql_vendeurs);

    // Vérifier s'il y a des résultats dans la table vendeurs
    if ($result_vendeurs->num_rows > 0) {
        // Récupérer les informations du vendeur
        $row_vendeur = $result_vendeurs->fetch_assoc();
        $id_utilisateur = $row_vendeur["id"];
        $nom_utilisateur = $row_vendeur["nom"];
        $prenom_utilisateur = $row_vendeur["prenom"];
        $user_image = $row_vendeur["user_image"];
        $type_utilisateur = "Vendeur"; // Indique que c'est un vendeur
    } else {
        // Si aucun résultat dans la table vendeurs, rechercher dans la table clients
        $sql_clients = "SELECT id, nom, prenom, user_image FROM clients WHERE email = '$email'";
        $result_clients = $conn->query($sql_clients);

        // Vérifier s'il y a des résultats dans la table clients
        if ($result_clients->num_rows > 0) {
            // Récupérer les informations du client
            $row_client = $result_clients->fetch_assoc();
            $id_utilisateur = $row_client["id"];
            $nom_utilisateur = $row_client["nom"];
            $prenom_utilisateur = $row_client["prenom"];
            $user_image = $row_client["user_image"];
            $type_utilisateur = "Client"; // Indique que c'est un client
        } else {
            // Si aucun résultat dans la table vendeurs ni dans la table clients, afficher un message d'erreur
            echo "Aucun utilisateur trouvé avec cet e-mail.";
            exit;
        }
    }

    // Fermer la connexion à la base de données
    $conn->close();
} else {
    echo "Aucun e-mail fourni.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoyer un message</title>
    <link rel="stylesheet" href="css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        .chat-container {
            max-width: 500px;
            margin: 20px auto;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .chat-header {
            background: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
        }

        .chat-header .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 10px;
        }

        .chat-header .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chat-header span {
            font-size: 18px;
        }

        .chat-messages {
            padding: 20px;
            max-height: 300px;
            overflow-y: auto;
        }

        .chat-input {
            padding: 10px;
            display: flex;
            align-items: center;
            background: #f9f9f9;
        }

        .chat-input textarea {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
            margin-right: 10px;
        }

        .chat-input button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .chat-input button:hover {
            background-color: #45a049;
        }

        .chat-input i {
            font-size: 20px;
            margin-right: 10px;
            cursor: pointer;
        }

        .record-item {
            margin-top: 10px;
            display: flex;
            align-items: center;
        }

        .record-item img {
            width: 100px;
            height: auto;
            margin-right: 10px;
        }

        .delete-icon {
            color: #FF0044;
            cursor: pointer;
            font-size: 18px;
        }

        .recording {
            background-color: #FF0044;
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <div class="user-avatar">
                <?php
                // Afficher l'image de l'utilisateur récupérée précédemment
                echo '<img src="image/' . $user_image . '" alt="' . $prenom_utilisateur . ' ' . $nom_utilisateur . '">';
                ?>
            </div>
            <span><?php echo $prenom_utilisateur . " " . $nom_utilisateur; ?></span>
        </div>
        <div class="chat-messages">
            <!-- Messages à afficher seront ajoutés ici via JavaScript -->
        </div>
        <div class="chat-input">
            <form id="messageForm" action="upload.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="123">
                <input type="hidden" name="vendeur_id" value="<?php echo $id_utilisateur; ?>">
                <input type="hidden" name="email_v" value="<?php echo $email; ?>">
                <textarea id="messageInput" name="message" placeholder="Type your message..."></textarea>
                <div id="recordingsList"></div>        
                <i id="startRecording" class="fas fa-microphone"></i>
                <i id="image" class="fa-solid fa-file"></i>
                <input type="file" name="audio" id="audioInput" accept="audio/*" style="display: none;">
                <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;">
                <button id="sendRecording">Send </button>
            </form>
        </div>
    </div>
    <script>
        // Votre JavaScript ici
    </script>
</body>
</html>



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
</body>
</html>


