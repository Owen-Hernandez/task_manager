<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('../config/db.php');

    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $is_completed = isset($_POST['is_completed']) ? (int) $_POST['is_completed'] : 0;
    $priority = isset($_POST['priority']) ? trim($_POST['priority']) : 'media';
    $due_date = isset($_POST['due_date']) && $_POST['due_date'] !== '' ? $_POST['due_date'] : null;

    $prioridadesValidas = ['baja', 'media', 'alta'];
    if (!in_array($priority, $prioridadesValidas)) {
        $priority = 'media';
    }

    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, is_completed = ?, priority = ?, due_date = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $description, $is_completed, $priority, $due_date, $id, $_SESSION['usuario_id']]);

    header("Location: ../views/dashboard.php");
    exit();
}
?>
