<?php
session_start();
include('includes/db.php');


// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
  header("Location: auth.php");
  exit();
}
include('includes/header.php');
// Datos básicos del usuario
$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_rol = $_SESSION['rol'];
?>

<main class="contenedor-perfil">
  <h1>Bienvenido, <?php echo htmlspecialchars($usuario_nombre); ?></h1>

  <section class="perfil-info">
    <?php include('componentes/perfil/info_personal.php'); ?>
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
