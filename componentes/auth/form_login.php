<form action="procesar_login.php" method="POST" class="form-auth" id="form-login">
  <h2>Iniciar Sesión</h2>

  <div class="form-grupo">
    <label for="email_login">Correo electrónico</label>
    <input type="email" name="email" id="email_login" required>
  </div>

  <div class="form-grupo">
    <label for="password_login">Contraseña</label>
    <input type="password" name="password" id="password_login" required>
  </div>

  <button type="submit" class="btn-auth">Entrar</button>

  <p class="cambio-form">¿No tienes cuenta? <a href="#" onclick="mostrarRegistro()">Regístrate</a></p>
</form>
