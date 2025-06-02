<?php
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? '';

    if (empty($nombre) || empty($email) || empty($password_raw) || empty($rol)) {
        die("Faltan campos obligatorios.");
    }

    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    // Verificar si existe el email
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "Este correo ya está registrado.";
        exit;
    } 

    // Insertar en usuarios
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $resultado = $stmt->execute([$nombre, $email, $password, $rol]);

    if ($resultado) {
        $usuario_id = $conn->lastInsertId();

        // Si el rol es tutor, insertar en tutores y materias
        if ($rol === 'tutor') {
            $materias = $_POST['materias'] ?? '';
            $materias = trim($materias);

            if (empty($materias)) {
                // Aquí decides si quieres detener el registro o dejar vacío el campo materias
                die("Debes ingresar las materias para el rol tutor.");
            }

            $stmtTutor = $conn->prepare("INSERT INTO tutores (usuario_id, materias) VALUES (?, ?)");
            $stmtTutor->execute([$usuario_id, $materias]);
        }

        header("Location: perfil.php?registro=exito");
        exit();
    } else {
        echo "Error al registrar el usuario.";
    }
} else {
    header("Location: auth.php");
    exit();
}

