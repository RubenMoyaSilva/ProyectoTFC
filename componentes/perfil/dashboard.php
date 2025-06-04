<?php
require_once(__DIR__ . '/../../includes/db.php');

// Validar sesión
if (!isset($_SESSION['usuario_id'], $_SESSION['rol'])) {
    echo "<p>Error: No has iniciado sesión correctamente.</p>";
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

echo "<h2>Panel de Control</h2>";

// Procesar formulario si es tutor y viene POST
if ($rol === 'tutor' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar_perfil') {
    $biografia = trim($_POST['biografia'] ?? '');
    $materias = trim($_POST['materias'] ?? '');
    
    // Validar datos mínimos
    if ($biografia === '' || $materias === '') {
        echo "<p style='color:red;'>La biografía y materias no pueden estar vacías.</p>";
    } else {
        try {
            // Actualizar biografía y materias en tabla tutores
            $stmt = $conn->prepare("UPDATE tutores SET biografia = ?, materias = ? WHERE usuario_id = ?");
            $stmt->execute([$biografia, $materias, $usuario_id]);
            echo "<p style='color:green;'>Perfil actualizado correctamente.</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error al actualizar perfil: " . htmlspecialchars($e->getMessage()) . "</p>";
        }

        // Procesar disponibilidad (días y horarios)
        // El formato esperado es arrays: dia_semana[], hora_inicio[], hora_fin[]
        // Primero, eliminar las disponibilidades previas para este tutor
        try {
            // Obtener tutor_id
            $stmt = $conn->prepare("SELECT id FROM tutores WHERE usuario_id = ?");
            $stmt->execute([$usuario_id]);
            $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tutor) {
                $tutor_id = $tutor['id'];

                // Eliminar disponibilidades previas
                $conn->prepare("DELETE FROM disponibilidad WHERE tutor_id = ?")->execute([$tutor_id]);

                // Insertar nuevas disponibilidades
                $dias = $_POST['dia_semana'] ?? [];
                $horas_inicio = $_POST['hora_inicio'] ?? [];
                $horas_fin = $_POST['hora_fin'] ?? [];

                $insertStmt = $conn->prepare("INSERT INTO disponibilidad (tutor_id, dia_semana, hora_inicio, hora_fin) VALUES (?, ?, ?, ?)");

                for ($i = 0; $i < count($dias); $i++) {
                    $dia = $dias[$i];
                    $hinicio = $horas_inicio[$i];
                    $hfin = $horas_fin[$i];

                    // Validar datos mínimos antes de insertar
                    if ($dia && $hinicio && $hfin) {
                        $insertStmt->execute([$tutor_id, $dia, $hinicio, $hfin]);
                    }
                }
                echo "<p style='color:green;'>Disponibilidad actualizada correctamente.</p>";
            } else {
                echo "<p style='color:red;'>No se encontró el tutor para actualizar disponibilidad.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error al actualizar disponibilidad: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

// Mostrar tutorías agendadas (igual que antes)
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

        // Mostrar formulario para actualizar biografía, materias y calendario

        // Obtener datos actuales del tutor
        $stmt = $conn->prepare("SELECT biografia, materias, id FROM tutores WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $tutorData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener disponibilidad actual
        $stmt = $conn->prepare("SELECT dia_semana, hora_inicio, hora_fin FROM disponibilidad WHERE tutor_id = ?");
        $stmt->execute([$tutorData['id']]);
        $disponibilidad = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Función para generar options de días
        function opcionesDias($seleccionado = '') {
            $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
            $html = '';
            foreach ($dias as $dia) {
                $sel = ($dia === $seleccionado) ? 'selected' : '';
                $html .= "<option value=\"$dia\" $sel>$dia</option>";
            }
            return $html;
        }

        // Formulario HTML
        echo '<h3>Editar Perfil</h3>';
        echo '<form method="POST" action="">';
        echo '<input type="hidden" name="accion" value="actualizar_perfil">';
        echo '<label>Biografía:</label><br>';
        echo '<textarea name="biografia" rows="4" cols="50">' . htmlspecialchars($tutorData['biografia'] ?? '') . '</textarea><br><br>';

        echo '<label>Materias (separadas por comas):</label><br>';
        echo '<textarea name="materias" rows="2" cols="50">' . htmlspecialchars($tutorData['materias'] ?? '') . '</textarea><br><br>';

        echo '<label>Disponibilidad:</label><br>';

        // Contenedor para las filas de disponibilidad
        echo '<div id="contenedor-disponibilidad">';

        if (count($disponibilidad) > 0) {
            foreach ($disponibilidad as $index => $d) {
                echo '<div class="fila-disponibilidad">';
                echo '<select name="dia_semana[]">' . opcionesDias($d['dia_semana']) . '</select>';
                echo '<input type="time" name="hora_inicio[]" value="' . htmlspecialchars(substr($d['hora_inicio'],0,5)) . '" required>';
                echo '<input type="time" name="hora_fin[]" value="' . htmlspecialchars(substr($d['hora_fin'],0,5)) . '" required>';
                echo '<button type="button" onclick="this.parentElement.remove()">Eliminar</button>';
                echo '</div>';
            }
        } else {
            // Mostrar una fila vacía por defecto
            echo '<div class="fila-disponibilidad">';
            echo '<select name="dia_semana[]">' . opcionesDias() . '</select>';
            echo '<input type="time" name="hora_inicio[]" required>';
            echo '<input type="time" name="hora_fin[]" required>';
            echo '<button type="button" onclick="this.parentElement.remove()">Eliminar</button>';
            echo '</div>';
        }
        echo '</div><br>';

        echo '<button type="button" onclick="agregarFila()">Agregar horario</button><br><br>';
        echo '<button type="submit">Guardar Cambios</button>';
        echo '</form>';

        // Script para agregar filas dinámicas de disponibilidad
        echo <<<HTML
<script>
function agregarFila() {
    const contenedor = document.getElementById('contenedor-disponibilidad');
    const fila = document.createElement('div');
    fila.className = 'fila-disponibilidad';
    fila.innerHTML = `
        <select name="dia_semana[]">
            <option value="Lunes">Lunes</option>
            <option value="Martes">Martes</option>
            <option value="Miércoles">Miércoles</option>
            <option value="Jueves">Jueves</option>
            <option value="Viernes">Viernes</option>
            <option value="Sábado">Sábado</option>
            <option value="Domingo">Domingo</option>
        </select>
        <input type="time" name="hora_inicio[]" required>
        <input type="time" name="hora_fin[]" required>
        <button type="button" onclick="this.parentElement.remove()">Eliminar</button>
    `;
    contenedor.appendChild(fila);
}
</script>
HTML;
    } else {
        echo "<p>Rol de usuario no reconocido.</p>";
    }
} catch (PDOException $e) {
    echo "<p>Error en la consulta a la base de datos: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>