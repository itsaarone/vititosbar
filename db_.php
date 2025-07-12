<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vititos_db"; 

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar caracteres
$conn->set_charset("utf8");
?>