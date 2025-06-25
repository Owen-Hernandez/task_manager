<?php
session_start();
require_once('../config/db.php');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

// Verificar que se recibió una tarea por POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    $user_id = $_SESSION['usuario_id'];

    // Verificar que la tarea pertenezca al usuario actual
    $stmt = $pdo->prepare("UPDATE tasks SET is_completed = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);

    header("Location: ../views/dashboard.php?mensaje=tarea_completada");
    exit();
} else {
    header("Location: ../views/dashboard.php?mensaje=error_datos_invalidos");
    exit();
}
