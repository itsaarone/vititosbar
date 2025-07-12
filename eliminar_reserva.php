<?php
session_start();

// Verificar acceso: usuario logueado y colaborador con "vititos" en su nombre
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'colaborador' || stripos($_SESSION['fullname'], 'vititos') === false) {
    header("Location: reservas.html");
    exit;
}

// Conexión a la base de datos
$host = "localhost";
$dbname = "vititosdb";
$user = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Obtener el id de la reserva a eliminar
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: panel_colaborador.php");
    exit;
}

// Eliminar la reserva
$stmt = $pdo->prepare("DELETE FROM reservas WHERE id = ?");
$stmt->execute([$id]);

// Redirigir al panel de colaborador
header("Location: panel_colaborador.php");
exit;
?>
