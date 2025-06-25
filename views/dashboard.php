<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once('../config/db.php');
include('../includes/header.php');

// Inicializar variables
$search = '';
$filter_priority = '';
$tareas = []; // Inicializar como array vacío

// Procesar búsqueda
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Procesar filtro de prioridad
if (isset($_GET['priority']) && in_array($_GET['priority'], ['alta', 'media', 'baja'])) {
    $filter_priority = $_GET['priority'];
}

try {
    $sql = "SELECT * FROM tasks WHERE user_id = ?";
    $params = [$_SESSION['usuario_id']];

    if ($search !== '') {
        $sql .= " AND (title LIKE ? OR description LIKE ?)";
        $likeSearch = "%$search%";
        array_push($params, $likeSearch, $likeSearch);
    }

    if ($filter_priority !== '') {
        $sql .= " AND priority = ?";
        array_push($params, $filter_priority);
    }

    $sql .= " ORDER BY FIELD(priority, 'alta', 'media', 'baja'), created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Manejo de errores de base de datos
    error_log("Error en la consulta: " . $e->getMessage());
    $tareas = []; // Asegurarse que $tareas es un array
}

function mostrarPrioridad($prio) {
    switch ($prio) {
        case 'alta': return 'Alta';
        case 'media': return 'Media';
        case 'baja': return 'Baja';
        default: return 'Sin prioridad';
    }
}
?>

<div class="dashboard-container">
    <!-- Header Mejorado -->
    <div class="dashboard-header bg-primary text-white p-4 rounded-bottom shadow">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">👋 Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h1>
                <p class="mb-0 small opacity-75"><?php echo date('l, j F Y'); ?></p>
            </div>
            <a href="../actions/logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </a>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-start-0 border-end-0 border-top-0 border-bottom-3 border-primary">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Tareas Totales</h6>
                    <h3 class="card-title"><?php echo count($tareas); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Pendientes</h6>
                    <h3 class="card-title text-warning"><?php echo count(array_filter($tareas, fn($t) => $t['is_completed'] == 0)); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Completadas</h6>
                    <h3 class="card-title text-success"><?php echo count(array_filter($tareas, fn($t) => $t['is_completed'] == 1)); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Acciones -->
    <div class="d-flex justify-content-between mb-4">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTaskModal">
            <i class="bi bi-plus-lg"></i> Nueva Tarea
        </button>
        
        <form method="GET" action="dashboard.php" class="d-flex gap-2">
            <div class="input-group" style="width: 300px;">
                <input type="text" name="search" class="form-control" placeholder="Buscar tareas..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <select name="priority" class="form-select" style="width: 150px;">
                <option value="">Todas</option>
                <option value="alta" <?= $filter_priority === 'alta' ? 'selected' : '' ?>>Alta</option>
                <option value="media" <?= $filter_priority === 'media' ? 'selected' : '' ?>>Media</option>
                <option value="baja" <?= $filter_priority === 'baja' ? 'selected' : '' ?>>Baja</option>
            </select>
        </form>
    </div>

    <!-- Lista de Tareas -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gray border-bottom-0">
                    <h5 class="mb-0">📝 Tareas Pendientes</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count(array_filter($tareas, fn($t) => $t['is_completed'] == 0)) === 0): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-check2-circle display-6"></i>
                            <p class="mt-2 mb-0">¡No tienes tareas pendientes!</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($tareas as $tarea): ?>
                                <?php if ($tarea['is_completed'] == 0): ?>
                                    <div class="list-group-item list-group-item-action task-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <form action="../actions/complete_task.php" method="POST" class="mb-0">
                                                        <input type="hidden" name="task_id" value="<?= $tarea['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-success p-1">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    </form>
                                                    <h6 class="mb-1"><?= htmlspecialchars($tarea['title']) ?></h6>
                                                </div>
                                                <?php if (!empty($tarea['description'])): ?>
                                                    <p class="mb-1 small text-muted"><?= htmlspecialchars($tarea['description']) ?></p>
                                                <?php endif; ?>
                                                <div class="d-flex gap-2 mt-2">
                                                    <span class="badge bg-<?= 
                                                        $tarea['priority'] === 'alta' ? 'danger' : 
                                                        ($tarea['priority'] === 'media' ? 'warning' : 'secondary')
                                                    ?>">
                                                        <?= mostrarPrioridad($tarea['priority']) ?>
                                                    </span>
                                                    <?php if (!empty($tarea['due_date'])): ?>
                                                        <span class="badge bg-light text-dark">
                                                            <i class="bi bi-calendar"></i> <?= $tarea['due_date'] ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm p-1 btn-dark" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu bg-gray">
                                                    <li>
                                                        <a class="dropdown-item" href="../views/edit_task.php?id=<?= $tarea['id'] ?>">
                                                            <i class="bi bi-pencil"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="../actions/delete_task.php" method="POST">
                                                            <input type="hidden" name="task_id" value="<?= $tarea['id'] ?>">
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('¿Eliminar esta tarea?')">
                                                                <i class="bi bi-trash"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-gray border-bottom-0">
                    <h5 class="mb-0">✅ Tareas Completadas</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count(array_filter($tareas, fn($t) => $t['is_completed'] == 1)) === 0): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-emoji-frown display-6"></i>
                            <p class="mt-2 mb-0">Aún no completas tareas</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($tareas as $tarea): ?>
                                <?php if ($tarea['is_completed'] == 1): ?>
                                    <div class="list-group-item list-group-item-light task-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 text-decoration-line-through"><?= htmlspecialchars($tarea['title']) ?></h6>
                                                <?php if (!empty($tarea['description'])): ?>
                                                    <p class="mb-1 small text-muted"><?= htmlspecialchars($tarea['description']) ?></p>
                                                <?php endif; ?>
                                                <div class="d-flex gap-2 mt-2">
                                                    <span class="badge bg-<?= 
                                                        $tarea['priority'] === 'alta' ? 'danger' : 
                                                        ($tarea['priority'] === 'media' ? 'warning' : 'secondary')
                                                    ?>">
                                                        <?= mostrarPrioridad($tarea['priority']) ?>
                                                    </span>
                                                    <?php if (!empty($tarea['due_date'])): ?>
                                                        <span class="badge bg-light text-dark">
                                                            <i class="bi bi-calendar"></i> <?= $tarea['due_date'] ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm p-1 btn-dark" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu bg-gray">
                                                    <li>
                                                        <a class="dropdown-item" href="../views/edit_task.php?id=<?= $tarea['id'] ?>">
                                                            <i class="bi bi-pencil"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="../actions/delete_task.php" method="POST">
                                                            <input type="hidden" name="task_id" value="<?= $tarea['id'] ?>">
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('¿Eliminar esta tarea?')">
                                                                <i class="bi bi-trash"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nueva Tarea -->
    <div class="modal fade" id="newTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">➕ Nueva Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../actions/add_task.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="f orm-label">Título</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción (opcional)</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prioridad</label>
                                <select name="priority" class="form-select" required>
                                    <option value="baja">Baja</option>
                                    <option value="media" selected>Media</option>
                                    <option value="alta">Alta</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha límite</label>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Tarea</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>