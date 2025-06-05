<?php

require_once(__DIR__ . '/../../includes/db.php');
// Verifica que el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: auth.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT id, nombre, email, rol, foto_perfil FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "<p>Error: Usuario no encontrado.</p>";
    exit();
}
?>

<div class="info-personal">
    <h2>Información Personal</h2>
    <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
    <p><strong>Rol:</strong> <?= htmlspecialchars($usuario['rol']) ?></p>
    <?php if ($usuario['foto_perfil']): ?>
        <img src="assets/img/<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil" width="100">
    <?php endif; ?>
</div>
