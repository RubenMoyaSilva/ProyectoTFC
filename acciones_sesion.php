<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: auth.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

$accion = $_POST['accion'] ?? null;
$sesion_id = $_POST['sesion_id'] ?? null;

if (!$accion || !$sesion_id) {
    die("Datos incompletos.");
}

try {
    // Verifica si el usuario tiene acceso a la sesión
    $stmt = $conn->prepare("
        SELECT * FROM sesiones
        WHERE id = ? AND (
            (estudiante_id = ? AND ? = 'estudiante') OR
            (tutor_id = ? AND ? = 'tutor')
        )
    ");
    $stmt->execute([$sesion_id, $usuario_id, $rol, $usuario_id, $rol]);
    $sesion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sesion) {
        die("Sesión no encontrada o sin permisos.");
    }

    if ($accion === 'confirmar' && $rol === 'tutor') {
        $stmt = $conn->prepare("UPDATE sesiones SET estado = 'confirmada' WHERE id = ?");
        $stmt->execute([$sesion_id]);
    } elseif ($accion === 'cancelar') {
        $stmt = $conn->prepare("UPDATE sesiones SET estado = 'cancelada' WHERE id = ?");
        $stmt->execute([$sesion_id]);
    } else {
        die("Acción no permitida.");
    }

    header("Location: perfil.php");
    exit;
} catch (PDOException $e) {
    echo "Error al actualizar la sesión: " . htmlspecialchars($e->getMessage());
}
