<?php
$servername = "localhost";
$username = "root";
$password = "rootroot";
$dbname = "roomtemperaturedb";

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
if ($resLocations) {
    while ($row = $resLocations->fetch_assoc()) {
        $LOCATION_IDS[] = (int)$row['IdLugar'];
    }
}
