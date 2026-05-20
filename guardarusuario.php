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
    $email = trim($_POST['email'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');

    if ($email === '' || $nombre === '') {
        respond(false, 'Faltan datos', 'Completa el email y el nombre antes de enviar.', '', 422);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(false, 'Email inválido', 'Escribe un correo electrónico válido.', '', 422);
    }

    $stmt = $pdo->prepare('SELECT NombreUsuario FROM usuarios WHERE emailusuario = ? LIMIT 1');
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        respond(false, 'Correo duplicado', 'Ese correo electrónico ya está registrado.', 'Usuario existente: ' . $existingUser['NombreUsuario'], 409);
    }

    $pass = bin2hex(random_bytes(32));
    $insert = $pdo->prepare('INSERT INTO usuarios VALUES (Null, :email, :pass, :nombre)');
    $insert->bindParam(':email', $email);
    $insert->bindParam(':pass', $pass);
    $insert->bindParam(':nombre', $nombre);

    if ($insert->execute()) {
        respond(
            true,
            'Registro creado',
            'El usuario se guardó correctamente.',
            'Correo registrado: ' . $email . "\n" . 'Contraseña asignada: ' . $pass,
            201
        );
    }

    respond(false, 'Error al crear el registro', 'No fue posible guardar los datos.', '', 500);
} catch (PDOException $e) {
    respond(false, 'Error de conexión o consulta', 'No se pudo completar el registro.', $e->getMessage(), 500);
}
