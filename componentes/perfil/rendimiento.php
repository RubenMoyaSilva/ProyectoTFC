<?php
require_once __DIR__ . '/../../includes/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'tutor') {
    header("Location: perfil.php");
    exit;
}

$tutor_id = $_SESSION['usuario_id'];

// Clases dadas
$stmt1 = $conn->prepare("SELECT COUNT(*) FROM tutorias WHERE tutor_id = ?");
$stmt1->execute([$tutor_id]);
$clases_dadas = $stmt1->fetchColumn();

// Calificación promedio
$stmt2 = $conn->prepare("SELECT AVG(puntuacion) FROM resenas WHERE tutor_id = ?");
$stmt2->execute([$tutor_id]);
$promedio_raw = $stmt2->fetchColumn();
$promedio = $promedio_raw !== null ? round($promedio_raw, 2) : null;

// Reseñas positivas (puntuacion >= 4)
$stmt3 = $conn->prepare("SELECT COUNT(*) FROM resenas WHERE tutor_id = ? AND puntuacion >= 4");
$stmt3->execute([$tutor_id]);
$positivas = $stmt3->fetchColumn();
?>
?>

<div class="rendimiento">
    <h2>Rendimiento del Tutor</h2>
    <p><strong>Clases dadas:</strong> <?= $clases_dadas ?></p>
    <p><strong>Calificación promedio:</strong> <?= $promedio ?: 'Sin reseñas' ?>/5</p>
    <p><strong>Reseñas positivas:</strong> <?= $positivas ?></p>
</div>
