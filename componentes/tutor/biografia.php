<?php
// Se espera que $tutor ya esté definido en tutor.php antes de incluir este archivo

if (!isset($tutor)) {
    echo "<p>Error: tutor no definido.</p>";
    return;
}

$tutor_id = $tutor['id'];
$calificacionPromedio = 0.00;

try {
    // Consultar calificación promedio basada en reseñas
    $stmt = $conn->prepare("SELECT AVG(calificacion) as promedio FROM resenas WHERE tutor_id = ?");
    $stmt->execute([$tutor_id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $calificacionPromedio = $resultado['promedio'] !== null ? floatval($resultado['promedio']) : 0.00;

    // (Opcional) Actualizar la tabla de tutores para mantener el campo actualizado
    $stmt = $conn->prepare("UPDATE tutores SET calificacion_promedio = ? WHERE id = ?");
    $stmt->execute([$calificacionPromedio, $tutor_id]);

} catch (PDOException $e) {
    echo "<p>Error al calcular la calificación promedio.</p>";
    return;
}
?>

<section class="tutor-biografia">
    <h2>Sobre <?= htmlspecialchars($tutor['nombre']) ?></h2>

    <?php if (!empty($tutor['foto_perfil'])): ?>
        <img src="<?= htmlspecialchars($tutor['foto_perfil']) ?>" alt="Foto de perfil">
    <?php endif; ?>

    <p><strong>Biografía:</strong></p>
    <p><?= nl2br(htmlspecialchars($tutor['biografia'] ?? 'No disponible')) ?></p>

    <p><strong>Calificación promedio:</strong> <?= number_format($calificacionPromedio, 2) ?>/5.00</p>
</section>
