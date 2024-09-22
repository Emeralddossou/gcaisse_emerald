<?php
require 'comptes.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if($_POST['id']){
        $id = $_POST['id'];
        $sql = "DELETE FROM Operations WHERE Id = '$id'";
        $conn->query($sql);
    }else{
        // Récupérer les valeurs du formulaire
        $date = $_POST['date'];
        $operations = $_POST['operations'] ?? '';
        $recettes = $_POST['recettes'] ?? 0;
        $depenses = $_POST['depenses'] ?? 0;

        // Calculer le solde
        $solde_precedent = 0;
        $sql = "SELECT Solde FROM Operations ORDER BY Dates DESC LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $solde_precedent = $row["Solde"];
        }
        $solde = $solde_precedent + $recettes - $depenses;

        // Insérer les données dans la base de données
        $sql = "INSERT INTO Operations (IdUser, Dates, Op, Recettes, Depenses)
                VALUES ('$id_utilisateur', '$date', '$operations', '$recettes', '$depenses')";

        if ($conn->query($sql) === TRUE) {
            $last_inserted_id = $conn->insert_id; // Obtenez l'ID de la dernière insertion
            echo $last_inserted_id;
        } else {
            echo "Erreur: " . $sql . "<br>" . $conn->error;
        }

        // header("Location: first.php");
    }
}

