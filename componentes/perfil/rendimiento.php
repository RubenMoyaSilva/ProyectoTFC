<?php
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'tutor') {
    echo "<p>No autorizado.</p>";
    return;
}

$tutor_id = $_SESSION['usuario_id'];

try {
    // Obtener número total de sesiones completadas
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM sesiones
        WHERE tutor_id = ? AND estado = 'completada'
    ");
    $stmt->execute([$tutor_id]);
    $total_sesiones = $stmt->fetchColumn();

    // Obtener promedio de calificación (resenas)
    $stmt = $conn->prepare("
        SELECT AVG(calificacion) AS promedio 
        FROM resenas 
        WHERE tutor_id = ?
    ");
    $stmt->execute([$tutor_id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $promedio = number_format($resultado['promedio'] ?? 0, 2);
} catch (PDOException $e) {
    echo "<p>Error al cargar datos de rendimiento: " . htmlspecialchars($e->getMessage()) . "</p>";
    return;
}
?>

<section class="tutor-rendimiento">
    <h3>Rendimiento como tutor</h3>
    <p><strong>Total de sesiones completadas:</strong> <?= $total_sesiones ?></p>
    <p><strong>Calificación promedio:</strong> <?= $promedio ?>/5.00</p>
</section>
