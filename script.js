// Function to toggle history table visibility
function toggleHistory() {
    
    var historyContainer = document.getElementById("historyContainer");
    if (historyContainer.style.display === "none" || historyContainer.style.display === "") { 
        historyContainer.style.display = "block"; // Show the history
    } else {
        historyContainer.style.display = "none"; // Hide the history
    }
}
let tempGauge, waterLevelGauge, conductivityGauge; // Declare chart variables globally

// Function to create a gauge chart with Chart.js
function createGaugeChart(ctx, label, value, min, max, color) {
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [label],
            datasets: [{
                data: [value, max - value],
                backgroundColor: [color, "#e0e0e0"],
                borderWidth: 0
            }]  
        },
        options: {
            circumference: 180,
            rotation: 270,
            cutout: '80%',
        },
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const tempCtx = document.getElementById("tempGauge").querySelector("canvas").getContext("2d");
    const waterLevelCtx = document.getElementById("waterLevelGauge").querySelector("canvas").getContext("2d");
    const conductivityCtx = document.getElementById("conductivityGauge").querySelector("canvas").getContext("2d");

    // Create initial charts
    tempGauge = createGaugeChart(tempCtx, "Température", phpData.temperature, 0, 100, "#FF6384");
    waterLevelGauge = createGaugeChart(waterLevelCtx, "Niveau d'eau", phpData.water_level, 0, 100, "#36A2EB");
    conductivityGauge = createGaugeChart(conductivityCtx, "Conductivité", phpData.conductivity, 0, 5000, "#FFCE56");

    // Set interval to refresh the charts every 5 seconds
    setInterval(updateCharts, 5000);
});

// Function to update the charts with the latest data
function updateCharts() {
    console.log("Fetching latest data...");
    fetch('latest_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
            } else {
                // If data is not an array, convert it to an array
                const dataArray = Array.isArray(data) ? data : [data];

                // Update text below each chart with the first record's data
                document.getElementById("tempValue").innerText = `${dataArray[0].temperature}°C`;
                document.getElementById("waterLevelValue").innerText = `${dataArray[0].water_level} cm`;
                document.getElementById("conductivityValue").innerText = `${dataArray[0].conductivity} µS/cm`;

                // Check if chart instances are initialized before updating
                if (tempGauge) {
                    tempGauge.data.datasets[0].data[0] = dataArray[0].temperature;
                    tempGauge.update(); // Redraw the chart
                }

                if (waterLevelGauge) {
                    waterLevelGauge.data.datasets[0].data[0] = dataArray[0].water_level;
                    waterLevelGauge.update(); // Redraw the chart
                }

                if (conductivityGauge) {
                    conductivityGauge.data.datasets[0].data[0] = dataArray[0].conductivity;
                    conductivityGauge.update(); // Redraw the chart
                }

                // Update the history table
                const historyTable = document.getElementById("historyTable");
                historyTable.innerHTML = `
                    <tr>
                        <th>Date et Heure</th>
                        <th>Température</th>
                        <th>Niveau d'eau</th>
                        <th>Conductivité</th>
                    </tr>
                `; // Clear existing rows and add headers

                dataArray.forEach(record => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${record.dateTime}</td>
                        <td>${record.temperature}°C</td>
                        <td>${record.water_level} cm</td>
                        <td>${record.conductivity} µS/cm</td>
                    `;
                    historyTable.appendChild(row); // Append new rows
                });
            }
        })
        .catch(error => console.error("Error fetching latest data:", error));
}

