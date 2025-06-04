<?php
require_once __DIR__ . '/includes/db.php';
session_start();

// Solo estudiantes pueden reservar
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'estudiante') {
    echo "<p>Acceso no autorizado.</p>";
    exit;
}

$estudiante_id = $_SESSION['usuario_id'];

// Verificar si tutor_id está presente
if (!isset($_GET['tutor_id']) || !is_numeric($_GET['tutor_id'])) {
    echo "<p>ID de tutor no válido.</p>";
    exit;
}

$tutor_id = (int) $_GET['tutor_id'];

// Obtener nombre del tutor
$stmt = $conn->prepare("SELECT u.nombre FROM tutores t JOIN usuarios u ON t.usuario_id = u.id WHERE t.id = ?");
$stmt->execute([$tutor_id]);
$tutor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tutor) {
    echo "<p>Tutor no encontrado.</p>";
    exit;
}

// Obtener disponibilidad del tutor
$stmt = $conn->prepare("SELECT * FROM disponibilidad WHERE tutor_id = ?");
$stmt->execute([$tutor_id]);
$disponibilidad = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha'], $_POST['hora'])) {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $duracion = 60; // minutos fijos por ahora
    $fecha_completa = "$fecha $hora";

    try {
        $stmt = $conn->prepare("INSERT INTO tutorias (estudiante_id, tutor_id, fecha, duracion, estado) VALUES (?, ?, ?, ?, 'pendiente')");
        $stmt->execute([$estudiante_id, $tutor_id, $fecha_completa, $duracion]);
        echo "<p style='color:green;'>¡Tutoría reservada correctamente!</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error al reservar: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<h2>Reservar Tutoría con <?= htmlspecialchars($tutor['nombre']) ?></h2>

<?php if (count($disponibilidad) === 0): ?>
    <p>Este tutor aún no ha configurado su disponibilidad.</p>
<?php else: ?>
    <form method="POST">
        <label>Selecciona una fecha:</label><br>
        <input type="date" name="fecha" required min="<?= date('Y-m-d') ?>"><br><br>

        <label>Selecciona una hora:</label><br>
        <select name="hora" required>
            <?php
            // Mostrar opciones agrupadas por día
            foreach ($disponibilidad as $slot) {
                echo "<optgroup label='" . htmlspecialchars($slot['dia_semana']) . "'>";
                $inicio = strtotime($slot['hora_inicio']);
                $fin = strtotime($slot['hora_fin']);
                while ($inicio + 3600 <= $fin) { // intervalos de 1 hora
                    echo "<option value='" . date('H:i', $inicio) . "'>" . date('H:i', $inicio) . "</option>";
                    $inicio += 3600;
                }
                echo "</optgroup>";
            }
            ?>
        </select><br><br>

        <button type="submit">Reservar</button>
    </form>
<?php endif; ?>