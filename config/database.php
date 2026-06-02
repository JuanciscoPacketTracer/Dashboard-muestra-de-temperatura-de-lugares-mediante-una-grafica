<?php
$servername = "localhost";
$username = "user23060120";
$password = "rootroot23060120";
$dbname = "roomtemperaturedb";

// Avoid hard fatals on servers where mysqli is configured to throw exceptions.
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

$LOCATION_IDS = [];
$sqlLocations = "
    SELECT DISTINCT t.Lugares_IdLugar AS IdLugar
    FROM Temperaturas t
    ORDER BY t.Lugares_IdLugar
";

$resLocations = $conn->query($sqlLocations);
if ($resLocations instanceof mysqli_result) {
    while ($row = $resLocations->fetch_assoc()) {
        $LOCATION_IDS[] = (int)$row['IdLugar'];
    }
} else {
    error_log('RoomTemperature warning: could not load LOCATION_IDS from Temperaturas. ' . $conn->error);
}
