<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once('../config/db.php');
include('../includes/header.php');

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$filter_priority = '';
if (isset($_GET['priority']) && in_array($_GET['priority'], ['alta', 'media', 'baja'])) {
    $filter_priority = $_GET['priority'];
}

$sql = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$_SESSION['usuario_id']];

if ($search !== '') {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $likeSearch = "%$search%";
    $params[] = $likeSearch;
    $params[] = $likeSearch;
}

if ($filter_priority !== '') {
    $sql .= " AND priority = ?";
    $params[] = $filter_priority;
}

$sql .= " ORDER BY FIELD(priority, 'alta', 'media', 'baja'), created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

function mostrarPrioridad($prio) {
    switch ($prio) {
        case 'alta': return 'Alta';
        case 'media': return 'Media';
        case 'baja': return 'Baja';
        default: return 'Sin prioridad';
    }
}
?>

<link rel="stylesheet" href="../public/dashboard.css">

<div class="dashboard-container">
    <div class="dashboard-header">
        <h2 class="dashboard-title">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> 👋</h2>
        <a href="../actions/logout.php" class="btn logout-btn">Cerrar sesión</a>
    </div>

    <!-- Crear nueva tarea -->
    <section class="card">
        <h3 class="section-title">Crear nueva tarea</h3>
        <form action="../actions/add_task.php" method="POST" class="form-grid">
            <input type="text" name="title" placeholder="Título de la tarea" required>
            <textarea name="description" placeholder="Descripción (opcional)"></textarea>

            <div class="form-inline">
                <label for="priority">Prioridad:</label>
                <select name="priority" id="priority" required>
                    <option value="baja">Baja</option>
                    <option value="media" selected>Media</option>
                    <option value="alta">Alta</option>
                </select>
            </div>

            <div class="form-inline">
                <label for="due_date">Fecha límite:</label>
                <input type="date" name="due_date" id="due_date">
            </div>

            <button type="submit" class="btn primary-btn">Agregar tarea</button>
        </form>
    </section>

    <!-- Búsqueda y filtro -->
    <section class="card">
        <h3 class="section-title">Buscar y filtrar</h3>
        <form method="GET" action="dashboard.php" class="form-grid">
            <input type="text" name="search" placeholder="Buscar tareas..." value="<?php echo htmlspecialchars($search); ?>">

            <div class="form-inline">
                <label for="priority_filter">Prioridad:</label>
                <select name="priority" id="priority_filter">
                    <option value="" <?php if ($filter_priority === '') echo 'selected'; ?>>Todas</option>
                    <option value="alta" <?php if ($filter_priority === 'alta') echo 'selected'; ?>>Alta</option>
                    <option value="media" <?php if ($filter_priority === 'media') echo 'selected'; ?>>Media</option>
                    <option value="baja" <?php if ($filter_priority === 'baja') echo 'selected'; ?>>Baja</option>
                </select>
            </div>

            <button type="submit" class="btn secondary-btn">Buscar</button>
            <?php if ($search !== '' || $filter_priority !== ''): ?>
                <a href="dashboard.php" class="btn clear-btn">Mostrar todas</a>
            <?php endif; ?>
        </form>
    </section>

    <!-- Tareas pendientes -->
    <section class="card">
        <h3 class="section-title">Tareas pendientes</h3>
        <?php
        $hayPendientes = false;
        foreach ($tareas as $tarea) {
            if ($tarea['is_completed'] == 0) {
                $hayPendientes = true;
                echo "<div class='task-item'>
                        <div class='task-header'>
                            <strong>" . htmlspecialchars($tarea['title']) . "</strong>
                            <span class='priority badge-" . $tarea['priority'] . "'>" . mostrarPrioridad($tarea['priority']) . "</span>
                        </div>
                        " . (!empty($tarea['due_date']) ? "<small class='task-date'>📅 " . htmlspecialchars($tarea['due_date']) . "</small><br>" : "") . "
                        <p class='task-desc'>" . htmlspecialchars($tarea['description']) . "</p>

                        <div class='task-actions'>
                            <form action='../actions/complete_task.php' method='POST'>
                                <input type='hidden' name='task_id' value='" . $tarea['id'] . "'>
                                <button type='submit' class='btn complete-btn'>✓ Completada</button>
                            </form>

                            <form action='../views/edit_task.php' method='GET'>
                                <input type='hidden' name='id' value='" . $tarea['id'] . "'>
                                <button type='submit' class='btn edit-btn'>✎ Editar</button>
                            </form>

                            <form action='../actions/delete_task.php' method='POST' onsubmit=\"return confirm('¿Seguro que deseas eliminar esta tarea?')\">
                                <input type='hidden' name='task_id' value='" . $tarea['id'] . "'>
                                <button type='submit' class='btn delete-btn'>🗑 Eliminar</button>
                            </form>
                        </div>
                      </div>";
            }
        }
        if (!$hayPendientes) {
            echo "<p class='no-tasks'>No tienes tareas pendientes.</p>";
        }
        ?>
    </section>

    <!-- Tareas completadas -->
    <section class="card">
        <h3 class="section-title">Tareas completadas</h3>
        <?php
        $hayCompletadas = false;
        foreach ($tareas as $tarea) {
            if ($tarea['is_completed'] == 1) {
                $hayCompletadas = true;
                echo "<div class='task-item completed'>
                        <div class='task-header'>
                            <strong>" . htmlspecialchars($tarea['title']) . "</strong>
                            <span class='priority badge-" . $tarea['priority'] . "'>" . mostrarPrioridad($tarea['priority']) . "</span>
                        </div>
                        " . (!empty($tarea['due_date']) ? "<small class='task-date'>📅 " . htmlspecialchars($tarea['due_date']) . "</small><br>" : "") . "
                        <p class='task-desc'>" . htmlspecialchars($tarea['description']) . "</p>
                        <em class='completed-label'>✔ Completada</em>

                        <div class='task-actions'>
                            <form action='../views/edit_task.php' method='GET'>
                                <input type='hidden' name='id' value='" . $tarea['id'] . "'>
                                <button type='submit' class='btn edit-btn'>✎ Editar</button>
                            </form>

                            <form action='../actions/delete_task.php' method='POST' onsubmit=\"return confirm('¿Seguro que deseas eliminar esta tarea?')\">
                                <input type='hidden' name='task_id' value='" . $tarea['id'] . "'>
                                <button type='submit' class='btn delete-btn'>🗑 Eliminar</button>
                            </form>
                        </div>
                      </div>";
            }
        }
        if (!$hayCompletadas) {
            echo "<p class='no-tasks'>Aún no has completado tareas.</p>";
        }
        ?>
    </section>

    <!-- Exportar -->
    <div class="export-link">
        <a href="../actions/export_tasks_csv.php" target="_blank">📥 Exportar tareas a CSV</a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
