<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'estudiante') {
    echo "<p>Acceso no autorizado.</p>";
    exit;
}

$estudiante_id = $_SESSION['usuario_id'];

// Validar POST
if (!isset($_POST['tutor_id'], $_POST['puntuacion'], $_POST['comentario'])) {
    echo "<p>Datos incompletos.</p>";
    exit;
}

$tutor_id = (int)$_POST['tutor_id'];
$puntuacion = (int)$_POST['puntuacion'];
$comentario = trim($_POST['comentario']);

// Validación básica
if ($puntuacion < 1 || $puntuacion > 5 || strlen($comentario) < 5) {
    echo "<p>Datos inválidos. Asegúrate de dar una puntuación entre 1 y 5 y un comentario válido.</p>";
    exit;
}

try {
    // Verificar que el estudiante tuvo al menos una sesión completada o confirmada con el tutor
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM sesiones
        WHERE tutor_id = ? AND estudiante_id = ? AND estado IN ('completada', 'confirmada')
    ");
    $stmt->execute([$tutor_id, $estudiante_id]);
    $existeSesion = $stmt->fetchColumn();

    if ($existeSesion == 0) {
        echo "<p>No puedes dejar una reseña sin haber tenido una sesión con este tutor.</p>";
        exit;
    }

    // Verificar si ya dejó una reseña para este tutor
    $stmt = $conn->prepare("SELECT COUNT(*) FROM resenas WHERE tutor_id = ? AND estudiante_id = ?");
    $stmt->execute([$tutor_id, $estudiante_id]);
    if ($stmt->fetchColumn() > 0) {
        echo "<p>Ya has dejado una reseña para este tutor.</p>";
        exit;
    }

    // Insertar reseña
    $stmt = $conn->prepare("
        INSERT INTO resenas (tutor_id, estudiante_id, calificacion, comentario, fecha)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$tutor_id, $estudiante_id, $puntuacion, $comentario]);

    echo "<p>¡Gracias por dejar tu reseña!</p>";
    echo "<p><a href='tutor.php?id=" . htmlspecialchars($tutor_id) . "'>Volver al perfil del tutor</a></p>";

} catch (PDOException $e) {
    echo "<p>Error al guardar la reseña: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
