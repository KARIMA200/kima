<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Commentaires</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .comment-container {
            max-width: 600px;
            margin: 20px auto;
        }
        .comment {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .user {
            font-weight: bold;
            color: #385898;
        }
        .content {
            margin-top: 5px;
            color: #333;
        }
        .timestamp {
            color: #999;
            font-size: 12px;
        }
        .reply-form {
            display: none;
            margin-top: 10px;
        }
        .reply-form textarea {
            width: calc(100% - 40px);
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: none;
        }
        .comment-options {
            margin-top: 5px;
        }
        .comment-options a {
            margin-right: 10px;
            color: #385898;
            text-decoration: none;
            cursor: pointer;
        }
        .comment-options a:hover {
            text-decoration: underline;
        }
        .image-icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            background-color: #ddd;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            color: #555;
            font-size: 14px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="comment-container">
        <!-- Exemple de commentaire -->
        <div class="comment">
            <div class="user">John Doe</div>
            <div class="content">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam.</div>
            <div class="timestamp">Il y a 5 minutes</div>
            <div class="comment-options">
                <a href="#" class="like">J'aime</a>
                <a href="#" class="reply">Répondre</a>
                <a href="#" class="delete">Supprimer</a>
            </div>
            <div class="reply-form">
                <form action="post_reply.php" method="POST">
                    <textarea name="reply" rows="2" placeholder="Répondre..."></textarea>
                    <label for="file-upload" class="image-icon">&#128247;</label>
                    <input id="file-upload" type="file" name="image" accept="image/*" style="display: none;">
                    <button type="submit">Envoyer</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Afficher/masquer le formulaire de réponse
        var replyLinks = document.querySelectorAll('.reply');
        replyLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var replyForm = this.parentElement.nextElementSibling;
                if (replyForm.style.display === 'block') {
                    replyForm.style.display = 'none';
                } else {
                    replyForm.style.display = 'block';
                }
            });
        });

        // Supprimer le commentaire
        var deleteLinks = document.querySelectorAll('.delete');
        deleteLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var comment = this.parentElement.parentElement;
                comment.remove();
            });
        });
    </script>
</body>
</html>
