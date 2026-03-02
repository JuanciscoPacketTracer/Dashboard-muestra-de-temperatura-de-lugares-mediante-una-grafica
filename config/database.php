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
$LOCATION_IDS = [4, 13, 14];
