<?php
session_start();
include 'includes/header.php';
require_once 'includes/db.php';

// 1. Validar sesión
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'estudiante') {
    header('Location: auth.php');
    exit;
}

$estudiante_id = $_SESSION['usuario_id'];

// 2. Recoger y validar datos
$tutor_id = $_POST['tutor_id'] ?? null;
$dia_semana = $_POST['dia_semana'] ?? null;
$hora_inicio = $_POST['hora_inicio'] ?? null;
$duracion = $_POST['duracion'] ?? null;

if (!$tutor_id || !$dia_semana || !$hora_inicio || !$duracion) {
    die('Datos incompletos.');
}

// 3. Calcular la fecha y hora exacta
function obtenerFechaProxima($diaSemanaDeseado) {
    $dias = [
        'Lunes' => 1, 'Martes' => 2, 'Miércoles' => 3,
        'Jueves' => 4, 'Viernes' => 5, 'Sábado' => 6, 'Domingo' => 7
    ];
    $hoy = new DateTime();
    $hoy->setTime(0, 0);
    $numeroDiaActual = (int) $hoy->format('N');
    $numeroDiaDeseado = $dias[$diaSemanaDeseado];

    $diasHastaProximo = ($numeroDiaDeseado - $numeroDiaActual + 7) % 7;
    if ($diasHastaProximo === 0) $diasHastaProximo = 7;

    $hoy->modify("+$diasHastaProximo days");
    return $hoy;
}

$fechaInicio = obtenerFechaProxima($dia_semana);
list($hora, $minuto) = explode(':', $hora_inicio);
$fechaInicio->setTime((int)$hora, (int)$minuto);

$fechaFin = clone $fechaInicio;
$fechaFin->modify("+{$duracion} minutes");

// 4. Verificar conflicto con otras sesiones ya agendadas
try {
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM sesiones
        WHERE tutor_id = ? AND estado IN ('pendiente', 'confirmada')
          AND (
            (fecha BETWEEN ? AND ?) OR
            (DATE_ADD(fecha, INTERVAL duracion_minutos MINUTE) BETWEEN ? AND ?)
          )
    ");
    $stmt->execute([
        $tutor_id,
        $fechaInicio->format('Y-m-d H:i:s'),
        $fechaFin->format('Y-m-d H:i:s'),
        $fechaInicio->format('Y-m-d H:i:s'),
        $fechaFin->format('Y-m-d H:i:s')
    ]);

    $conflictos = $stmt->fetchColumn();
    if ($conflictos > 0) {
        echo "<p>Este horario ya está reservado. Por favor elige otro.</p>";
        echo '<p><a href="javascript:history.back()">Volver atrás</a></p>';
        exit;
    }

    // 5. Insertar la nueva sesión
    $stmt = $conn->prepare("
        INSERT INTO sesiones (estudiante_id, tutor_id, fecha, duracion_minutos, estado)
        VALUES (?, ?, ?, ?, 'pendiente')
    ");
    $stmt->execute([
        $estudiante_id,
        $tutor_id,
        $fechaInicio->format('Y-m-d H:i:s'),
        $duracion
    ]);

    echo "<p>¡Reserva realizada con éxito para " . $fechaInicio->format('d/m/Y H:i') . "!</p>";
    echo '<p><a href="perfil.php">Ir a mi perfil</a></p>';

    // Mostrar próximas sesiones del estudiante
    echo "<h3>Tus próximas sesiones</h3>";
    $stmt = $conn->prepare("
        SELECT s.fecha, s.duracion_minutos, u.nombre AS tutor
        FROM sesiones s
        JOIN usuarios u ON s.tutor_id = u.id
        WHERE s.estudiante_id = ? AND s.estado IN ('pendiente', 'confirmada')
        ORDER BY s.fecha ASC
        LIMIT 5
    ");
    $stmt->execute([$estudiante_id]);
    $sesiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($sesiones) === 0) {
        echo "<p>No tienes sesiones programadas aún.</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Fecha</th><th>Duración (min)</th><th>Tutor</th></tr>";
        foreach ($sesiones as $s) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars(date('d/m/Y H:i', strtotime($s['fecha']))) . "</td>";
            echo "<td>" . htmlspecialchars($s['duracion_minutos']) . "</td>";
            echo "<td>" . htmlspecialchars($s['tutor']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

} catch (PDOException $e) {
    echo "<p>Error al procesar la reserva: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
