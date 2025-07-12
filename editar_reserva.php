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

// Obtener el id de la reserva
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: panel_colaborador.php");
    exit;
}

// Si se envía el formulario, actualizar datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['name'] ?? '';
    $correo = $_POST['email'] ?? '';
    $telefono = $_POST['phone'] ?? '';
    $fecha = $_POST['date'] ?? '';
    $hora = $_POST['time'] ?? '';
    $personas = $_POST['people'] ?? '';
    $mensaje = $_POST['message'] ?? '';

    $stmt = $pdo->prepare("UPDATE reservas SET nombre = ?, correo = ?, telefono = ?, fecha = ?, hora = ?, personas = ?, mensaje = ? WHERE id = ?");
    $stmt->execute([$nombre, $correo, $telefono, $fecha, $hora, $personas, $mensaje, $id]);

    header("Location: panel_colaborador.php");
    exit;
}

// Obtener los datos actuales de la reserva
$stmt = $pdo->prepare("SELECT * FROM reservas WHERE id = ?");
$stmt->execute([$id]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reserva) {
    header("Location: panel_colaborador.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Reserva | Vititos</title>
  <link rel="stylesheet" href="estilos.css" />
  <style>
    .edit-form {
      max-width: 600px;
      margin: 2rem auto;
      background: rgba(0,0,0,0.6);
      padding: 1.5rem;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.5);
      color: var(--text-light);
    }
    .edit-form label {
      display: block;
      margin-top: 0.8rem;
      font-weight: bold;
      color: var(--accent);
    }
    .edit-form input,
    .edit-form select,
    .edit-form textarea {
      width: 100%;
      padding: 0.4rem;
      border-radius: 5px;
      border: none;
      margin-top: 0.2rem;
    }
    .edit-form button {
      margin-top: 1.2rem;
      padding: 0.6rem 1.2rem;
      background: var(--accent);
      border: none;
      border-radius: 6px;
      font-weight: bold;
      color: #111;
      cursor: pointer;
      transition: background 0.3s;
    }
    .edit-form button:hover {
      background: #ffbb33;
    }
    h1 {
      text-align: center;
      margin-top: 2rem;
      color: var(--accent);
      text-shadow: 2px 2px 6px rgba(0,0,0,0.7);
    }
  </style>
</head>
<body>
  <header>
    <a href="main.html"><img src="vititos.png" alt="Logo Vititos"></a>
  </header>

  <nav>
    <a href="entradas.html">Entradas</a>
    <a href="cervezas.html">Cervezas</a>
    <a href="cocteles.html">Cócteles</a>
    <a href="shots.html">Shots</a>
    <a href="bebidas.html">Bebidas</a>
    <a href="reservas.html">Reservas</a>
  </nav>

  <h1>Editar Reserva</h1>

  <form class="edit-form" action="editar_reserva.php?id=<?php echo $id; ?>" method="POST">
    <label for="name">Nombre completo</label>
    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($reserva['nombre']); ?>" />

    <label for="email">Correo electrónico</label>
    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($reserva['correo']); ?>" />

    <label for="phone">Teléfono</label>
    <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($reserva['telefono']); ?>" />

    <label for="date">Fecha</label>
    <input type="date" id="date" name="date" required value="<?php echo htmlspecialchars($reserva['fecha']); ?>" />

    <label for="time">Hora</label>
    <input type="time" id="time" name="time" required value="<?php echo htmlspecialchars($reserva['hora']); ?>" />

    <label for="people">Número de personas</label>
    <select id="people" name="people" required>
      <?php
        $options = ['1', '2', '3', '4', '5+'];
        foreach ($options as $option) {
          $selected = ($reserva['personas'] === $option) ? 'selected' : '';
          echo "<option value=\"$option\" $selected>$option</option>";
        }
      ?>
    </select>

    <label for="message">Mensaje adicional</label>
    <textarea id="message" name="message" rows="3"><?php echo htmlspecialchars($reserva['mensaje']); ?></textarea>

    <button type="submit">Guardar Cambios</button>
  </form>

  <footer>
    2025 Emilio Polastre. Ingeniería en Informática. Contacto: epolastre1@gmail.com. Todos los derechos reservados.
  </footer>
</body>
</html>
