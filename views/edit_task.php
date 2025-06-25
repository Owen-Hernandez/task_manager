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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../public/edit.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h4 mb-0"><i class="fas fa-edit me-2"></i>Editar Tarea</h2>
                    </div>
                    <div class="card-body">
                        <form action="../actions/update_task.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $tarea['id']; ?>">
                            
                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Título:</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($tarea['title']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Descripción:</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3"><?php echo htmlspecialchars($tarea['description']); ?></textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="is_completed" class="form-label fw-bold">Estado:</label>
                                    <select class="form-select" id="is_completed" name="is_completed">
                                        <option value="0" <?php echo $tarea['is_completed'] == 0 ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="1" <?php echo $tarea['is_completed'] == 1 ? 'selected' : ''; ?>>Completada</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="priority" class="form-label fw-bold">Prioridad:</label>
                                    <select class="form-select" id="priority" name="priority" required>
                                        <option value="alta" <?php echo $tarea['priority'] == 'alta' ? 'selected' : ''; ?>>Alta</option>
                                        <option value="media" <?php echo $tarea['priority'] == 'media' ? 'selected' : ''; ?>>Media</option>
                                        <option value="baja" <?php echo $tarea['priority'] == 'baja' ? 'selected' : ''; ?>>Baja</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="due_date" class="form-label fw-bold">Fecha límite:</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" 
                                       value="<?php echo htmlspecialchars($tarea['due_date']); ?>">
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Guardar cambios
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>