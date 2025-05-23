<?php
session_start();
require_once "includes/db.php"; // Aquí usamos tu conexión con $pdo

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $telefono = trim($_POST["telefono"] ?? '');
    $direccion = trim($_POST["direccion"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirmar = $_POST["confirmar_password"] ?? '';

    // Validaciones básicas
    if (!$usuario || !$email || !$telefono || !$direccion || !$password || !$confirmar) {
        die("Por favor completa todos los campos.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("El correo no es válido.");
    }

    if (!preg_match('/^[0-9]{9}$/', $telefono)) {
        die("El teléfono debe tener 9 dígitos.");
    }

    if ($password !== $confirmar) {
        die("Las contraseñas no coinciden.");
    }

    // Verificar si usuario o email ya existen
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = :usuario OR email = :email");
    $stmt->execute(['usuario' => $usuario, 'email' => $email]);

    if ($stmt->fetch()) {
        die("El usuario o el email ya están registrados.");
    }

    // Encriptar contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar en la base de datos
    $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, email, telefono, direccion, password) VALUES (:usuario, :email, :telefono, :direccion, :password)");
    $resultado = $stmt->execute([
        'usuario' => $usuario,
        'email' => $email,
        'telefono' => $telefono,
        'direccion' => $direccion,
        'password' => $passwordHash
    ]);

    if ($resultado) {
        header("Location: login.php?registro=exitoso");
        exit;
    } else {
        die("Error al registrar el usuario.");
    }
} else {
    header("Location: registro.php");
    exit;
}