// Récupérer les opérations associées à l'IdCompte spécifié
$sql = "SELECT Id, Dates, Op, Recettes, Depenses FROM operations WHERE IdUser='$id_utilisateur' ORDER BY Dates ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de caisse</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            margin-bottom: 20px;
        }
        form label {
            display: inline-block;
            margin-bottom: 1px;
            width: 20%;
        }
        form input[type="date"],
        form input[type="text"],
        form input[type="number"] {
            width: 35%;
            padding: 5px;
            margin-bottom: 1px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        button, input[type="button"] {
            padding: 10px 20px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button.cancel {
            background-color: #ccc;
            margin-left: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        button.cancel:hover {
            background-color: #bbb;
        }
        .btn-edit,
        .btn-delete {
            padding: 5px 10px;
            margin-right: 5px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-edit:hover,
        .btn-delete:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Gestion de caisse</h1>
    <div style="max-height: 300px;overflow: scroll;">
    <table id="tableau_affichage">
        <tr>
            <th>Date</th>
            <th>Désignation de l'opération</th>
            <th>Recettes</th>
            <th>Dépenses</th>
            <th>Solde</th>
            <th>Actions</th>
        </tr>
        <!-- Les lignes de tableau seront ajoutées dynamiquement ici -->
        <?php
            if ($result->num_rows > 0) {
                $solde = 0;
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
            $conn->close();
        ?>
    </table>
    <?php
    if(@$aucune_operation){
        echo '<span id="aucune_operation">Aucune opération n\'a encore été effectuée.</span>';
    }else{
        echo '<span id="aucune_operation"></span>';
    }
    ?>
    <br><br>
    <a href="#" id="click_end"></a>
    </div>


<button onclick="toggleForm()">Ajouter une opération</button>

<!-- Formulaire pour ajouter une nouvelle ligne "onsubmit="return addOperation(<?php echo $idCompte; ?>)"-->
<form id="form" style="display: none;">
    <label for="date">Date:</label>
    <input type="date" id="date" name="date" required><br><br>
    <label for="operations">Désignation de l'opération:</label>
    <input type="text" id="operations" name="operations" required><br><br>
    <label for="recettes">Recettes:</label>
    <input type="number" step="0.01" id="recettes" name="recettes" onchange="toggleFields()" required><br><br>
    <label for="depenses">Dépenses:</label>
    <input type="number" step="0.01" id="depenses" name="depenses" onchange="toggleFields()" required><br><br>
    <input type="button" onclick="addOperation()" id="addButton" value="Ajouter">
    <button class="cancel" type="button" onclick="cancelEdit()" id="cancelButton" style="display: none;">Quitter la modification</button> <!-- Bouton Quitter la modification -->
<!--     <input type="submit" value="Ajouter"> -->
    <input type="hidden" name ="idUser" value = "1">
</form>
</div>

<script>

    function toggleForm() {
        var form = document.getElementById("form");
        if (form.style.display === "none") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    function addOperation() {
        document.getElementById('click_end').scrollIntoView();

        var formData = new FormData(document.getElementById("form"));

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status == 200) {
                    id_button = xhr.responseText;
                    // Succès de la requête
                    // Recharger la page pour afficher les nouvelles données
                    var updatedData = xhr.responseText;
                    document.getElementById('tableau_affichage').innerHTML = updatedData;
                    document.getElementById('operations').value = '';
                    document.getElementById('recettes').value = '';
                    document.getElementById('depenses').value = '';
                    onPageLoad();
                    if(document.getElementById('aucune_operation') !== null){
                        document.getElementById('aucune_operation').remove();
                    }
                } else {
                    // Gestion des erreurs
                    console.error('Erreur lors de la requête : ' + xhr.status);
                }
            }
        };

        xhr.open("POST", "save_operation.php", true);
        xhr.send(formData);

        return false; // Empêcher le formulaire de soumettre normalement
    }


    function toggleFields() {
        var recettesInput = document.getElementById("recettes");
        var depensesInput = document.getElementById("depenses");

        if (recettesInput.value == "") {
            recettesInput.value = 0; // Réinitialise la valeur de dépenses si recettes est remplie
        } else if (depensesInput.value == "") {
            depensesInput.value = 0; // Réinitialise la valeur de recettes si dépenses est remplie
        } else {
        }
    }

    function deleteOperation(id) {

        if(confirm('Voulez-vous vraiment supprimer cette ligne ?')){
            // Implémentez la logique pour supprimer l'opération avec l'ID donné

            var formData = new FormData();
            formData.append('id', id)

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    if (xhr.status == 200) {
                        // Implémentez la logique pour supprimer l'opération avec l'ID donné
                        var ligne_a_supprimer = document.getElementById(id);
                        if (ligne_a_supprimer) {
                            ligne_a_supprimer.remove();
                            console.log("Suppression de l'opération avec l'ID : " + id);
                            var updatedData = xhr.responseText;
                            document.getElementById('tableau_affichage').innerHTML = updatedData;
                        }
                    } else {
                        // Gestion des erreurs
                        console.error('Erreur lors de la requête : ' + xhr.status);
                    }
                }
            };

            xhr.open("POST", "save_operation.php", true);
            xhr.send(formData);
        }
    }

    function editOperation(id) {
        // Récupérer les données de la ligne sélectionnée à partir de la base de données en utilisant une requête AJAX
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status == 200) {
                    // Succès de la requête
                    var data = JSON.parse(xhr.responseText);
                    // Pré-remplir le formulaire d'ajout avec les valeurs de la ligne sélectionnée
                    document.getElementById("date").value = data.date;
                    document.getElementById("operations").value = data.operations;
                    document.getElementById("recettes").value = data.recettes;
                    document.getElementById("depenses").value = data.depenses;
                    // Changer le bouton "Ajouter" en bouton "Modifier"
                    var addButton = document.getElementById("addButton");
                    addButton.value = "Modifier";
                    addButton.onclick = function() {
                        // Logique pour mettre à jour la ligne dans la base de données
                        updateOperation(id);
                    };
                    document.getElementById('cancelButton').style.display = "inline";
                    var form = document.getElementById("form");
                    if (form.style.display === "none") {
                        form.style.display = "block";
                    }
                } else {
                    // Gestion des erreurs
                    console.error('Erreur lors de la requête : ' + xhr.status);
                }
            }
        };

        xhr.open("GET", "get_operations.php?id=" + id, true);
        xhr.send();
    }

    function updateOperation(id) {
        // Récupérer les valeurs modifiées du formulaire
        var date = document.getElementById("date").value;
        var operations = document.getElementById("operations").value;
        var recettes = parseFloat(document.getElementById("recettes").value) || 0;
        var depenses = parseFloat(document.getElementById("depenses").value) || 0;

        // Effectuer une requête AJAX pour mettre à jour la ligne dans la base de données
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status == 200) {
                    // Succès de la requête
                    // Mettre à jour le tableau sur la page avec les données mises à jour
                    var updatedData = xhr.responseText;
                    document.getElementById('tableau_affichage').innerHTML = updatedData;
                    cancelEdit();
                } else {
                    // Gestion des erreurs
                    console.error('Erreur lors de la requête : ' + xhr.status);
                }
            }
        };

        xhr.open("POST", "update_operations.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("id=" + id + "&date=" + date + "&operations=" + operations + "&recettes=" + recettes + "&depenses=" + depenses);
    }

    function cancelEdit() {
        // Réinitialiser les champs du formulaire
        document.getElementById("operations").value = "";
        document.getElementById("recettes").value = 0;
        document.getElementById("depenses").value = 0;
        // Changer le texte du bouton en "Ajouter"
        var addButton = document.getElementById("addButton");
        addButton.value = "Ajouter";
        addButton.onclick = function() {
            // Logique pour ajouter une nouvelle opération
            addOperation();
        };
        document.getElementById('cancelButton').style.display = "none";
    }

    // Fonction pour obtenir la date actuelle au format JJ/MM/YYYY
    function obtenirDateActuelle() {
        var dateActuelle = new Date();
        var jour = String(dateActuelle.getDate()).padStart(2, '0');
        var mois = String(dateActuelle.getMonth() + 1).padStart(2, '0');
        var annee = dateActuelle.getFullYear();
        return annee + '-' + mois + '-' + jour; // Format YYYY-MM-DD
    }

    // Appeler la fonction pour obtenir la date actuelle et définir la valeur par défaut de l'entrée de date
    document.getElementById('date').value = obtenirDateActuelle();
</script>

</body>
</html>
