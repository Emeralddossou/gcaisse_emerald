<?php
// Deuxièmes identifiants
$servername2 = 'localhost';
$username2 = 'root';
$password2 = '';
$dbname2 = 'operations';

try {
    // Essayer la connexion avec les premiers identifiants
    @$conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier si la connexion a réussi
    if ($conn->connect_error) {
        // Si la connexion avec les premiers identifiants échoue, lancer une exception
        throw new Exception("Échec de la connexion avec les premiers identifiants");
    } else {
        // Si la connexion avec les premiers identifiants réussit, vous pouvez utiliser $conn pour effectuer des requêtes SQL
        //echo "Connexion réussie avec les premiers identifiants";
    }
} catch (Exception $e) {
    // Si une exception est attrapée (c'est-à-dire que la connexion avec les premiers identifiants a échoué), essayer avec les seconds identifiants
    @$conn = new mysqli($servername2, $username2, $password2, $dbname2);

    // Vérifier si la connexion avec les seconds identifiants a réussi
    if ($conn->connect_error) {
        // Si la connexion avec les deux ensembles d'identifiants échoue, afficher un message d'erreur
        die("Échec de la connexion avec les seconds identifiants : " . $conn->connect_error);
    } else {
        // Si la connexion avec les seconds identifiants réussit, vous pouvez utiliser $conn pour effectuer des requêtes SQL
        //echo "Connexion réussie avec les seconds identifiants";
    }
}
?>