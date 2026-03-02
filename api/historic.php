<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime('-7 days'));
$to   = isset($_GET['to'])   ? $_GET['to']   : date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format. Use YYYY-MM-DD.']);
    $conn->close();
    exit;
}

$fromFull = $from . ' 00:00:00';
$toFull   = $to   . ' 23:59:59';

$diffDays = (new DateTime($from))->diff(new DateTime($to))->days;

if ($diffDays <= 2) {
    $dateFormat  = '%Y-%m-%d %H:%i:00';
    $granularity = 'minute';
} elseif ($diffDays <= 30) {
    $dateFormat  = '%Y-%m-%d %H:00:00';
    $granularity = 'hour';
} else {
    $dateFormat  = '%Y-%m-%d';
    $granularity = 'day';
}

$placeholders = implode(',', array_fill(0, count($LOCATION_IDS), '?'));
$locTypes = str_repeat('i', count($LOCATION_IDS));

$sql = "
    SELECT 
        l.NombreLugar, 
        DATE_FORMAT(t.FechaTemperatura, '$dateFormat') AS Fecha, 
        ROUND(AVG(t.ValorTemperatura), 1) AS PromedioTemp
    FROM Temperaturas t
    JOIN Lugares l ON t.Lugares_IdLugar = l.IdLugar 
    WHERE l.IdLugar IN ($placeholders)
      AND t.FechaTemperatura >= ?
      AND t.FechaTemperatura <= ?
    GROUP BY l.IdLugar, l.NombreLugar, DATE_FORMAT(t.FechaTemperatura, '$dateFormat')
    ORDER BY Fecha ASC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Query preparation failed: ' . $conn->error]);
    $conn->close();
    exit;
}

$params = array_merge($LOCATION_IDS, [$fromFull, $toFull]);
$stmt->bind_param($locTypes . 'ss', ...$params);
$stmt->execute();
$resultado = $stmt->get_result();

if (!$resultado) {
    http_response_code(500);
    echo json_encode(['error' => 'Query execution failed: ' . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

$series = [];

while ($row = $resultado->fetch_assoc()) {
    $lugar = $row['NombreLugar'];
    $fecha = $row['Fecha'];
    $temp  = (float)$row['PromedioTemp'];

    if (!isset($series[$lugar])) {
        $series[$lugar] = [];
    }
    $series[$lugar][] = ['x' => $fecha, 'y' => $temp];
}

$apex_series = [];
foreach ($series as $lugar => $data) {
    $apex_series[] = ['name' => $lugar, 'data' => $data];
}

echo json_encode([
    'series'      => $apex_series,
    'granularity' => $granularity,
    'from'        => $from,
    'to'          => $to
]);

$stmt->close();
$conn->close();
