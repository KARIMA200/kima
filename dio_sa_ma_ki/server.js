const express = require('express');
const app = express();
const port = 3000;

// Endpoint pour recevoir les fichiers audio
app.post('/upload', (req, res) => {
    // Code pour gérer l'upload du fichier audio et le stocker dans la base de données
    console.log('Fichier audio reçu sur le serveur.');
    res.send('Fichier audio reçu avec succès!');
});

app.listen(port, () => {
    console.log(`Serveur à l'écoute sur http://localhost:${port}`);
});
