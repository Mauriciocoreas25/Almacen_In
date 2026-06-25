<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php'); exit();
}
if ($_SESSION['id_rol'] != 1) {
    header('Location: dashboard.php'); exit();
}

// Cargar datos para la tabla de usuarios
require_once __DIR__ . '/../Capa_Negocio/UsuarioNegocio.php';
$usuarioNegocio  = new UsuarioNegocio();
$listaUsuarios  = $usuarioNegocio->listarUsuarios()  ?? [];

// Mensajes flash
$flashOk  = $_SESSION['flash_ok']  ?? ''; unset($_SESSION['flash_ok']);
$flashErr = $_SESSION['flash_err'] ?? ''; unset($_SESSION['flash_err']);

// Cargar roles disponibles para el modal
require_once __DIR__ . '/../Capa_Negocio/RolNegocio.php';
$rolNegocio = new RolNegocio();
$listaRoles = $rolNegocio->listarTodo() ?? [];

$rolNombres = [];
foreach ($listaRoles as $r) {
    $rolNombres[$r->getIdRol()] = $r->getNombreRol();
}

$pageTitle  = 'Usuarios';
$activePage = 'usuarios';
require_once __DIR__ . '/includes/header.php';
?>
<body>
<div class="app-shell">

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

<div class="main-content">
    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="hamburger" id="hamburgerBtn">
                <span></span><span></span><span></span>
            </button>
            <div>
                <div class="topbar-title">Gestión de Usuarios</div>
                <div class="topbar-subtitle">Usuarios con acceso al sistema</div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-date">
                <i class="fa-regular fa-calendar" style="margin-right:6px;color:var(--accent);"></i>
                <?php echo date('d M Y'); ?>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">

        <!-- Flash alerts -->
        <?php if ($flashOk): ?>
        <div class="alert alert-success mb-16">
            <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($flashOk); ?>
        </div>
        <?php endif; ?>
        <?php if ($flashErr): ?>
        <div class="alert alert-danger mb-16">
            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($flashErr); ?>
        </div>
        <?php endif; ?>

        <!-- Tabla de Usuarios -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fa-solid fa-users" style="color:var(--accent);"></i>
                    Listado de Usuarios
                </h2>
                <button class="btn btn-primary btn-sm" onclick="openModal('modalAddUser')">
                    Agregar Usuario
                </button>
            </div>
            <div class="table-wrapper">
                <table class="nx-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Usuario / Cuenta</th>
                            <th>Nombre Completo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th style="text-align:center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($listaUsuarios)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">👤</div>
                                    <p>No hay usuarios registrados en el sistema.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaUsuarios as $user): ?>
                        <tr>
                            <td class="fw"><?php echo $user->getIdUsuario(); ?></td>
                            <td>
                                <span class="badge badge-accent">
                                    <?php echo htmlspecialchars($user->getUsername()); ?>
                                </span>
                            </td>
                            <td class="fw"><?php echo htmlspecialchars($user->getNombreComplete()); ?></td>
                            <td>
                                <?php 
                                $rolName = $rolNombres[$user->getIdRol()] ?? 'Desconocido';
                                $badgeClass = 'badge-neutral';
                                if ($user->getIdRol() == 1) {
                                    $badgeClass = 'badge-info';
                                } elseif ($user->getIdRol() == 3) {
                                    $badgeClass = 'badge-accent';
                                }
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($rolName); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user->getEstado()): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <button class="btn btn-warning btn-sm"
                                        title="Editar usuario"
                                        onclick="openEditUser(<?php echo $user->getIdUsuario(); ?>, '<?php echo htmlspecialchars($user->getNombreComplete(), ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user->getUsername(), ENT_QUOTES); ?>', <?php echo $user->getIdRol(); ?>)">
                                    Editar
                                </button>
                                <a href="/Almacen_In/Capa_Presentacion/Controladores/UsuarioController.php?accion=cambiar_estado&id=<?php echo $user->getIdUsuario(); ?>"
                                   class="btn <?php echo $user->getEstado() ? 'btn-danger' : 'btn-success'; ?> btn-sm"
                                   title="<?php echo $user->getEstado() ? 'Desactivar' : 'Activar'; ?>"
                                   onclick="return confirm('¿Confirmar cambio de estado?')">
                                    <?php echo $user->getEstado() ? 'Desactivar' : 'Activar'; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div><!-- /.main-content -->
</div><!-- /.app-shell -->

<!-- ═══════════ MODAL: Agregar Usuario ═══════════ -->
<div class="modal-overlay" id="modalAddUser">
    <div class="modal-box">
        <div class="modal-header">
            <h4>Registrar Nuevo Usuario</h4>
            <button class="modal-close" onclick="closeModal('modalAddUser')">✕</button>
        </div>
        <form action="/Almacen_In/Capa_Presentacion/Controladores/UsuarioController.php?accion=guardar" method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="nombre_complete" class="form-control" placeholder="Ej: Juan Pérez" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Usuario / Cuenta de acceso</label>
                    <input type="text" name="username" class="form-control" placeholder="Ej: jperez" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6">
                </div>
                <div class="form-group">
                    <label class="form-label">Rol del Usuario</label>
                    <select name="id_rol" class="form-select" required>
                        <option value="" disabled selected>Seleccione un rol...</option>
                        <?php foreach ($listaRoles as $r): ?>
                            <option value="<?php echo $r->getIdRol(); ?>"><?php echo htmlspecialchars($r->getNombreRol()); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddUser')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Usuario</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════ MODAL: Editar Usuario ═══════════ -->
<div class="modal-overlay" id="modalEditUser">
    <div class="modal-box">
        <div class="modal-header">
            <h4>Editar Usuario</h4>
            <button class="modal-close" onclick="closeModal('modalEditUser')">✕</button>
        </div>
        <form action="/Almacen_In/Capa_Presentacion/Controladores/UsuarioController.php?accion=editar" method="POST">
            <input type="hidden" name="id_usuario" id="editUserId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="nombre_complete" id="editNombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Usuario / Cuenta</label>
                    <input type="text" name="username" id="editUsername" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nueva Contraseña <span style="color:var(--text-muted);font-weight:400;">(dejar vacío para no cambiar)</span></label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" minlength="6">
                </div>
                <div class="form-group">
                    <label class="form-label">Rol</label>
                    <select name="id_rol" id="editRol" class="form-select" required>
                        <?php foreach ($listaRoles as $r): ?>
                            <option value="<?php echo $r->getIdRol(); ?>"><?php echo htmlspecialchars($r->getNombreRol()); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEditUser')">Cancelar</button>
                <button type="submit" class="btn btn-warning">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast container -->
<div id="toast-container"></div>

<script>
// ── Modal helpers ────────────────────────────────
function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

// Close on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
        if (e.target === overlay) closeModal(overlay.id);
    });
});

// Open edit modal with pre-filled data
function openEditUser(id, nombre, username, rolId) {
    document.getElementById('editUserId').value   = id;
    document.getElementById('editNombre').value   = nombre;
    document.getElementById('editUsername').value = username;
    document.getElementById('editRol').value      = rolId;
    openModal('modalEditUser');
}

// Auto-dismiss alerts
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity 0.5s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    }, 5000);
});
</script>
</body>
</html>
