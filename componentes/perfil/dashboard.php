<?php
require_once(__DIR__ . '/../../includes/db.php');

// Validar sesión
if (!isset($_SESSION['usuario_id'], $_SESSION['rol'])) {
    echo "<p>Error: No has iniciado sesión correctamente.</p>";
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

// Mostrar mensaje dependiendo del rol
echo "<h2>Panel de Control</h2>";

try {
    if ($rol === 'estudiante') {
        // Consultar tutorías del estudiante
        $stmt = $conn->prepare("
            SELECT t.id, u.nombre AS tutor_nombre, t.fecha, t.duracion, t.estado
            FROM tutorias t
            JOIN usuarios u ON t.tutor_id = u.id
            WHERE t.estudiante_id = ?
            ORDER BY t.fecha DESC
            LIMIT 10
        ");
        $stmt->execute([$usuario_id]);
        $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($tutorias) === 0) {
            echo "<p>No tienes tutorías agendadas.</p>";
        } else {
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>ID</th><th>Tutor</th><th>Fecha</th><th>Duración (min)</th><th>Estado</th></tr>";
            foreach ($tutorias as $t) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($t['id']) . "</td>";
                echo "<td>" . htmlspecialchars($t['tutor_nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($t['fecha']) . "</td>";
                echo "<td>" . htmlspecialchars($t['duracion']) . "</td>";
                echo "<td>" . htmlspecialchars($t['estado']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

    } elseif ($rol === 'tutor') {
        // Consultar tutorías del tutor
        $stmt = $conn->prepare("
            SELECT t.id, u.nombre AS estudiante_nombre, t.fecha, t.duracion, t.estado
            FROM tutorias t
            JOIN usuarios u ON t.estudiante_id = u.id
            WHERE t.tutor_id = ?
            ORDER BY t.fecha DESC
            LIMIT 10
        ");
        $stmt->execute([$usuario_id]);
        $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($tutorias) === 0) {
            echo "<p>No tienes tutorías programadas.</p>";
        } else {
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>ID</th><th>Estudiante</th><th>Fecha</th><th>Duración (min)</th><th>Estado</th></tr>";
            foreach ($tutorias as $t) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($t['id']) . "</td>";
                echo "<td>" . htmlspecialchars($t['estudiante_nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($t['fecha']) . "</td>";
                echo "<td>" . htmlspecialchars($t['duracion']) . "</td>";
                echo "<td>" . htmlspecialchars($t['estado']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        // Aquí podrías añadir estadísticas o reseñas futuras

    } else {
        echo "<p>Rol de usuario no reconocido.</p>";
    }
} catch (PDOException $e) {
    echo "<p>Error en la consulta a la base de datos: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>