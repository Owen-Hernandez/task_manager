<?php
session_start();
require_once('../config/db.php');

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../views/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    // Verificar que la tarea pertenezca al usuario
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $_SESSION['usuario_id']]);
    $task = $stmt->fetch();

    if ($task) {
        // Cambiar el estado de la tarea (0 → 1, 1 → 0)
        $new_status = $task['is_completed'] ? 0 : 1;
        $update = $pdo->prepare("UPDATE tasks SET is_completed = ? WHERE id = ?");
        $update->execute([$new_status, $task_id]);
    }
}

header("Location: ../views/dashboard.php");
exit();
