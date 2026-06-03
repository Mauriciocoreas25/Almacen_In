<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Control de seguridad
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

// Jalar la capa de negocio para cargar los usuarios reales en la tabla
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Negocio' . DIRECTORY_SEPARATOR . 'UsuarioNegocio.php';
$usuarioNegocio = new UsuarioNegocio();
$listaUsuarios = $usuarioNegocio->listarUsuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TIENDA PAJARITO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"> <style>
        body { min-height: 100vh; overflow-x: hidden; }
        .sidebar { min-width: 250px; max-width: 250px; min-height: 100vh; background-color: #212529; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 12px 20px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background-color: #343a40; border-left: 4px solid #0d6efd; }
    </style>
</head>
<body class="bg-light d-flex">

    <div class="sidebar text-white shadow">
        <div class="p-4 text-center border-bottom border-secondary">
            <h4 class="fw-bold text-primary mb-0">TIENDA PAJARITO</h4>
            <small class="text-muted">Inventario & Ventas</small>
        </div>
        <div class="p-3">
            <p class="text-uppercase text-muted fw-bold small mb-2">Módulos</p>
            <a href="#" class="active"><i class="fa-solid fa-users me-2"></i> Usuarios</a>
            <a href="#"><i class="fa-solid fa-boxes-stacked me-2"></i> Inventario</a>
            <a href="#"><i class="fa-solid fa-cart-shopping me-2"></i> Ventas</a>
            <a href="#"><i class="fa-solid fa-address-book me-2"></i> Clientes</a>
            
            <p class="text-uppercase text-muted fw-bold small mt-4 mb-2">Sistema</p>
            <a href="login.php" class="text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión</a>
        </div>
    </div>

    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h2 class="fw-bold text-dark mb-0">Gestión de Usuarios</h2>
                <small class="text-muted">Bienvenido de nuevo, <?php echo $_SESSION['nombre_usuario']; ?></small>
            </div>
<button class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUsuario">
    <i class="fa-solid fa-user-plus me-2"></i> Agregar Usuario
</button>        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Usuario/Cuenta</th>
                                <th>Nombre Completo</th>
                                <th>Rol ID</th>
                                <th>Estado</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listaUsuarios)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No hay usuarios registrados en el sistema.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listaUsuarios as $user): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?php echo $user->getIdUsuario(); ?></td>
                                        <td><span class="badge bg-light text-dark border fw-semibold"><?php echo $user->getUsername(); ?></span></td>
                                        <td><?php echo $user->getNombreComplete(); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo $user->getIdRol(); ?></span></td>
                                        <td>
                                            <?php if ($user->getEstado()): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <button class="btn btn-sm btn-outline-warning me-1" title="Editar"><i class="fa-solid fa-pen"></i></button>
                                            <button class="btn btn-sm btn-outline-danger" title="Cambiar Estado"><i class="fa-solid fa-toggle-on"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="modalUsuarioLabel">Registrar Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="controladores/UsuarioController.php?accion=guardar" method="POST">
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Nombre Completo</label>
                            <input type="text" name="nombre_complete" class="form-control" placeholder="Ej: Juan Pérez" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Usuario / Cuenta de acceso</label>
                            <input type="text" name="username" class="form-control" placeholder="Ej: jperez" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Rol del Usuario</label>
                            <select name="id_rol" class="form-select" required>
                                <option value="" disabled selected>Seleccione un rol...</option>
                                <option value="1">Administrador</option>
                                <option value="2">Vendedor / Cajero</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>