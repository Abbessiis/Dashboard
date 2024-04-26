<?php
$hostname = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "techguard"; 
$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Fetch multiple records
$sql = "SELECT temperature, water_level, conductivity, DateTime FROM dht11 ORDER BY DateTime DESC LIMIT 10"; // Get the last 10 records
$result = $conn->query($sql);

$data = []; // Initialize an array
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'dateTime' => $row['DateTime'],
            'temperature' => $row['temperature'],
            'water_level' => $row['water_level'],
            'conductivity' => $row['conductivity'],
        ];
    }
} else {
    $data = ["error" => "No data found"]; // Error message if no data
}

echo json_encode($data); // Return JSON-formatted array
$conn->close();
?>
