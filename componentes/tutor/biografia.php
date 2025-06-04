<?php
// Se espera que $tutor ya esté definido en tutor.php antes de incluir este archivo

if (!isset($tutor)) {
    echo "<p>Error: tutor no definido.</p>";
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

    <p><strong>Calificación promedio:</strong> <?= number_format($tutor['calificacion_promedio'], 2) ?>/5</p>
</section>
