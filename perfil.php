<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: auth.php");
    exit();
}

include('includes/header.php');

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_rol = $_SESSION['rol'];

// Bandera para mostrar mensaje
$perfil_actualizado = isset($_GET['actualizado']) && $_GET['actualizado'] == '1';
?>

<main class="contenedor-perfil">
  <h1>Bienvenido, <?= htmlspecialchars($usuario_nombre); ?></h1>

  <?php if ($perfil_actualizado): ?>
    <p class="mensaje-exito">Perfil actualizado correctamente.</p>
  <?php endif; ?>

  <section class="perfil-info">
    <?php include('componentes/perfil/info_personal.php'); ?>
  </section>

  <section class="perfil-edicion">
    <a href="?editar=1" class="btn-editar">Editar perfil</a>
    <?php if (isset($_GET['editar']) && $_GET['editar'] == '1'): ?>
      <?php include('componentes/perfil/editar_perfil.php'); ?>
    <?php endif; ?>
  </section>

  <section class="perfil-dashboard">
    <?php include('componentes/perfil/dashboard.php'); ?>
  </section>

  <?php if ($usuario_rol === 'tutor'): ?>
    <section class="perfil-rendimiento">
      <?php include('componentes/perfil/rendimiento.php'); ?>
    </section>
  <?php endif; ?>
</main>

<?php include('includes/footer.php'); ?>
