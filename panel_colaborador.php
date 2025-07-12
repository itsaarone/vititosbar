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

// Manejo de operaciones: agregar, editar, eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $id = $_POST['id'] ?? null;

        if ($action === 'delete' && $id) {
            $stmt = $pdo->prepare("DELETE FROM reservas WHERE id = ?");
            $stmt->execute([$id]);
        }

        if (($action === 'add' || $action === 'edit')) {
            $nombre = $_POST['name'] ?? '';
            $correo = $_POST['email'] ?? '';
            $telefono = $_POST['phone'] ?? '';
            $fecha = $_POST['date'] ?? '';
            $hora = $_POST['time'] ?? '';
            $personas = $_POST['people'] ?? '';
            $mensaje = $_POST['message'] ?? '';

            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO reservas (user_id, nombre, correo, telefono, fecha, hora, personas, mensaje) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                // Como colaborador, no hay user_id, dejamos null o 0
                $stmt->execute([0, $nombre, $correo, $telefono, $fecha, $hora, $personas, $mensaje]);
            }

            if ($action === 'edit' && $id) {
                $stmt = $pdo->prepare("UPDATE reservas SET nombre = ?, correo = ?, telefono = ?, fecha = ?, hora = ?, personas = ?, mensaje = ? WHERE id = ?");
                $stmt->execute([$nombre, $correo, $telefono, $fecha, $hora, $personas, $mensaje, $id]);
            }
        }
    }
}

// Obtener todas las reservas ordenadas por fecha y hora
$stmt = $pdo->query("SELECT * FROM reservas ORDER BY fecha ASC, hora ASC");
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel Colaborador | Vititos</title>
  <link rel="stylesheet" href="estilos.css" />
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2rem;
      background: rgba(0,0,0,0.6);
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.5);
    }
    th, td {
      padding: 0.8rem;
      border-bottom: 1px solid #333;
      color: var(--text-light);
      text-align: left;
    }
    th {
      background: var(--accent);
      color: #111;
      font-weight: bold;
    }
    tr:hover {
      background: rgba(230, 156, 15, 0.3);
    }
    form.inline {
      display: inline;
    }
    .btn {
      background: var(--accent);
      border: none;
      padding: 0.4rem 0.8rem;
      margin: 0 0.2rem;
      border-radius: 5px;
      color: #111;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s;
    }
    .btn:hover {
      background: #ffbb33;
    }
    .add-reserva {
      margin-bottom: 2rem;
      background: var(--overlay);
      padding: 1rem;
      border-radius: 10px;
      max-width: 600px;
    }
    .add-reserva label {
      display: block;
      margin: 0.5rem 0 0.2rem;
      color: var(--accent);
      font-weight: bold;
    }
    .add-reserva input,
    .add-reserva select,
    .add-reserva textarea {
      width: 100%;
      padding: 0.4rem;
      border-radius: 5px;
      border: none;
      margin-bottom: 0.8rem;
    }
    h1 {
      color: var(--accent);
      margin-bottom: 1rem;
      text-align: center;
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

  <h1>Panel de Reservas - Colaborador</h1>

  <!-- Formulario para agregar reserva -->
  <div class="add-reserva">
    <form action="panel_colaborador.php" method="POST">
      <input type="hidden" name="action" value="add" />
      <label for="name">Nombre completo</label>
      <input type="text" name="name" id="name" required />

      <label for="email">Correo electrónico</label>
      <input type="email" name="email" id="email" required />

      <label for="phone">Teléfono</label>
      <input type="tel" name="phone" id="phone" required />

      <label for="date">Fecha</label>
      <input type="date" name="date" id="date" required />

      <label for="time">Hora</label>
      <input type="time" name="time" id="time" required />

      <label for="people">Número de personas</label>
      <select name="people" id="people" required>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5+">5 o más</option>
      </select>

      <label for="message">Mensaje adicional</label>
      <textarea name="message" id="message" rows="2"></textarea>

      <button class="btn" type="submit">Agregar Reserva</button>
    </form>
  </div>

  <!-- Tabla de reservas -->
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Teléfono</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Personas</th>
        <th>Mensaje</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($reservas as $reserva): ?>
        <tr>
          <td><?php echo htmlspecialchars($reserva['id']); ?></td>
          <td><?php echo htmlspecialchars($reserva['nombre']); ?></td>
          <td><?php echo htmlspecialchars($reserva['correo']); ?></td>
          <td><?php echo htmlspecialchars($reserva['telefono']); ?></td>
          <td><?php echo htmlspecialchars($reserva['fecha']); ?></td>
          <td><?php echo htmlspecialchars($reserva['hora']); ?></td>
          <td><?php echo htmlspecialchars($reserva['personas']); ?></td>
          <td><?php echo htmlspecialchars($reserva['mensaje']); ?></td>
          <td>
            <!-- Editar -->
            <form class="inline" action="editar_reserva.php" method="GET" style="display:inline;">
              <input type="hidden" name="id" value="<?php echo $reserva['id']; ?>" />
              <button class="btn" type="submit">Editar</button>
            </form>
            <!-- Eliminar -->
            <form class="inline" action="panel_colaborador.php" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar esta reserva?');" style="display:inline;">
              <input type="hidden" name="action" value="delete" />
              <input type="hidden" name="id" value="<?php echo $reserva['id']; ?>" />
              <button class="btn" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <footer>
    2025 Emilio Polastre. Ingeniería en Informática. Contacto: epolastre1@gmail.com. Todos los derechos reservados.
  </footer>
</body>
</html>
