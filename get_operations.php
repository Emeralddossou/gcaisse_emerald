<?php
$no_compte = true;
include 'comptes.php';

// Récupérer l'ID de l'opération à partir des paramètres GET
$id = $_GET['id'];

// Sélectionner les données de l'opération correspondante
$sql = "SELECT * FROM operations WHERE Id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Récupérer les données de l'opération
    $row = $result->fetch_assoc();
    $operationData = array(
        'date' => $row['Dates'],
        'operations' => $row['Op'],
        'recettes' => $row['Recettes'],
        'depenses' => $row['Depenses']
    );
    // Renvoyer les données sous forme de JSON
    echo json_encode($operationData);
} else {
    echo "Opération non trouvée";
}
$conn->close();
?>
