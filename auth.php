<?php
function roomtemperature_start_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}
function roomtemperature_pdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $dsn = 'mysql:host=localhost;dbname=roomtemperaturedb;charset=utf8mb4';
    $username = 'user23060120';
    $password = 'rootroot23060120';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
    return $pdo;
}
function roomtemperature_authenticate_user(string $email, string $password, ?string &$dbError = null): ?array
{
    $dbError = null;
    $email = trim($email);
    $password = trim($password);
    if ($email === '' || $password === '') {
        return null;
    }
    try {
        if (class_exists('PDO')) {
            $pdoDrivers = PDO::getAvailableDrivers();
            if (is_array($pdoDrivers) && in_array('mysql', $pdoDrivers, true)) {
                $pdo = roomtemperature_pdo();
                $stmt = $pdo->prepare(
                    'SELECT IdUsuario, NombreUsuario, emailusuario
                     FROM usuarios
                     WHERE emailusuario = ? AND passwordusuario = ?
                     LIMIT 1'
                );
                $stmt->execute([$email, $password]);
                $user = $stmt->fetch();

                return $user ?: null;
            }
        }

        require __DIR__ . '/config/database.php';

        if (!isset($conn) || !($conn instanceof mysqli)) {
            $dbError = 'No se pudo inicializar la conexion a MySQL.';
            return null;
        }

        $stmt = $conn->prepare(
            'SELECT IdUsuario, NombreUsuario, emailusuario
             FROM usuarios
             WHERE emailusuario = ? AND passwordusuario = ?
             LIMIT 1'
        );

        if (!$stmt) {
            $dbError = $conn->error;
            $conn->close();
            return null;
        }

        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();

        $user = null;
        if (method_exists($stmt, 'get_result')) {
            $res = $stmt->get_result();
            if ($res) {
                $user = $res->fetch_assoc() ?: null;
            }
        } else {
            $stmt->bind_result($idUsuario, $nombreUsuario, $emailUsuario);
            if ($stmt->fetch()) {
                $user = [
                    'IdUsuario' => $idUsuario,
                    'NombreUsuario' => $nombreUsuario,
                    'emailusuario' => $emailUsuario,
                ];
            }
        }

        $stmt->close();
        $conn->close();

        return $user;
    } catch (Throwable $e) {
        $dbError = $e->getMessage();
        return null;
    }
}
function roomtemperature_session_user(): ?array
{
    roomtemperature_start_session();

    if (!isset($_SESSION['roomtemperature_user']) || !is_array($_SESSION['roomtemperature_user'])) {
        return null;
    }

    return $_SESSION['roomtemperature_user'];
}

function roomtemperature_login_user(array $user): void
{
    roomtemperature_start_session();
    session_regenerate_id(true);
    $_SESSION['roomtemperature_user'] = [
        'IdUsuario' => $user['IdUsuario'] ?? null,
        'NombreUsuario' => $user['NombreUsuario'] ?? '',
        'emailusuario' => $user['emailusuario'] ?? '',
    ];
}

function roomtemperature_clear_session(): void
{
    roomtemperature_start_session();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 3600,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}
function roomtemperature_require_login(): void
{
    if (roomtemperature_session_user() === null) {
        header('Location: login.php');
        exit;
    }
}
function roomtemperature_require_login_json(): void
{
    if (roomtemperature_session_user() === null) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
