<?php
// Paramètres de connexion à la base de données
$hostname = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "techguard";  // Nom de la base de données à vérifier

// Connexion au serveur MySQL
$conn = mysqli_connect($hostname, $username, $password);

if (!$conn) { 
    die("Connexion au serveur MySQL échouée : " . mysqli_connect_error());
} 

// Vérifiez si la base de données existe et sélectionnez-la
$db_selected = mysqli_select_db($conn, $database);

if (!$db_selected) {
    die("Impossible de sélectionner la base de données '$database' : " . mysqli_error($conn));
} 

echo "Connexion à la base de données réussie<br>"; 

// Vérifiez si les données POST sont définies et nettoyez les entrées
if (isset($_POST["temperature"]) && isset($_POST["conductivity"]) && isset($_POST["water_level"])) {

    // Nettoyez les entrées pour éviter l'injection SQL
    $temperature = mysqli_real_escape_string($conn, $_POST["temperature"]);
    $conductivity = mysqli_real_escape_string($conn, $_POST["conductivity"]);
    $water_level = mysqli_real_escape_string($conn, $_POST["water_level"]);

    // Insérez les données dans la base de données
    $sql = "INSERT INTO dht11 (temperature, conductivity, water_level) VALUES ('$temperature', '$conductivity', '$water_level')";

    if (mysqli_query($conn, $sql)) { 
        echo "Nouveau enregistrement créé avec succès"; 
    } else { 
        echo "Erreur lors de l'exécution de la requête : " . mysqli_error($conn); 
    }
} else {
    echo "Les données de température, de conductivité ou de niveau d'eau sont manquantes.<br>";
}

// Fermez la connexion à la base de données
mysqli_close($conn);
?>
