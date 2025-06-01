<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=bbdd;charset=utf8", "root", "");
    // Configurar modo de error a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error en conexión: " . $e->getMessage());
}
?>
