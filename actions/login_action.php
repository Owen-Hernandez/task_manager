<?php
session_start();
require_once('../config/db.php'); // Aquí tienes $pdo

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_input = trim($_POST['usuario']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username_input]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['username'];

            header("Location: ../views/dashboard.php");
            exit();
        } else {
            header("Location: ../views/login.php?mensaje=clave_incorrecta");
            exit();
        }
    } else {
        header("Location: ../views/login.php?mensaje=usuario_no_existe");
        exit();
    }
}
