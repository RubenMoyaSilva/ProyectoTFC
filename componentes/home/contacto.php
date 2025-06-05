<section class="contacto">
  <div class="container">
    <h2 class="titulo-seccion">¿Necesitas ayuda?</h2>
    <p>Ponte en contacto con nosotros si tienes preguntas, sugerencias o necesitas soporte.</p>

    <form action="index.php" method="POST" class="formulario-contacto">
      <div class="form-grupo">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required>
      </div>

      <div class="form-grupo">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div class="form-grupo">
        <label for="mensaje">Mensaje</label>
        <textarea id="mensaje" name="mensaje" rows="5" required></textarea>
      </div>

      <button type="submit" class="btn-enviar">Enviar</button>
    </form>
  </div>
</section>
