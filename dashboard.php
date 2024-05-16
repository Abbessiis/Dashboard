<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TechGuard Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">    
</head>
<body>
    <div class="navbar">
        <img src="logo.png" alt="TechGuard Logo" class="logo">
        <div class="datetime" id="datetime"></div>
    </div>
    <div class="content">
        <div class="chart-container">
            <?php
            // Database connection and data fetching
            $hostname = "localhost"; 
            $username = "root"; 
            $password = ""; 
            $database = "techguard"; 
            $conn = new mysqli($hostname, $username, $password, $database);

            if ($conn->connect_error) {
                die("Erreur de connexion : " . $conn->connect_error);
            }

            $sql = "SELECT temperature, water_level, conductivity, DateTime FROM dht11 ORDER BY DateTime DESC LIMIT 1";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // JSON data for JavaScript
                $data = [
                    'temperature' => $row['temperature'],
                    'water_level' => $row['water_level'],
                    'conductivity' => $row['conductivity'],
                ];
                $jsonData = json_encode($data);
            } else {
                $jsonData = json_encode(["error" => "No data found"]);
            }

            $conn->close();
            ?>
            <!-- Pass JSON data to JavaScript -->
            <script>
                const phpData = <?php echo $jsonData; ?>; // JSON data from PHP
            </script>

            <div class="chart-container"> <!-- Container for the charts -->
                <!-- Temperature Gauge and its data -->
                <div class="chart-section">
                    <div class="gauge-chart" id="tempGauge"><canvas></canvas></div>
                    <p id="tempValue">--°C</p> <!-- Placeholder for temperature data -->
                </div>

                <!-- Water Level Gauge and its data -->
                <div class="chart-section">
                    <div class="gauge-chart" id="waterLevelGauge"><canvas></canvas></div>
                    <p id="waterLevelValue">--cm</p> <!-- Placeholder for water level data -->
                </div>

                <!-- Conductivity Gauge and its data -->
                <div class="chart-section">
                    <div class="gauge-chart" id="conductivityGauge"><canvas></canvas></div>
                    <p id="conductivityValue">--µS/cm</p> <!-- Placeholder for conductivity data -->
                </div>
            </div>
        </div>

        <!-- Button to show/hide history table -->
        <button class="toggle-button" onclick="toggleHistory()">
            <i class="fas fa-history"></i>Afficher l'historique
        </button>
        
        <!-- History table, initially hidden -->
        <div class="history" id="historyContainer" style="display: none;"> 
            <table id="historyTable" class="history-table">
                <tr>
                    <th>Date et Heure</th>
                    <th>Température</th>
                    <th>Niveau d'eau</th>
                    <th>Conductivité</th>
                </tr>
                <?php
                // Fetch last 10 records for the history table
                $conn = new mysqli($hostname, $username, $password, $database);

                if ($conn->connect_error) {
                    die("Erreur de connexion : " . $conn->connect_error); 
                }
                $sql = "SELECT temperature, water_level, conductivity, DateTime FROM dht11 ORDER BY DateTime DESC LIMIT 10";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['DateTime'] . "</td>";
                        echo "<td>" . $row['temperature'] . "°C</td>";
                        echo "<td>" . $row['water_level'] . "cm</td>";
                        echo "<td>" . $row['conductivity'] . "µS/cm</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Aucune donnée trouvée.</td></tr>";
                }

                $conn->close();            
                ?>
            </table>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-links">
            <a href="https://www.tn.kbe-elektrotechnik.com/fr/entreprise" target="_blank">
                <img src="kbelogo.png" alt="KB Elektrotechnik Logo" class="footer-logo" style="width: 50px; height:45px;">
            </a>
        </div>

        <div class="footer-contacts"> <!-- Horizontal Flex Layout -->
            <div class="contact-info"> <!-- Container for each contact -->
                <strong><i class="fas fa-user"></i> MOHSEN BOUMIZA | Gérant</strong>
                <p><i class="fas fa-phone"></i> +216 73 322 547</p>
                <p><i class="fas fa-fax"></i> +216 73 322 548</p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:mohsen.boumiza@kbe-elektrotechnik.com">mohsen.boumiza@kbe-elektrotechnik.com</a></p>
            </div>

            <div class="contact-info"> <!-- Container for each contact -->
                <strong><i class="fas fa-user"></i> HOUCINE MANAA | Directeur Qualité & Dir. General</strong>
                <p><i class="fas fa-phone"></i> +216 73 322 547</p>
                <p><i class="fas fa-fax"></i> +216 73 322 548</p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:houcine.manaa@kbe-elektrotechnik.com">houcine.manaa@kbe-elektrotechnik.com</a></p>
            </div>

            <div class="contact-info"> <!-- Container for each contact -->
                <strong><i class="fas fa-user"></i> MAHER CHAHED | Ingénieur technico-commercial</strong>
                <p><i class="fas fa-phone"></i>  +216 73 322 547</p>
                <p><i class="fas fa-fax"></i> +216 73 322 548</p>
                <p<i class="fas fa-envelope"></i> <a href="mailto:maher.chahed@kbe-elektrotechnik.com">maher.chahed@kbe-elektrotechnik.com</a></p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-doughnut-gauge@0.3.0"></script>
    <script src="script.js"></script>
    <script>
        function updateDateTime() {
            const now = new Date();
            const dateTimeString = now.toLocaleDateString('fr-FR') + ' ' + now.toLocaleTimeString('fr-FR');
            document.getElementById('datetime').textContent = dateTimeString;
        }
        setInterval(updateDateTime, 1000);
        updateDateTime(); // Initial call to set date and time immediately
    </script>
</body>
</html>
