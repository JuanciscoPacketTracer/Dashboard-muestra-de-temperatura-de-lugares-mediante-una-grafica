<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth.php';
roomtemperature_require_login_json();

function respond(bool $success, string $title, string $message, string $details = '', int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'title' => $title,
        'message' => $message,
        'details' => $details,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = roomtemperature_pdo();
    $lugar = trim($_POST['lugar'] ?? '');

    if ($lugar === '') {
        respond(false, 'Faltan datos', 'Completa el nombre del lugar antes de enviar.', '', 422);
    }

    $stmt = $pdo->prepare('SELECT NombreLugar FROM lugares WHERE NombreLugar = ? LIMIT 1');
    $stmt->execute([$lugar]);
    $existingLugar = $stmt->fetch();

    if ($existingLugar) {
        respond(false, 'Lugar duplicado', 'Ese lugar ya está registrado.', 'Lugar existente: ' . $existingLugar['NombreLugar'], 409);
    }

    $insert = $pdo->prepare('INSERT INTO lugares VALUES (Null, :NombreLugar)');
    $insert->bindParam(':NombreLugar', $lugar);

    if ($insert->execute()) {
        respond(
            true,
            'Registro creado',
            'El lugar se guardó correctamente.',
            'Nombre del lugar: ' . $lugar,
            201
        );
    }

    respond(false, 'Error al crear el registro', 'No fue posible guardar los datos.', '', 500);
} catch (PDOException $e) {
    respond(false, 'Error de conexión o consulta', 'No se pudo completar el registro.', $e->getMessage(), 500);
}
