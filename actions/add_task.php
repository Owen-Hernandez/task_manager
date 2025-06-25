<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once('../config/db.php');

// Validar que los datos necesarios estén presentes
if (!isset($_POST['title'], $_POST['priority'])) {
    header("Location: ../views/dashboard.php?error=missing_data");
    exit();
}

$title = trim($_POST['title']);
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// 🟡 CORRECCIÓN PRINCIPAL: Tomar el valor directo del select (alta/media/baja)
$priority = isset($_POST['priority']) && in_array($_POST['priority'], ['alta', 'media', 'baja'])
    ? $_POST['priority']
    : 'media'; // Valor por defecto solo si hay manipulación

$user_id = $_SESSION['usuario_id'];

// Capturar fecha límite (puede venir vacía)
$due_date = isset($_POST['due_date']) && !empty($_POST['due_date']) ? $_POST['due_date'] : null;

// Preparar SQL dinámicamente según si hay fecha o no
if ($due_date) {
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, priority, due_date, is_completed, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
    $stmt->execute([$user_id, $title, $description, $priority, $due_date]);
} else {
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, priority, is_completed, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
    $stmt->execute([$user_id, $title, $description, $priority]);
}

header("Location: ../views/dashboard.php");
exit();