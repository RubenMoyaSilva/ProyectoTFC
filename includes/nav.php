<?php 
require_once 'includes/funciones.php';
?>
<nav>
    <ul>
        <li><a href="index.php">Inicio</a></li>
        <li><a href="buscar.php">Buscar Tutores</a></li>
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <li><a href="perfil.php">Mi Perfil</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        <?php else: ?>
            <li><a href="auth.php">Login / Registro</a></li>
        <?php endif; ?>
    </ul>
</nav>