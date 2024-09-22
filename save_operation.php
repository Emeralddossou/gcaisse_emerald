<?php
require 'comptes.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(@$_POST['id']){
        $id = $_POST['id'];
        $sql = "DELETE FROM operations WHERE Id = '$id'";
        $conn->query($sql);
        // Récupérer les opérations associées à l'IdCompte spécifié
    }else{
        // Récupérer les valeurs du formulaire
        $date = $_POST['date'];
        $operations = htmlspecialchars($_POST['operations'] ?? '');
        $recettes = $_POST['recettes'] ?? 0;
        $depenses = $_POST['depenses'] ?? 0;

        // Calculer le solde
        $solde_precedent = 0;
        $sql = "SELECT Solde FROM operations ORDER BY Dates DESC LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $solde_precedent = $row["Solde"];
        }
        $solde = $solde_precedent + $recettes - $depenses;

        // Insérer les données dans la base de données
        $sql = "INSERT INTO operations (IdUser, Dates, Op, Recettes, Depenses)
                VALUES ('$id_utilisateur', '$date', '$operations', '$recettes', '$depenses')";

        if ($conn->query($sql) === TRUE) {

        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }

        // header("Location: first.php");
    }

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
            } else {
                // echo "<tr><td colspan='5'>Aucune opération n'a encore été effectuée dans ce journal</td></tr>";
                $aucune_operation = true;
            }
    // Fermer la connexion
    $conn->close();
}
?>
