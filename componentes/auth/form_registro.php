<form action="procesar_registro.php" method="POST" class="form-auth" id="form-registro" style="display:none;">
<h2>Registro</h2>

  <div class="form-grupo">
    <label for="nombre_registro">Nombre completo</label>
    <input type="text" name="nombre" id="nombre_registro" required>
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
    <label for="rol_registro">Rol</label>
    <select name="rol" id="rol_registro" required onchange="mostrarMaterias()">
      <option value="">Selecciona un rol</option>
      <option value="estudiante">Estudiante</option>
      <option value="tutor">Tutor</option>
    </select>
  </div>

  <div class="form-grupo" id="materias_grupo" style="display:none;">
    <label for="materias_registro">Materias (separadas por comas)</label>
    <input type="text" name="materias" id="materias_registro" placeholder="Ej: Matemáticas, Física, Inglés">
  </div>

  <button type="submit" class="btn-auth">Registrarse</button>

  <p class="cambio-form">¿Ya tienes cuenta? <a href="#" onclick="mostrarLogin()">Iniciar sesión</a></p>
</form>
<script>
function mostrarMaterias() {
  const rolSelect = document.getElementById('rol_registro');
  const materiasGrupo = document.getElementById('materias_grupo');

  if (rolSelect.value === 'tutor') {
    materiasGrupo.style.display = 'block';
  } else {
    materiasGrupo.style.display = 'none';
  }
}
</script>