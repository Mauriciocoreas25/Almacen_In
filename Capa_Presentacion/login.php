<?php
// Iniciar sesión para verificar si vienen alertas o mensajes de error
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>

<div class="card login-card p-4 bg-white">
    <div class="card-body">
        <h3 class="card-title text-center mb-4 fw-bold text-dark">NEXUS POS</h3>
        <p class="text-muted text-center mb-4">Control de Inventario y Ventas</p>

        <?php if (isset($_SESSION['error_login'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['error_login']; 
                    unset($_SESSION['error_login']); // Limpiar el error para que no se quede fijo
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="controladores/LoginController.php" method="POST">
            <div class="mb-3">
                <label for="usuario" class="form-label text-secondary">Usuario o Cuenta</label>
                <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ej: admin" required autocomplete="username">
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label text-secondary">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary py-2 fw-semibold">Ingresar al Sistema</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>