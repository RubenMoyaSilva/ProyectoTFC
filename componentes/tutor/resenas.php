<?php
if (!isset($tutor['id'])) {
    echo "<p>Error: tutor no definido.</p>";
    return;
}

$tutor_id = $tutor['id'];
$usuario_id = $_SESSION['usuario_id'] ?? null;
$yaReservo = false;
$yaReseno = false;

// Obtener reseñas existentes
try {
    $stmt = $conn->prepare("
        SELECT r.calificacion, r.comentario, r.fecha, u.nombre 
        FROM resenas r
        JOIN usuarios u ON r.estudiante_id = u.id
        WHERE r.tutor_id = ?
        ORDER BY r.fecha DESC
    ");
    $stmt->execute([$tutor_id]);
    $resenas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Error al cargar reseñas: " . htmlspecialchars($e->getMessage()) . "</p>";
    return;
}

// Verificar si el usuario actual es estudiante y puede dejar una reseña
if ($usuario_id && $_SESSION['rol'] === 'estudiante') {
    try {
        // ¿Ya reservó alguna sesión con este tutor?
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM sesiones 
            WHERE estudiante_id = ? AND tutor_id = ? AND estado IN ('completada', 'confirmada')
        ");
        $stmt->execute([$usuario_id, $tutor_id]);
        $yaReservo = $stmt->fetchColumn() > 0;

        // ¿Ya dejó una reseña?
        $stmt = $conn->prepare("SELECT COUNT(*) FROM resenas WHERE estudiante_id = ? AND tutor_id = ?");
        $stmt->execute([$usuario_id, $tutor_id]);
        $yaReseno = $stmt->fetchColumn() > 0;

    } catch (PDOException $e) {
        echo "<p>Error al verificar permisos de reseña.</p>";
    }
}
?>

<section class="tutor-resenas">
    <h3>Reseñas de estudiantes</h3>

    <?php if (count($resenas) > 0): ?>
        <?php foreach ($resenas as $resena): ?>
            <div class="resena">
                <p><strong><?= htmlspecialchars($resena['nombre']) ?></strong> dejó una reseña:</p>
                <p><strong>Puntuación:</strong> <?= intval($resena['calificacion']) ?>/5</p>
                <p><?= nl2br(htmlspecialchars($resena['comentario'])) ?></p>
                <p class="fecha"><?= date("d/m/Y H:i", strtotime($resena['fecha'])) ?></p>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Este tutor aún no tiene reseñas.</p>
    <?php endif; ?>

    <?php if ($yaReservo && !$yaReseno): ?>
        <div class="form-resena" style="margin-top:2em;">
            <h4>Dejar una reseña</h4>
            <form method="POST" action="procesar_resena.php">
                <input type="hidden" name="tutor_id" value="<?= htmlspecialchars($tutor_id) ?>">
                <label for="puntuacion">Puntuación:</label>
                <select name="puntuacion" id="puntuacion" required>
                    <option value="">--Seleccionar--</option>
                    <option value="1">1 - Muy malo</option>
                    <option value="2">2 - Malo</option>
                    <option value="3">3 - Regular</option>
                    <option value="4">4 - Bueno</option>
                    <option value="5">5 - Excelente</option>
                </select><br><br>

                <label for="comentario">Comentario:</label><br>
                <textarea name="comentario" id="comentario" rows="4" cols="50" required></textarea><br><br>

                <button type="submit">Enviar Reseña</button>
            </form>
        </div>
    <?php elseif ($yaReseno): ?>
        <p><em>Ya has dejado una reseña para este tutor.</em></p>
    <?php elseif (!$yaReservo): ?>
        <p><em>Debes completar al menos una sesión con este tutor antes de poder dejar una reseña.</em></p>
    <?php endif; ?>
</section>
