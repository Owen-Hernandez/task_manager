<?php
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Verificar si el usuario ya existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            echo "El nombre de usuario ya existe. <a href='../views/register.php'>Volver</a>";
        } else {
            // Encriptar contraseña y guardar
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hashed_password])) {
                header("Location: ../views/login.php?mensaje=registro_exitoso");
                exit;
            } else {
                echo "Error al registrar usuario.";
            }
        }
    } else {
        echo "Todos los campos son obligatorios.";
    }
} else {
    header("Location: ../views/login.php?mensaje=registro_exitoso");
    exit;
}
