<?php include('../includes/header.php'); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../public/styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2 class="auth-title">Registro de Usuario</h2>
            <p class="auth-subtitle">Crea una cuenta para comenzar</p>
        </div>

        <form action="../actions/register_action.php" method="POST" class="auth-form">
            <div class="input-group">
                <label for="username">Nombre de usuario</label>
                <input type="text" id="username" name="username" required placeholder="Crea tu nombre de usuario">
            </div>
            
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Crea una contraseña segura">
            </div>

            <button type="submit" class="auth-btn">Registrarse</button>
        </form>

        <div class="auth-footer">
            <p>¿Ya tienes una cuenta? <a href="login.php" class="auth-link">Iniciar sesión</a></p>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>