<?php
$modo = $_GET['modo'] ?? 'login'; // ?modo=registro para alternar
include 'includes/header.php';

if ($modo === 'registro') {
  include 'componentes/auth/form_registro.php';
} else {
  include 'componentes/auth/form_login.php';
}

include 'includes/footer.php';