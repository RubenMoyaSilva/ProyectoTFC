<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/funciones.php';
include 'includes/header.php';

?>

<main>
  <?php

  include 'componentes/home/banner.php';
  include 'componentes/home/como_funciona.php';
  include 'componentes/home/testimonios.php';
  include 'componentes/home/contacto.php';
  ?>
</main>

<?php
include 'includes/footer.php';
