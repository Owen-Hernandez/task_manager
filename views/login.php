<?php
include('../includes/header.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../public/styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2 class="auth-title">Iniciar Sesión</h2>
            <p class="auth-subtitle">Ingresa tus credenciales para continuar</p>
        </div>

        <!-- Mensajes -->
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="<?= ($_GET['mensaje'] === 'registro_exitoso') ? 'mensaje-exito' : 'mensaje-error' ?>">
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
        <form action="../actions/login_action.php" method="POST" class="auth-form">
            <div class="input-group">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" required placeholder="Ingresa tu usuario">
            </div>
            
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Ingresa tu contraseña">
            </div>

            <button type="submit" class="auth-btn">Iniciar sesión</button>
        </form>

        <div class="auth-footer">
            <p>¿No tienes una cuenta? <a href="register.php" class="auth-link">Regístrate aquí</a></p>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>