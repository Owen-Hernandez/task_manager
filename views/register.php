<?php include('../includes/header.php'); ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm" style="width: 100%; max-width: 450px;">
        <div class="card-body p-4 p-md-5 position-relative">
            <!-- Interruptor modo nocturno (misma posición que en login) -->
            <div class="form-check form-switch position-absolute" style="top: 1rem; right: 1rem;">
                <input class="form-check-input" type="checkbox" id="darkModeToggle">
                <label class="form-check-label" for="darkModeToggle" title="Modo nocturno">
                    <i class="bi bi-moon-stars"></i>
                </label>
            </div>

            <!-- Encabezado -->
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">Registro de Usuario</h2>
                <p class="text-muted">Crea una cuenta para comenzar</p>
            </div>

            <!-- Formulario -->
            <form action="../actions/register_action.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Nombre de usuario</label>
                    <input type="text" class="form-control" id="username" name="username" required 
                           placeholder="Crea tu nombre de usuario">
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required 
                           placeholder="Crea una contraseña segura">
                    <div id="passwordStrength" class="form-text"></div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirmar contraseña</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required 
                           placeholder="Repite tu contraseña">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Registrarse</button>
            </form>

            <!-- Pie de página -->
            <div class="text-center mt-3">
                <p class="text-muted">¿Ya tienes una cuenta? <a href="login.php" class="text-decoration-none">Iniciar sesión</a></p>
            </div>
        </div>
    </div>
</div>

<script>
// Validación de fortaleza de contraseña en tiempo real
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBadge = document.getElementById('passwordStrength');
    let strength = 0;
    
    // Validaciones
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    // Mostrar resultado
    const strengthText = ['Muy débil', 'Débil', 'Moderada', 'Fuerte', 'Muy fuerte'][strength];
    const strengthClass = ['text-danger', 'text-danger', 'text-warning', 'text-success', 'text-success'][strength];
    
    strengthBadge.className = 'form-text ' + strengthClass;
    strengthBadge.textContent = 'Fortaleza: ' + strengthText;
});
</script>

<?php include('../includes/footer.php'); ?>