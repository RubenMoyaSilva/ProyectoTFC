<?php
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    // Verificar si existe el email
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "Este correo ya está registrado.";
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        $resultado = $stmt->execute([$nombre, $email, $password, $rol]);

        if ($resultado) {
            header("Location: auth.php?registro=exito");
            exit();
        } else {
            echo "Error al registrar el usuario.";
        }
    }
} else {
    header("Location: auth.php");
    exit();
}
