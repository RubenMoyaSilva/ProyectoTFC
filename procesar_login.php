<?php
session_start();
require_once "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? '');
    $password = $_POST["password"] ?? '';

    if (!$usuario || !$password) {
        header("Location: login.php?error=" . urlencode("Por favor completa todos los campos."));
        exit;
    }

    // Consulta para buscar al usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
    $stmt->execute(['usuario' => $usuario]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado && password_verify($password, $resultado["password"])) {
        $_SESSION["usuario"] = $resultado["usuario"];
        header("Location: index.php?login=1");
        exit;
    } else {
        header("Location: login.php?error=" . urlencode("Usuario o contraseña incorrectos."));
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
