<form action="procesar_registro.php" method="POST" class="form-auth" id="form-registro" style="display:none;">
  <h2>Crear Cuenta</h2>

  <div class="form-grupo">
    <label for="nombre">Nombre completo</label>
    <input type="text" name="nombre" id="nombre" required>
  </div>

  <div class="form-grupo">
    <label for="email_registro">Correo electrónico</label>
    <input type="email" name="email" id="email_registro" required>
  </div>

  <div class="form-grupo">
    <label for="password_registro">Contraseña</label>
    <input type="password" name="password" id="password_registro" required>
  </div>

  <div class="form-grupo">
    <label for="rol">Rol</label>
    <select name="rol" id="rol" required>
      <option value="estudiante">Estudiante</option>
      <option value="tutor">Tutor</option>
    </select>
  </div>

  <button type="submit" class="btn-auth">Registrarse</button>

  <p class="cambio-form">¿Ya tienes cuenta? <a href="#" onclick="mostrarLogin()">Inicia sesión</a></p>
</form>
