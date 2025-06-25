<?php
session_start();
require_once('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    $user_id = $_SESSION['usuario_id'];

    // Verifica que la tarea pertenezca al usuario
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);

    header("Location: ../views/dashboard.php");
    exit();
} else {
    header("Location: ../views/dashboard.php?mensaje=error_eliminacion");
    exit();
}
