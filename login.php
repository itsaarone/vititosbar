<?php
session_start();

// Datos de conexión (ajusta si tu usuario o contraseña es diferente)
$host = "localhost";
$db = "vititos_db";
$user = "root";
$pass = "";

// Crear conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recoger datos del formulario
$email = $_POST['email'];
$password = $_POST['password'];

// Preparar y ejecutar consulta para evitar SQL Injection
$stmt = $conn->prepare("SELECT id, nombre, password, tipo FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verificar contraseña usando password_verify
    if (password_verify($password, $user['password'])) {
        // Guardar info en sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_type'] = $user['tipo'];

        $_SESSION['success'] = "¡Bienvenido, " . htmlspecialchars($user['nombre']) . "!";

        // Redireccionar según tipo
        if ($user['tipo'] === 'colaborador') {
            header("Location: panel_colaborador.php");
        } else {
            header("Location: panel_cliente.php");
        }
        exit();
    } else {
        $_SESSION['error'] = "Contraseña incorrecta.";
        header("Location: reservas.html");
        exit();
    }
} else {
    $_SESSION['error'] = "Usuario no encontrado.";
    header("Location: reservas.html");
    exit();
}

$stmt->close();
$conn->close();
?>
