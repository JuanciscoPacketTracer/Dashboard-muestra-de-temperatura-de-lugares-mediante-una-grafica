<?php
require_once __DIR__ . '/../auth.php';
roomtemperature_require_login();
require_once __DIR__ . '/../config/database.php';
if (!isset($conn) || $conn->connect_error) {
    die(json_encode(['error' => 'DB connection failed', 'msg' => $conn->connect_error ?? 'conn not set']));
}
if (empty($LOCATION_IDS)) {
    error_log('RoomTemperature: LOCATION_IDS is empty — check Temperaturas table has rows and Lugares_IdLugar is populated');
}
$sql_count = "SELECT COUNT(*) as Total FROM Temperaturas";
$res_count = $conn->query($sql_count);
$total_registros = '0';
if ($res_count) {
    $rowCount = $res_count->fetch_assoc();
    $total_registros = $rowCount ? number_format($rowCount['Total']) : '0';
}

$location_stats = [];

if (!empty($LOCATION_IDS)) {
    $placeholders = implode(',', array_fill(0, count($LOCATION_IDS), '?'));
    $locTypes = str_repeat('i', count($LOCATION_IDS));

    $sql_stats = "
        SELECT 
            l.NombreLugar, 
            ROUND(AVG(t.ValorTemperatura), 1) as Promedio, 
            MAX(t.ValorTemperatura) as MaxTemp,
            MIN(t.ValorTemperatura) as MinTemp
        FROM Temperaturas t
        JOIN Lugares l ON t.Lugares_IdLugar = l.IdLugar
        WHERE l.IdLugar IN ($placeholders)
        GROUP BY l.IdLugar, l.NombreLugar
    ";
    $stmt_stats = $conn->prepare($sql_stats);

    if ($stmt_stats) {
        $stmt_stats->bind_param($locTypes, ...$LOCATION_IDS);
        $stmt_stats->execute();
        if (method_exists($stmt_stats, 'get_result')) {
            $res_stats = $stmt_stats->get_result();
            if ($res_stats) {
                while ($row = $res_stats->fetch_assoc()) {
                    $location_stats[] = $row;
                }
            }
        } else {
            $stmt_stats->bind_result($nombreLugar, $promedio, $maxTemp, $minTemp);
            while ($stmt_stats->fetch()) {
                $location_stats[] = [
                    'NombreLugar' => $nombreLugar,
                    'Promedio' => $promedio,
                    'MaxTemp' => $maxTemp,
                    'MinTemp' => $minTemp,
                ];
            }
        }
        $stmt_stats->close();
    }
}
