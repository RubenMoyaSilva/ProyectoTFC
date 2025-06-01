<?php
$host = 'localhost';
$db = 'bbdd';
$user = 'usuario';
$pass = 'contraseña';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>
