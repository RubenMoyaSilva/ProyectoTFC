<?php
session_start();
require_once 'includes/db.php';
include 'includes/header.php';

// Obtener id del tutor de la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>ID de tutor inválido.</p>";
    include 'includes/footer.php';
    exit;
}

$tutor_id = (int)$_GET['id'];

// Consultar datos del tutor con info de usuario
try {
    $stmt = $conn->prepare("
        SELECT u.id, u.nombre, u.foto_perfil, t.biografia, t.materias, t.calificacion_promedio
        FROM usuarios u
        JOIN tutores t ON u.id = t.usuario_id
        WHERE u.id = ? AND u.rol = 'tutor'
        LIMIT 1
    ");
    $stmt->execute([$tutor_id]);
    $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tutor) {
        echo "<p>Tutor no encontrado.</p>";
        include 'includes/footer.php';
        exit;
    }
} catch (PDOException $e) {
    echo "<p>Error en la consulta: " . htmlspecialchars($e->getMessage()) . "</p>";
    include 'includes/footer.php';
    exit;
}

// Ahora $tutor está disponible para los componentes
include 'componentes/tutor/biografia.php';
include 'componentes/tutor/materias.php';
include 'componentes/tutor/calendario.php';
include 'componentes/tutor/resenas.php';

include 'includes/footer.php';
