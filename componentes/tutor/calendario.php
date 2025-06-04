<?php
require_once(__DIR__ . '/../../includes/db.php');

if (!isset($tutor['id'])) {
    echo "<p>Error: tutor no definido.</p>";
    return;
}

// Este ID es probablemente el ID de usuario (usuarios.id), no el de tutores
$usuario_id = $tutor['id'];

// Buscar el ID real del tutor
$stmt = $conn->prepare("SELECT id FROM tutores WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$tutorDB = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tutorDB) {
    echo "<p>Error: No se encontró el tutor en la base de datos.</p>";
    return;
}

$tutor_id = $tutorDB['id'];

// Cargar disponibilidad
try {
    $stmt = $conn->prepare("
        SELECT dia_semana, hora_inicio, hora_fin 
        FROM disponibilidad 
        WHERE tutor_id = ?
        ORDER BY FIELD(dia_semana, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'), hora_inicio
    ");
    $stmt->execute([$tutor_id]);
    $disponibilidad = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Error al cargar disponibilidad: " . htmlspecialchars($e->getMessage()) . "</p>";
    return;
}
?>

<section class="tutor-disponibilidad">
    <h3>Disponibilidad Semanal</h3>

    <?php if (count($disponibilidad) > 0): ?>
        <ul>
            <?php foreach ($disponibilidad as $slot): ?>
                <li>
                    <?= htmlspecialchars($slot['dia_semana']) ?>:
                    <?= substr($slot['hora_inicio'], 0, 5) ?> - <?= substr($slot['hora_fin'], 0, 5) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Este tutor aún no ha configurado su disponibilidad.</p>
    <?php endif; ?>
</section>
