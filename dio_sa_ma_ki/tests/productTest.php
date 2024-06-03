<?php

use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testInsertProductSuccess()
    {
        // Créez une instance de votre script pour le tester
        // Inclure le fichier à tester
require_once __DIR__ . '/../src/votre_script.php';

// Maintenant, vous pouvez accéder aux fonctions et aux classes définies dans votre script pour les tester
// Remplacez 'votre_script.php' par le chemin vers votre fichier

        // Simulez les données du formulaire
        $_SERVER["REQUEST_METHOD"] = "POST";
        $_POST['nom_produit'] = 'Nom du produit';
        $_POST['description_produit'] = 'Description du produit';
        $_POST['prix_produit'] = 'Prix du produit';
        $_POST['stock'] = 'Stock du produit';
        $_POST['categorie'] = 'Catégorie du produit';
        $_FILES['image']['name'] = 'image.jpg';
        $_FILES['image']['tmp_name'] = 'chemin_de_l_image.jpg';
        $_FILES['image']['error'] = UPLOAD_ERR_OK;

        // Exécutez la méthode de votre script
        ob_start(); // Capture la sortie de l'echo
        insertProduct(); // Appeler la fonction qui insère le produit
        $output = ob_get_clean(); // Récupérer la sortie de l'echo

        // Vérifiez si le message de succès est affiché
        $this->assertStringContainsString('Nouveau produit ajouté avec succès.', $output);
    }

    public function testInsertProductFailure()
    {
        // Créez une instance de votre script pour le tester
        // Inclure le fichier à tester
require_once __DIR__ . '/../src/votre_script.php';

// Maintenant, vous pouvez accéder aux fonctions et aux classes définies dans votre script pour les tester
// Remplacez 'votre_script.php' par le chemin vers votre fichier

        // Simulez les données du formulaire (par exemple, des données manquantes)
        $_SERVER["REQUEST_METHOD"] = "POST";
        // Ne définissez pas les données du formulaire pour provoquer un échec
        // ...

        // Exécutez la méthode de votre script
        ob_start(); // Capture la sortie de l'echo
        insertProduct(); // Appeler la fonction qui insère le produit
        $output = ob_get_clean(); // Récupérer la sortie de l'echo

        // Vérifiez si le message d'erreur est affiché
        $this->assertStringContainsString('Erreur lors de l\'ajout du produit', $output);
    }
}
