<?php
if (!isset($tutor['id'])) {
    echo "<p>Error: tutor no definido.</p>";
    return;
}

$tutor_id = $tutor['id'];

try {
    $stmt = $conn->prepare("
        SELECT r.puntuacion, r.comentario, r.creado_en, u.nombre 
        FROM resenas r
        JOIN usuarios u ON r.estudiante_id = u.id
        WHERE r.tutor_id = ?
        ORDER BY r.creado_en DESC
    ");
    $stmt->execute([$tutor_id]);
    $resenas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Error al cargar reseñas.</p>";
    return;
}
?>

<section class="tutor-resenas">
    <h3>Reseñas de estudiantes</h3>

    <?php if (count($resenas) > 0): ?>
        <?php foreach ($resenas as $resena): ?>
            <div class="resena">
                <p><strong><?= htmlspecialchars($resena['nombre']) ?></strong> dejó una reseña:</p>
                <p><strong>Puntuación:</strong> <?= intval($resena['puntuacion']) ?>/5</p>
                <p><?= nl2br(htmlspecialchars($resena['comentario'])) ?></p>
                <p class="fecha"><?= date("d/m/Y H:i", strtotime($resena['creado_en'])) ?></p>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Este tutor aún no tiene reseñas.</p>
    <?php endif; ?>
</section>