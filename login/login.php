<?php
session_start();
$error = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title></title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="script.js" defer></script>
</head>
<body class="<?= (isset($_COOKIE['modo']) && $_COOKIE['modo'] === 'oscuro') ? 'dark-mode' : '' ?>">
  <?php include("includes/header.php"); ?>

  <div class="container mt-5 contenedorgeneral p-4 rounded shadow">
    <h1 class="titulo text-center mb-4">Iniciar Sesión</h1>

    <?php if ($error): ?>
      <div class="alert alert-danger text-center">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="procesar_login.php" method="POST" class="mx-auto" style="max-width: 400px;">
      <div class="mb-3">
        <label for="usuario" class="form-label-login">Usuario</label>
        <input type="text" class="form-control" id="usuario" name="usuario" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label-login">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-success w-100 mb-3">Entrar</button>
      <a href="registro.php" class="btn btn-outline-azul w-100">¿No tienes cuenta? Regístrate</a>
    </form>
  </div>

  <?php include("includes/footer.php"); ?>
</body>
</html>
