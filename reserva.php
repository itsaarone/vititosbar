<?php
session_start();

// Verificar que el usuario esté logueado como cliente para permitir reservar
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    $_SESSION['error'] = "Debe iniciar sesión como cliente para hacer una reserva.";
    header("Location: reservas.html");
    exit;
}

$host = "localhost";
$dbname = "vititosdb";
$user = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error en la conexión a la base de datos.";
    header("Location: reservas.html");
    exit;
}

$nombre = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['phone'] ?? '');
$fecha = $_POST['date'] ?? '';
$hora = $_POST['time'] ?? '';
$personas = $_POST['people'] ?? '';
$mensaje = trim($_POST['message'] ?? '');

if (!$nombre || !$email || !$telefono || !$fecha || !$hora || !$personas) {
    $_SESSION['error'] = "Por favor complete todos los campos obligatorios.";
    header("Location: reservas.html");
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO reservas (nombre_completo, email, telefono, fecha, hora, personas, mensaje) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $email, $telefono, $fecha, $hora, $personas, $mensaje]);

    $_SESSION['success'] = "Reserva realizada con éxito. Gracias, $nombre.";
    header("Location: reservas.html");
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al guardar la reserva.";
    header("Location: reservas.html");
    exit;
}

