<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../auth.php';

roomtemperature_start_session();

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    $data = $_POST;
}

$user = roomtemperature_session_user();
if ($user === null) {
    $email = trim($data['email'] ?? '');
    $password = (string)($data['password'] ?? '');

    if ($email === '' || $password === '') {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $user = roomtemperature_authenticate_user($email, $password);
    if ($user === null) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid email or password'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    roomtemperature_login_user($user);
}

require_once __DIR__ . '/../config/database.php';

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

if ($data === null || !isset($data["temperatura"]) || !isset($data["lugar_id"])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request: temperatura and lugar_id required"]);
    $conn->close();
    exit;
}

$temp = (float)$data["temperatura"];
$lugarId = (int)$data["lugar_id"];

$stmtLugar = $conn->prepare("SELECT NombreLugar FROM Lugares WHERE IdLugar = ? LIMIT 1");
if (!$stmtLugar) {
    http_response_code(500);
    echo json_encode(["error" => "Location validation failed"]);
    $conn->close();
    exit;
}

$stmtLugar->bind_param("i", $lugarId);
$stmtLugar->execute();
if (method_exists($stmtLugar, 'get_result')) {
    $resLugar = $stmtLugar->get_result();

    if (!$resLugar || $resLugar->num_rows === 0) {
        http_response_code(404);
        echo json_encode(["error" => "El lugar seleccionado no existe"]);
        $stmtLugar->close();
        $conn->close();
        exit;
    }
} else {
    $stmtLugar->bind_result($nombreLugar);
    if (!$stmtLugar->fetch()) {
        http_response_code(404);
        echo json_encode(["error" => "El lugar seleccionado no existe"]);
        $stmtLugar->close();
        $conn->close();
        exit;
    }
}

$stmtLugar->close();

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