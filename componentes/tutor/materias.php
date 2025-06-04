<?php
// Se espera que $tutor ya esté definido

if (!isset($tutor)) {
    echo "<p>Error: tutor no definido.</p>";
    return;
}

// Convertimos materias en arreglo
$materias = array_map('trim', explode(',', $tutor['materias']));
?>

<section class="tutor-materias">
    <h3>Materias que imparte</h3>
    
    <?php if (count($materias) > 0 && $materias[0] !== ''): ?>
        <ul>
            <?php foreach ($materias as $materia): ?>
                <li><?= htmlspecialchars($materia) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Este tutor aún no ha especificado sus materias.</p>
    <?php endif; ?>
</section>
