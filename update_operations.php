<?php
include 'comptes.php';

// Récupérer les valeurs modifiées à partir des paramètres POST
$id = $_POST['id'];
$date = $_POST['date'];
$operations = htmlspecialchars($_POST['operations']);
$recettes = $_POST['recettes'];
$depenses = $_POST['depenses'];

// Mettre à jour les données de l'opération dans la base de données
$sql = "UPDATE operations SET Dates='$date', Op='$operations', Recettes='$recettes', Depenses='$depenses' WHERE Id=$id";

if ($conn->query($sql) === TRUE) {
    // Récupérer les opérations associées à l'IdCompte spécifié
    $sql = "SELECT Id, Dates, Op, Recettes, Depenses FROM operations WHERE IdUser='$id_utilisateur' ORDER BY Dates ASC";
    $result = $conn->query($sql);
    ?>
        <tr>
            <th>Date</th>
            <th>Désignation de l'opération</th>
            <th>Recettes</th>
            <th>Dépenses</th>
            <th>Solde</th>
            <th>Actions</th>
        </tr>
        <?php
        $solde = 0;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $solde += ($row["Recettes"] - $row["Depenses"]);
                echo "<tr id='{$row['Id']}'>";
                echo "<td>" . $row["Dates"] . "</td>";
                echo "<td>" . $row["Op"] . "</td>";
                echo "<td>" . $row["Recettes"] . "</td>";
                echo "<td>" . $row["Depenses"] . "</td>";
                echo "<td>" . $solde . "</td>";
                echo "<td>";
                echo "<button class='btn-edit' onclick='editOperation(" . $row['Id'] . ")'>Modifier</button>";
                echo "<button class='btn-delete' onclick='deleteOperation(" . $row['Id'] . ")'>Supprimer</button>";
                echo "</td>";
                echo "</tr>";
            }
        }
} else {
    echo "Erreur lors de la mise à jour de l'opération: " . $conn->error;
}

$conn->close();
?>
