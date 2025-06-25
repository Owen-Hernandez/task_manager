<?php
include('../includes/header.php');
?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm" style="width: 100%; max-width: 450px;">
        <div class="card-body p-4 p-md-5 position-relative">
            <!-- Interruptor modo nocturno (esquina superior derecha) -->
            <div class="form-check form-switch position-absolute" style="top: 1rem; right: 1rem;">
                <input class="form-check-input" type="checkbox" id="darkModeToggle">
                <label class="form-check-label" for="darkModeToggle" title="Modo nocturno">
                    <i class="bi bi-moon-stars"></i>
                </label>
            </div>

            <!-- Encabezado -->
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">Iniciar Sesión</h2>
                <p class="text-muted">Ingresa tus credenciales para continuar</p>
            </div>

            <!-- Mensajes -->
            <?php if (isset($_GET['mensaje'])): ?>
                <div class="alert <?= ($_GET['mensaje'] === 'registro_exitoso') ? 'alert-success' : 'alert-danger' ?> mb-4">
                    <?php
                    switch ($_GET['mensaje']) {
                        case 'registro_exitoso':
                            echo "¡Registro exitoso! Ahora puedes iniciar sesión.";
                            break;
                        case 'clave_incorrecta':
                            echo "Contraseña incorrecta. Intenta de nuevo.";
                            break;
                        case 'usuario_no_existe':
                            echo "No se encontró una cuenta con ese usuario.";
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <form action="../actions/login_action.php" method="POST">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required placeholder="Ingresa tu usuario">
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Ingresa tu contraseña">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Iniciar sesión</button>
            </form>

            <!-- Pie de página -->
            <div class="text-center mt-3">
                <p class="text-muted">¿No tienes una cuenta? <a href="register.php" class="text-decoration-none">Regístrate aquí</a></p>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>