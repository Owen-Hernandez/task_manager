<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title>Gestor de Tareas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Tu CSS personalizado -->
    <link rel="stylesheet" href="../public/styles.css">
    
    <!-- Script para modo oscuro mejorado -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            
            // 1. Detectar preferencias (localStorage > sistema)
            const savedMode = localStorage.getItem('darkMode');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const darkMode = savedMode !== null ? savedMode === 'true' : systemPrefersDark;

            // 2. Aplicar estado inicial
            if (darkMode) {
                document.documentElement.classList.add('dark-mode');
                if (darkModeToggle) darkModeToggle.checked = true;
            }

            // 3. Controlador del interruptor
            if (darkModeToggle) {
                darkModeToggle.addEventListener('change', function() {
                    document.documentElement.classList.toggle('dark-mode');
                    localStorage.setItem('darkMode', this.checked);
                    
                    // Forzar repintado para navegadores con renderizado optimizado
                    document.body.style.display = 'none';
                    document.body.offsetHeight; // Trigger reflow
                    document.body.style.display = '';
                });
            }

            // 4. Aplicar transición suave después de carga
            setTimeout(() => {
                document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
            }, 100);
        });
    </script>
</head>
<body class="bg-light">