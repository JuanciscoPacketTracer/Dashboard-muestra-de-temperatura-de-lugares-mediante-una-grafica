<?php
require_once __DIR__ . '/../config/database.php';

$sql_count = "SELECT COUNT(*) as Total FROM Temperaturas";
$res_count = $conn->query($sql_count);
$total_registros = '0';
if ($res_count) {
    $rowCount = $res_count->fetch_assoc();
    $total_registros = $rowCount ? number_format($rowCount['Total']) : '0';
}

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
$location_stats = [];

if ($stmt_stats) {
    $stmt_stats->bind_param($locTypes, ...$LOCATION_IDS);
    $stmt_stats->execute();
    $res_stats = $stmt_stats->get_result();

    while ($row = $res_stats->fetch_assoc()) {
        $location_stats[] = $row;
    }
    $stmt_stats->close();
}
