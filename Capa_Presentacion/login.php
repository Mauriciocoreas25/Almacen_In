<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
// Si ya está logueado, ir al dashboard directamente
if (isset($_SESSION['id_usuario'])) {
    header('Location: /Almacen_In/Capa_Presentacion/dashboard.php');
    exit();
}
$error = '';
if (isset($_SESSION['error_login'])) {
    $error = $_SESSION['error_login'];
    unset($_SESSION['error_login']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Iniciar sesión en el Sistema de Control de Inventario & Ventas">
    <title>Iniciar Sesión — Sistema de Control de Inventario & Ventas</title>
    <link rel="stylesheet" href="/Almacen_In/Capa_Presentacion/assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏪</text></svg>">
</head>
<body>

<!-- Animated background -->
<div class="bg-blobs">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
</div>

<div class="login-wrapper">
    <div class="login-card">

        <!-- Logo & Brand -->
        <div class="login-logo">
            <h1 class="login-title" style="font-size: 20px; line-height: 1.3;">Sistema de Control de Inventario &amp; Ventas</h1>
            <p class="login-subtitle">Acceso al Sistema</p>
        </div>

        <!-- Error message -->
        <?php if (!empty($error)): ?>
        <div class="login-error" id="errorAlert">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="/Almacen_In/Capa_Presentacion/Controladores/LoginController.php" method="POST" id="loginForm">

            <div class="login-input-group">
                <label for="usuario">Usuario o Cuenta</label>
                <div class="input-wrap">
                    <input type="text"
                           id="usuario"
                           name="usuario"
                           placeholder="Ej: admin"
                           required
                           autocomplete="username"
                           autofocus>
                </div>
            </div>

            <div class="login-input-group">
                <label for="password">Contraseña</label>
                <div class="input-wrap">
                    <input type="password"
                           id="password"
                           name="password"
                           placeholder="••••••••"
                           required
                           autocomplete="current-password">
                    <button type="button" class="toggle-pass" id="togglePass" title="Mostrar / Ocultar">
                        [Ver]
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login" id="submitBtn">
                Ingresar al Sistema
            </button>
        </form>

        <div class="login-footer">
            Conexión segura • Sistema de Control de Inventario &amp; Ventas &copy; <?php echo date('Y'); ?>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
const toggleBtn  = document.getElementById('togglePass');
const passInput  = document.getElementById('password');

toggleBtn.addEventListener('click', () => {
    const isPass = passInput.type === 'password';
    passInput.type  = isPass ? 'text' : 'password';
    toggleBtn.textContent = isPass ? '[Ocultar]' : '[Ver]';
});

// Loading state on submit
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = 'Verificando...';
    btn.disabled = true;
});

// Auto-dismiss error after 5s
const err = document.getElementById('errorAlert');
if (err) {
    setTimeout(() => {
        err.style.transition = 'opacity 0.5s';
        err.style.opacity = '0';
        setTimeout(() => err.remove(), 500);
    }, 5000);
}
</script>
</body>
</html>