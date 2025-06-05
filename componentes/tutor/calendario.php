<?php
// Suponiendo que $tutor['id'] está disponible y $conn también

// Obtener el ID real del tutor en tabla tutores
$stmt = $conn->prepare("SELECT id FROM tutores WHERE usuario_id = ?");
$stmt->execute([$tutor['id']]);
$tutorDB = $stmt->fetch(PDO::FETCH_ASSOC);
$tutor_id = $tutorDB ? $tutorDB['id'] : null;

if (!$tutor_id) {
    echo "<p>Error: Tutor no encontrado en la base de datos.</p>";
    return;
}

// Cargar disponibilidad ordenada por día y hora
$stmt = $conn->prepare("
    SELECT dia_semana, hora_inicio, hora_fin
    FROM disponibilidad
    WHERE tutor_id = ?
    ORDER BY FIELD(dia_semana, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'), hora_inicio
");
$stmt->execute([$tutor_id]);
$disponibilidad = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="tutor-disponibilidad">
    <h3>Disponibilidad Semanal</h3>

    <?php if (count($disponibilidad) > 0): ?>
        <ul>
            <?php foreach ($disponibilidad as $slot): ?>
                <li>
                    <?= htmlspecialchars($slot['dia_semana']) ?>:
                    <?= substr($slot['hora_inicio'], 0, 5) ?> - <?= substr($slot['hora_fin'], 0, 5) ?>
                    &nbsp;&nbsp;
                    <a href="tutor.php?id=<?= $tutor['id'] ?>&reservar=1&dia_semana=<?= urlencode($slot['dia_semana']) ?>&hora_inicio=<?= urlencode(substr($slot['hora_inicio'], 0, 5)) ?>" class="btn-reservar">
                        Reservar
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Este tutor aún no ha configurado su disponibilidad.</p>
    <?php endif; ?>
</section>
