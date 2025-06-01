<?php
  include('includes/header.php');
?>

<main class="contenedor-auth">

  <div class="contenedor-formularios">
    <?php include('componentes/auth/form_login.php'); ?>
    <?php include('componentes/auth/form_registro.php'); ?>
  </div>

</main>

<?php include('includes/footer.php'); ?>

<script>
  function mostrarRegistro() {
    document.getElementById('form-login').style.display = 'none';
    document.getElementById('form-registro').style.display = 'block';
  }

  function mostrarLogin() {
    document.getElementById('form-login').style.display = 'block';
    document.getElementById('form-registro').style.display = 'none';
  }
</script>