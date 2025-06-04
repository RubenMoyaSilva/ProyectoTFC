<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
// require_once __DIR__ . '/includes/nav.php';

// Recoger filtro materia
$materia = $_GET['materia'] ?? '';

// Construir la consulta dinámica
$query = "SELECT u.id, u.nombre, u.foto_perfil, t.biografia, t.materias, t.calificacion_promedio
          FROM usuarios u
          JOIN tutores t ON u.id = t.usuario_id
          WHERE u.rol = 'tutor'";

$params = [];

if (!empty($materia)) {
    $query .= " AND t.materias LIKE ?";
    $params[] = "%$materia%";
}

$query .= " ORDER BY t.calificacion_promedio DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$tutores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="buscar-container">
    <h1>Buscar Tutores</h1>

    <form method="GET" action="buscar.php" class="filtros-busqueda">
        <input type="text" name="materia" placeholder="Materia" value="<?= htmlspecialchars($materia) ?>">
        <button type="submit">Buscar</button>
    </form>

    <div class="lista-tutores">
        <?php if ($tutores): ?>
            <?php foreach ($tutores as $tutor): ?>
                <div class="tutor-card">
                    <img src="<?= htmlspecialchars($tutor['foto_perfil'] ?? 'assets/img/default.png') ?>" alt="Foto perfil">
                    <h3><?= htmlspecialchars($tutor['nombre']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($tutor['biografia'] ?? 'Sin biografía')) ?></p>
                    <p><strong>Materias:</strong> <?= ($tutor['materias']) ?></p>
                    <p><strong>Calificación:</strong> <?= number_format($tutor['calificacion_promedio'], 2) ?>/5</p>
                    <a href="tutor.php?id=<?= $tutor['id'] ?>" class="ver-mas">Ver perfil</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron tutores con esos criterios.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>