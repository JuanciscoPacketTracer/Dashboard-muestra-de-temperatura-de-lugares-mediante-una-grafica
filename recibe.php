<?php
date_default_timezone_set('America/Tijuana');
$dsn = 'mysql:host=localhost;dbname=roomtemperaturedb';
$username = 'user23060120';
$password = 'rootroot123';
$email = $_POST["email"];
$pass = $_POST["password"];
$options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];
$ubi = $_POST["lugar_id"];
$tem = $_POST["temperatura"];
$fecha = date("Y-m-d H:i:s");
try {
	$pdo2 = new PDO($dsn, $username, $password, $options);
	$stmt2 = $pdo2->prepare("SELECT * FROM lugares WHERE idlugar = ?");
	$stmt2->execute([$ubi]);
	if ($row = $stmt2->fetch()) {
		try {
			$pdo1 = new PDO($dsn, $username, $password, $options);
			$stmt1 = $pdo1->prepare("SELECT * FROM usuarios WHERE emailusuario = ? AND passwordusuario = ?");
			$stmt1->execute([$email, $pass]);
			if ($row = $stmt1->fetch()) {
				$pdo = new PDO($dsn, $username, $password, $options);
				$sql = "INSERT INTO temperaturas VALUES (Null,  :fecha, :temperatura,  :ubicacion)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':ubicacion', $ubi);
				$stmt->bindParam(':temperatura', $tem);
				$stmt->bindParam(':fecha', $fecha);
				if ($stmt->execute()) {
					echo "Registro creado exitosamente";
				} else {
					echo "Error al crear el registro";
				}
			} else {
				echo "contraseña incorrecta o correo no registrado";
			}
		} catch (PDOException $e) {
			echo "Error de conexión o consulta: " . $e->getMessage();
		}
	} else {
		echo "El lugar seleccionado no existe";
	}
} catch (PDOException $e) {
	echo "Error de conexión o consulta: " . $e->getMessage();
}
?>
