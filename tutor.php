<?php
session_start();
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='error-msg'>ID de tutor inválido.</p>";
    include 'includes/footer.php';
    exit;
}

$tutor_id = (int)$_GET['id'];

try {
    $stmt = $conn->prepare("
        SELECT u.id, u.nombre, u.foto_perfil, t.biografia, t.materias, t.calificacion_promedio
        FROM usuarios u
        JOIN tutores t ON u.id = t.usuario_id
        WHERE u.id = ? AND u.rol = 'tutor'
        LIMIT 1
    ");
    $stmt->execute([$tutor_id]);
    $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tutor) {
        echo "<p class='error-msg'>Tutor no encontrado.</p>";
        include 'includes/footer.php';
        exit;
    }
} catch (PDOException $e) {
    echo "<p class='error-msg'>Error en la consulta: " . htmlspecialchars($e->getMessage()) . "</p>";
    include 'includes/footer.php';
    exit;
}

echo "<div class='tutor-profile'>";
echo "<div class='tutor-grid'>";
include 'componentes/tutor/biografia.php';
include 'componentes/tutor/materias.php';
include 'componentes/tutor/resenas.php';
echo "</div>";

echo "<section class='availability-section'>";
echo "<h3>Disponibilidad del tutor</h3>";
try {
    $stmt = $conn->prepare("
        SELECT dia_semana, hora_inicio, hora_fin
        FROM disponibilidad
        WHERE tutor_id = ?
        ORDER BY FIELD(dia_semana, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'), hora_inicio
    ");
    $stmt->execute([$tutor_id]);
    $disponibilidad = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($disponibilidad) === 0) {
        echo "<p class='info-msg'>Este tutor aún no ha registrado su disponibilidad.</p>";
    } else {
        echo "<div class='table-container'>";
        echo "<table class='styled-table'>";
        echo "<thead><tr><th>Día</th><th>Hora Inicio</th><th>Hora Fin</th><th>Acción</th></tr></thead><tbody>";
        foreach ($disponibilidad as $d) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($d['dia_semana']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($d['hora_inicio'], 0, 5)) . "</td>";
            echo "<td>" . htmlspecialchars(substr($d['hora_fin'], 0, 5)) . "</td>";
            echo "<td>";
            echo "<form method='GET' action='tutor.php' class='inline-form'>";
            echo "<input type='hidden' name='id' value='" . htmlspecialchars($tutor_id) . "'>";
            echo "<input type='hidden' name='reservar' value='1'>";
            echo "<input type='hidden' name='dia_semana' value='" . htmlspecialchars($d['dia_semana']) . "'>";
            echo "<input type='hidden' name='hora_inicio' value='" . htmlspecialchars(substr($d['hora_inicio'], 0, 5)) . "'>";
            echo "<button type='submit' class='btn btn-primary'>Reservar</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</div>";
    }
    echo "</section>";
} catch (PDOException $e) {
    echo "<p class='error-msg'>Error al obtener la disponibilidad: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<section class='reservations-section'>";
echo "<h3>Sesiones reservadas</h3>";
try {
    $stmt = $conn->prepare("
        SELECT s.fecha, s.duracion_minutos, u.nombre AS estudiante
        FROM sesiones s
        JOIN usuarios u ON s.estudiante_id = u.id
        WHERE s.tutor_id = ? AND s.estado IN ('pendiente', 'confirmada')
        ORDER BY s.fecha ASC
        LIMIT 10
    ");
    $stmt->execute([$tutor_id]);
    $sesiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($sesiones) === 0) {
        echo "<p class='info-msg'>No hay sesiones reservadas con este tutor aún.</p>";
    } else {
        echo "<div class='table-container'>";
        echo "<table class='styled-table'>";
        echo "<thead><tr><th>Fecha</th><th>Duración</th><th>Estudiante</th></tr></thead><tbody>";
        foreach ($sesiones as $s) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars(date('d/m/Y H:i', strtotime($s['fecha']))) . "</td>";
            echo "<td>" . htmlspecialchars($s['duracion_minutos']) . " min</td>";
            echo "<td>" . htmlspecialchars($s['estudiante']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</div>";
    }
    echo "</section>";
} catch (PDOException $e) {
    echo "<p class='error-msg'>Error al cargar sesiones: " . htmlspecialchars($e->getMessage()) . "</p>";
}

if (isset($_GET['reservar']) && $_GET['reservar'] == '1') {
    $dia_semana = $_GET['dia_semana'] ?? '';
    $hora_inicio = $_GET['hora_inicio'] ?? '';

    if (empty($dia_semana) || empty($hora_inicio)) {
        echo "<p class='error-msg'>Parámetros para reservar incompletos.</p>";
    } else {
        ?>
        <section class="reservation-form">
            <h3>Reservar sesión con <?= htmlspecialchars($tutor['nombre']) ?></h3>
            <form method="POST" action="procesar_reserva.php" class="form-box">
                <input type="hidden" name="tutor_id" value="<?= htmlspecialchars($tutor_id) ?>">
                <input type="hidden" name="dia_semana" value="<?= htmlspecialchars($dia_semana) ?>">
                <input type="hidden" name="hora_inicio" value="<?= htmlspecialchars($hora_inicio) ?>">

                <p><strong>Día:</strong> <?= htmlspecialchars($dia_semana) ?></p>
                <p><strong>Hora:</strong> <?= htmlspecialchars($hora_inicio) ?></p>

                <label for="duracion">Duración (minutos):</label>
                <input type="number" id="duracion" name="duracion" min="30" max="180" value="60" required>

                <label for="comentarios">Comentarios (opcional):</label>
                <textarea id="comentarios" name="comentarios" rows="1"></textarea>

                <button type="submit" class="btn btn-success">Confirmar Reserva</button>
            </form>
        </section>
        <?php
    }
}

if (isset($_SESSION['usuario_id']) && $_SESSION['rol'] === 'estudiante') {
    $estudiante_id = $_SESSION['usuario_id'];

    try {
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM sesiones
            WHERE tutor_id = ? AND estudiante_id = ? AND estado IN ('completada', 'confirmada')
        ");
        $stmt->execute([$tutor_id, $estudiante_id]);
        $tieneSesion = $stmt->fetchColumn();

        $stmt = $conn->prepare("SELECT COUNT(*) FROM resenas WHERE tutor_id = ? AND estudiante_id = ?");
        $stmt->execute([$tutor_id, $estudiante_id]);
        $yaReseno = $stmt->fetchColumn();

        if ($tieneSesion > 0 && $yaReseno == 0) {
            ?>
            <section class="review-form">
                <h3>Dejar una reseña</h3>
                <form method="POST" action="procesar_resena.php" class="form-box">
                    <input type="hidden" name="tutor_id" value="<?= htmlspecialchars($tutor_id) ?>">

                    <label for="puntuacion">Calificación (1-5):</label>
                    <select id="puntuacion" name="puntuacion" required>
                        <option value="">Seleccione</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>

                    <label for="comentario">Comentario:</label>
                    <textarea name="comentario" id="comentario" rows="4" required></textarea>

                    <button type="submit" class="btn btn-secondary">Enviar Reseña</button>
                </form>
            </section>
            <?php
        }
    } catch (PDOException $e) {
        echo "<p class='error-msg'>Error al verificar sesiones previas: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "</div>"; // .tutor-profile
include 'includes/footer.php';
?>
