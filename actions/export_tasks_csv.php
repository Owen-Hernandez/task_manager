<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require '../config/db.php';

// Obtener las tareas del usuario
$stmt = $pdo->prepare("SELECT title, description, priority, due_date, is_completed, created_at FROM tasks WHERE user_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Configurar headers para descarga
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=tareas.csv');

// Abrir salida estándar para escribir el CSV
$output = fopen('php://output', 'w');

// Escribir fila de encabezados
fputcsv($output, ['Título', 'Descripción', 'Prioridad', 'Fecha límite', '¿Completada?', 'Fecha de creación']);

// Escribir cada tarea
foreach ($tareas as $tarea) {
    fputcsv($output, [
        $tarea['title'],
        $tarea['description'],
        ucfirst($tarea['priority']),
        $tarea['due_date'],
        $tarea['is_completed'] ? 'Sí' : 'No',
        $tarea['created_at']
    ]);
}

fclose($output);
exit();
