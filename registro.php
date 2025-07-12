<?php
session_start();

$host = "localhost";
$db = "vititos_db";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$nombre = $_POST['nombre'];
$email = $_POST['email'];
$password = $_POST['password'];
$tipo = 'cliente'; // por defecto

// Validar que el email no exista
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['error'] = "El correo ya está registrado.";
    header("Location: registro.html");
    exit();
}

$stmt->close();

// Encriptar contraseña
$pass_hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar nuevo usuario
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, tipo) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $email, $pass_hash, $tipo);

if ($stmt->execute()) {
    $_SESSION['success'] = "Registro exitoso. Ahora puedes iniciar sesión.";
    header("Location: reservas.html");
    exit();
} else {
    $_SESSION['error'] = "Error en el registro.";
    header("Location: registro.html");
    exit();
}

$stmt->close();
$conn->close();
?>
