<?php
require_once(__DIR__ . '/../../includes/db.php');
if (!isset($_SESSION['usuario_id'], $_SESSION['rol'])) {
    echo "<p>Error: No has iniciado sesión correctamente.</p>";
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

echo "<h2 class='dashboard-title'>Panel de Control</h2>";

// ----------------------
// GESTIONAR DISPONIBILIDAD (solo tutores)
// ----------------------
if ($rol === 'tutor') {
    // Agregar disponibilidad
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disponibilidad_submit'])) {
        $dia_semana = $_POST['dia_semana'] ?? '';
        $hora_inicio = $_POST['hora_inicio'] ?? '';
        $hora_fin = $_POST['hora_fin'] ?? '';

        if ($dia_semana && $hora_inicio && $hora_fin) {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO disponibilidad (tutor_id, dia_semana, hora_inicio, hora_fin)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$usuario_id, $dia_semana, $hora_inicio, $hora_fin]);

                // Redirigir para evitar reenvío automático
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } catch (PDOException $e) {
                echo "<p style='color: red;'>Error al agregar disponibilidad: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            echo "<p style='color: red;'>Por favor, completa todos los campos.</p>";
        }
    }

    // Eliminar disponibilidad
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_disponibilidad'])) {
        $id_disponibilidad = (int)$_POST['id_disponibilidad'];
        try {
            $stmt = $conn->prepare("DELETE FROM disponibilidad WHERE id = ? AND tutor_id = ?");
            $stmt->execute([$id_disponibilidad, $usuario_id]);

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Error al eliminar disponibilidad: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    // Mostrar formulario
    echo "<h3 class='form-title'>Gestionar Disponibilidad</h3>";
    echo '<form method="POST" class="form-disponibilidad" style="margin-bottom: 20px;">
            <label for="dia_semana">Día:</label>
            <select name="dia_semana" id="dia_semana" required class="input-select">
                <option value="">Selecciona un día</option>
                <option value="Lunes">Lunes</option>
                <option value="Martes">Martes</option>
                <option value="Miércoles">Miércoles</option>
                <option value="Jueves">Jueves</option>
                <option value="Viernes">Viernes</option>
                <option value="Sábado">Sábado</option>
                <option value="Domingo">Domingo</option>
            </select>

            <label for="hora_inicio">Hora inicio:</label>
            <input type="time" name="hora_inicio" id="hora_inicio" required class="input-time">

            <label for="hora_fin">Hora fin:</label>
            <input type="time" name="hora_fin" id="hora_fin" required class="input-time">

            <button type="submit" name="disponibilidad_submit" class="btn btn-primary">Guardar Disponibilidad</button>
        </form>';

    // Mostrar disponibilidades existentes
    try {
        $stmt = $conn->prepare("SELECT * FROM disponibilidad WHERE tutor_id = ? ORDER BY FIELD(dia_semana, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'), hora_inicio");
        $stmt->execute([$usuario_id]);
        $disponibilidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($disponibilidades) {
            echo "<h4>Disponibilidad actual</h4>";
            echo "<table class='tabla-disponibilidad' border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>Día</th><th>Hora Inicio</th><th>Hora Fin</th><th>Acción</th></tr>";
            foreach ($disponibilidades as $d) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($d['dia_semana']) . "</td>";
                echo "<td>" . htmlspecialchars(substr($d['hora_inicio'], 0, 5)) . "</td>";
                echo "<td>" . htmlspecialchars(substr($d['hora_fin'], 0, 5)) . "</td>";
                echo "<td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='id_disponibilidad' value='" . $d['id'] . "'>
                            <button type='submit' name='eliminar_disponibilidad' class='btn btn-danger' onclick='return confirm(\"¿Eliminar esta disponibilidad?\")'>Eliminar</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No has añadido disponibilidad aún.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error al cargar disponibilidad: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// ----------------------
// SESIONES
// ----------------------
try {
    if ($rol === 'estudiante') {
        $stmt = $conn->prepare("
            SELECT s.id, u.nombre AS tutor, s.fecha, s.duracion_minutos, s.estado
            FROM sesiones s
            JOIN usuarios u ON s.tutor_id = u.id
            WHERE s.estudiante_id = ?
            ORDER BY s.fecha DESC
        ");
    } else {
        $stmt = $conn->prepare("
            SELECT s.id, u.nombre AS estudiante, s.fecha, s.duracion_minutos, s.estado
            FROM sesiones s
            JOIN usuarios u ON s.estudiante_id = u.id
            WHERE s.tutor_id = ?
            ORDER BY s.fecha DESC
        ");
    }
    $stmt->execute([$usuario_id]);
    $sesiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3 class='sesiones-title'>Sesiones " . ($rol === 'tutor' ? "agendadas con estudiantes" : "reservadas con tutores") . "</h3>";

    if (empty($sesiones)) {
        echo "<p>No tienes sesiones programadas.</p>";
    } else {
        echo "<table class='tabla-sesiones' border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr>
                <th>Fecha</th>
                <th>Duración</th>
                <th>" . ($rol === 'tutor' ? "Estudiante" : "Tutor") . "</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>";

        foreach ($sesiones as $s) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars(date('d/m/Y H:i', strtotime($s['fecha']))) . "</td>";
            echo "<td>" . htmlspecialchars($s['duracion_minutos']) . " min</td>";
            echo "<td>" . htmlspecialchars($rol === 'tutor' ? $s['estudiante'] : $s['tutor']) . "</td>";
            echo "<td>" . htmlspecialchars($s['estado']) . "</td>";
            echo "<td>";

            if ($s['estado'] === 'pendiente' && $rol === 'tutor') {
                echo "<form method='POST' action='acciones_sesion.php' style='display:inline'>
                        <input type='hidden' name='accion' value='confirmar'>
                        <input type='hidden' name='sesion_id' value='" . $s['id'] . "'>
                        <button type='submit' class='btn btn-confirmar'>Confirmar</button>
                      </form>";
            }

            if ($s['estado'] !== 'cancelada') {
                echo "<form method='POST' action='acciones_sesion.php' style='display:inline; margin-left:5px'>
                        <input type='hidden' name='accion' value='cancelar'>
                        <input type='hidden' name='sesion_id' value='" . $s['id'] . "'>
                        <button type='submit' class='btn btn-cancelar'>Cancelar</button>
                      </form>";
            }

            echo "</td></tr>";
        }
        echo "</table>";
    }
} catch (PDOException $e) {
    echo "<p>Error al obtener las sesiones: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
