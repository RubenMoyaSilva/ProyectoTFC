<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = trim($_POST['nombre'] ?? '');
    $nuevo_email = trim($_POST['email'] ?? '');
    $nueva_foto = trim($_POST['foto_perfil'] ?? '');

    if ($nuevo_nombre && $nuevo_email) {
        try {
            $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ?, foto_perfil = ? WHERE id = ?");
            $stmt->execute([$nuevo_nombre, $nuevo_email, $nueva_foto, $usuario_id]);

            // Refrescar para volver al modo de vista
            header("Location: perfil.php");
            exit;
        } catch (PDOException $e) {
            echo "<p>Error al actualizar perfil.</p>";
        }
    } else {
        echo "<p>Todos los campos obligatorios deben estar completos.</p>";
    }
}
?>

<form method="POST" action="perfil.php?editar=1" class="form-editar-perfil">
    <label for="nombre">Nombre:</label><br>
    <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required><br><br>

    <label for="email">Correo:</label><br>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['email']) ?>" required><br><br>

    <button type="submit">Guardar Cambios</button>
    <a href="perfil.php">Cancelar</a>
</form>

