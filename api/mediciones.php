<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if ($data === null || !isset($data["temperatura"]) || !isset($data["lugar_id"])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request: temperatura and lugar_id required"]);
    $conn->close();
    exit;
}

$temp = (float)$data["temperatura"];
$lugarId = (int)$data["lugar_id"];

if ($temp < -50 || $temp > 150) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid temperature value"]);
    $conn->close();
    exit;
}
$sql = "INSERT INTO Temperaturas 
(IdTemperatura, FechaTemperatura, ValorTemperatura, Lugares_IdLugar) 
VALUES (NULL, NOW(), ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Query preparation failed"]);
    $conn->close();
    exit;
}
$stmt->bind_param("di", $temp, $lugarId);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        "status" => "ok",
        "id" => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Insert failed"]);
}
$stmt->close();
$conn->close();
?>