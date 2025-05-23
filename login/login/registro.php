<?php
// registro.php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - PixelTec</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="script.js" defer></script>
</head>
<body class="<?= (isset($_COOKIE['modo']) && $_COOKIE['modo'] === 'oscuro') ? 'dark-mode' : '' ?>">
  <?php include("includes/header.php"); ?>

  <div class="container mt-5 contenedorgeneral p-4 rounded shadow">
    <h1 class="titulo text-center mb-4">Crear una cuenta</h1>

    <form action="procesar_registro.php" method="POST" class="mx-auto" style="max-width: 500px;">
      <div class="mb-3">
        <label for="usuario" class="form-label-login">Nombre de usuario</label>
        <input type="text" class="form-control" id="usuario" name="usuario" required pattern="^[a-zA-Z0-9_]{4,20}$" title="Letras, números o guiones bajos (4-20 caracteres)">
      </div>

      <div class="mb-3">
        <label for="email" class="form-label-login">Correo electrénico</label>
        <input type="email" class="form-control" id="email" name="email" required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Introduce un correo válido">
      </div>

      <div class="mb-3">
        <label for="telefono" class="form-label-login">Teléfono</label>
        <input type="tel" class="form-control" id="telefono" name="telefono" required pattern="^[0-9]{9}$" title="Introduce un número de teléfono de 9 dígitos">
      </div>

      <div class="mb-3">
        <label for="direccion" class="form-label-login">Dirección</label>
        <input type="text" class="form-control" id="direccion" name="direccion" required minlength="5" title="Introduce una dirección válida">
      </div>

      <div class="mb-3">
        <label for="password" class="form-label-login">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$" title="Debe contener al menos 6 caracteres, incluyendo letras y números">
      </div>

      <div class="mb-3">
        <label for="confirmar_password" class="form-label-login">Confirmar contraseña</label>
        <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
      </div>

      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="verPassword">
        <label class="form-check-label" for="verPassword">Mostrar contraseñas</label>
      </div>

      <button type="submit" class="btn btn-success w-100">Registrarse</button>
      <a href="login.php" class="btn btn-outline-azul w-100 mt-3">¿Ya tienes cuenta? Inicia sesión</a>
    </form>
  </div>

  <?php include("includes/footer.php"); ?>
</body>
</html>