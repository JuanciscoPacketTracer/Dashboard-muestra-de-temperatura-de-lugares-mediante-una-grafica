<?php
require_once __DIR__ . '/../config/database.php';

$sql_count = "SELECT COUNT(*) as Total FROM Temperaturas";
$res_count = $conn->query($sql_count);
$total_registros = '0';
if ($res_count) {
    $rowCount = $res_count->fetch_assoc();
    $total_registros = $rowCount ? number_format($rowCount['Total']) : '0';
}

$sql_avg = "SELECT ROUND(AVG(ValorTemperatura), 1) as PromedioTotal FROM Temperaturas";
$res_avg = $conn->query($sql_avg);
$promedio_total = '--';
if ($res_avg) {
    $rowAvg = $res_avg->fetch_assoc();
    $promedio_total = $rowAvg ? $rowAvg['PromedioTotal'] : '--';
}

$sql_max = "SELECT MAX(ValorTemperatura) as MaxTemp FROM Temperaturas";
$res_max = $conn->query($sql_max);
$max_temp = '--';
if ($res_max) {
    $rowMax = $res_max->fetch_assoc();
    $max_temp = $rowMax ? $rowMax['MaxTemp'] : '--';
}
