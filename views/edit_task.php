<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID de tarea no proporcionado.";
    exit();
}

require_once('../config/db.php');

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['usuario_id']]);
$tarea = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarea) {
    echo "Tarea no encontrada.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <link rel="stylesheet" href="../public/edit.css">
</head>
<body>

<h2>Editar Tarea</h2>

<form action="../actions/update_task.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $tarea['id']; ?>">

    <label>Título:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($tarea['title']); ?>" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="description"><?php echo htmlspecialchars($tarea['description']); ?></textarea><br><br>

    <label>Estado:</label><br>
    <select name="is_completed">
        <option value="0" <?php echo $tarea['is_completed'] == 0 ? 'selected' : ''; ?>>Pendiente</option>
        <option value="1" <?php echo $tarea['is_completed'] == 1 ? 'selected' : ''; ?>>Completada</option>
    </select><br><br>

    <label>Prioridad:</label><br>
    <select name="priority" required>
        <option value="alta" <?php echo $tarea['priority'] == 'alta' ? 'selected' : ''; ?>>Alta</option>
        <option value="media" <?php echo $tarea['priority'] == 'media' ? 'selected' : ''; ?>>Media</option>
        <option value="baja" <?php echo $tarea['priority'] == 'baja' ? 'selected' : ''; ?>>Baja</option>
    </select><br><br>

    <label>Fecha límite:</label><br>
    <input type="date" name="due_date" value="<?php echo htmlspecialchars($tarea['due_date']); ?>"><br><br>

    <button type="submit">Guardar cambios</button>
    <a href="dashboard.php">Cancelar</a>
</form>

</body>
</html>
