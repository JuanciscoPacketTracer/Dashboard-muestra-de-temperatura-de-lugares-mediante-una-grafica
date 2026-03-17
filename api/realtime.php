<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$since = isset($_GET['since']) ? $_GET['since'] : null;

$series = [];
$latestDate = null;

if (!empty($LOCATION_IDS)) {
    $placeholders = implode(',', array_fill(0, count($LOCATION_IDS), '?'));
    $locTypes = str_repeat('i', count($LOCATION_IDS));

    if ($since) {
        $sql = "
            SELECT 
                l.NombreLugar, 
                t.FechaTemperatura AS Fecha, 
                t.ValorTemperatura AS Temp
            FROM Temperaturas t
            JOIN Lugares l ON t.Lugares_IdLugar = l.IdLugar 
            WHERE l.IdLugar IN ($placeholders) AND t.FechaTemperatura > ?
            ORDER BY Fecha ASC
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => 'Query preparation failed: ' . $conn->error]);
            $conn->close();
            exit;
        }

        $params = array_merge($LOCATION_IDS, [$since]);
        $stmt->bind_param($locTypes . 's', ...$params);
    } else {
        $sql = "
            SELECT NombreLugar, Fecha, Temp
            FROM (
                SELECT 
                    l.NombreLugar, 
                    t.FechaTemperatura AS Fecha, 
                    t.ValorTemperatura AS Temp,
                    ROW_NUMBER() OVER (PARTITION BY l.IdLugar ORDER BY t.FechaTemperatura DESC) AS rn
                FROM Temperaturas t
                JOIN Lugares l ON t.Lugares_IdLugar = l.IdLugar 
                WHERE l.IdLugar IN ($placeholders)
            ) sub
            WHERE rn <= 20
            ORDER BY Fecha ASC
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => 'Query preparation failed: ' . $conn->error]);
            $conn->close();
            exit;
        }

        $stmt->bind_param($locTypes, ...$LOCATION_IDS);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();

    if (!$resultado) {
        http_response_code(500);
        echo json_encode(['error' => 'Query execution failed: ' . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }

    while ($row = $resultado->fetch_assoc()) {
        $lugar = $row['NombreLugar'];
        $fecha = $row['Fecha'];
        $temp  = (float)$row['Temp'];

        if (!isset($series[$lugar])) {
            $series[$lugar] = [];
        }
        $series[$lugar][] = ['x' => $fecha, 'y' => $temp];

        if ($latestDate === null || $fecha > $latestDate) {
            $latestDate = $fecha;
        }
    }

    $stmt->close();
}

$apex_series = [];
foreach ($series as $lugar => $data) {
    $apex_series[] = ['name' => $lugar, 'data' => $data];
}

$sql_count = "SELECT COUNT(*) as Total FROM Temperaturas";
$res_count = $conn->query($sql_count);
$total_registros = 0;
if ($res_count) {
    $rowCount = $res_count->fetch_assoc();
    $total_registros = $rowCount ? $rowCount['Total'] : 0;
}

echo json_encode([
    'series' => $apex_series,
    'latest' => $latestDate,
    'totalRegistros' => number_format((float)$total_registros)
]);

$conn->close();
