<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) { header('Location: login.php'); exit(); }

require_once __DIR__ . '/../Capa_Negocio/ClienteNegocio.php';
$clienteNegocio = new ClienteNegocio();
$listaClientes  = $clienteNegocio->listarClientes() ?? [];

$flashOk  = $_SESSION['flash_ok']  ?? ''; unset($_SESSION['flash_ok']);
$flashErr = $_SESSION['flash_err'] ?? ''; unset($_SESSION['flash_err']);

$pageTitle  = 'Clientes';
$activePage = 'clientes';
require_once __DIR__ . '/includes/header.php';
?>
<body>
<div class="app-shell">
<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

<div class="main-content">
    <header class="topbar">
        <div class="topbar-left">
            <button class="hamburger" id="hamburgerBtn"><span></span><span></span><span></span></button>
            <div>
                <div class="topbar-title">Clientes</div>
                <div class="topbar-subtitle"><?php echo count($listaClientes); ?> cliente(s) registrados</div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-date"><i class="fa-regular fa-calendar" style="margin-right:6px;color:var(--accent);"></i><?php echo date('d M Y'); ?></div>
        </div>
    </header>

    <main class="page-content">
        <?php if ($flashOk): ?>
        <div class="alert alert-success mb-16"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($flashOk); ?></div>
        <?php endif; ?>
        <?php if ($flashErr): ?>
        <div class="alert alert-danger mb-16"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($flashErr); ?></div>
        <?php endif; ?>

        <div class="page-header">
            <div class="page-header-left">
                <h1><i class="fa-solid fa-address-book" style="color:var(--accent);margin-right:8px;"></i>Gestión de Clientes</h1>
                <p>Registro, edición y búsqueda de clientes</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('modalAddCliente')">
                <i class="fa-solid fa-user-plus"></i> Nuevo Cliente
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-users" style="color:var(--accent);"></i> Directorio de Clientes</h2>
                <div class="search-bar">
                    <div class="input-group">
                        <i class="fa-solid fa-search input-icon"></i>
                        <input type="text" id="searchCliente" class="form-control" placeholder="Buscar por nombre, teléfono..." oninput="filterClientes(this.value)">
                    </div>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="nx-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre Completo</th>
                            <th>DUI</th>
                            <th>NIT</th>
                            <th>Tipo Persona</th>
                            <th>Teléfono</th>
                            <th>Correo Electrónico</th>
                            <th>Dirección</th>
                            <th style="text-align:center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="clienteTableBody">
                    <?php if (empty($listaClientes)): ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-icon">👥</div>
                                    <p>No hay clientes registrados. Agrega el primero.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaClientes as $cli): ?>
                        <tr>
                            <td class="fw"><?php echo $cli->getIdCliente(); ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent-light));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
                                        <?php echo strtoupper(substr($cli->getNombreCompleto(),0,1)); ?>
                                    </div>
                                    <span class="fw"><?php echo htmlspecialchars($cli->getNombreCompleto()); ?></span>
                                </div>
                            </td>
                            <td><span class="badge badge-neutral"><?php echo htmlspecialchars($cli->getDui() ?: '—'); ?></span></td>
                            <td><span class="badge badge-neutral"><?php echo htmlspecialchars($cli->getNit() ?: '—'); ?></span></td>
                            <td>
                                <?php if ($cli->getPersonalidadJuridica()): ?>
                                    <span class="badge badge-info">Jurídica</span>
                                <?php else: ?>
                                    <span class="badge badge-accent">Natural</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="display:flex;align-items:center;gap:6px;color:var(--text-secondary);">
                                    <i class="fa-solid fa-phone" style="font-size:11px;color:var(--success);"></i>
                                    <?php echo htmlspecialchars($cli->getTelefono()); ?>
                                </span>
                            </td>
                            <td style="color:var(--text-muted);"><?php echo htmlspecialchars($cli->getCorreo() ?: '—'); ?></td>
                            <td style="color:var(--text-muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($cli->getDireccion() ?: '—'); ?></td>
                            <td style="text-align:center;">
                                <button class="btn btn-warning btn-sm" title="Editar"
                                    onclick="openEditCliente(<?php echo $cli->getIdCliente(); ?>, '<?php echo htmlspecialchars($cli->getNombreCompleto(),ENT_QUOTES); ?>', '<?php echo htmlspecialchars($cli->getTelefono(),ENT_QUOTES); ?>', '<?php echo htmlspecialchars($cli->getCorreo(),ENT_QUOTES); ?>', '<?php echo htmlspecialchars($cli->getDireccion(),ENT_QUOTES); ?>', '<?php echo htmlspecialchars($cli->getDui() ?? '',ENT_QUOTES); ?>', '<?php echo htmlspecialchars($cli->getNit() ?? '',ENT_QUOTES); ?>', <?php echo (int)$cli->getPersonalidadJuridica(); ?>)">
                                    Editar
                                </button>
                                <a href="/Almacen_In/Capa_Presentacion/Controladores/ClienteController.php?accion=eliminar&id=<?php echo $cli->getIdCliente(); ?>"
                                   class="btn btn-danger btn-sm" title="Eliminar"
                                   onclick="return confirm('¿Eliminar a este cliente? Esta acción es permanente.')">
                                    Eliminar
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
</div>
</div>

<!-- MODAL: Nuevo Cliente -->
<div class="modal-overlay" id="modalAddCliente">
    <div class="modal-box">
        <div class="modal-header">
            <h4><i class="fa-solid fa-user-plus" style="color:var(--accent);"></i> Registrar Nuevo Cliente</h4>
            <button class="modal-close" onclick="closeModal('modalAddCliente')">✕</button>
        </div>
        <form action="/Almacen_In/Capa_Presentacion/Controladores/ClienteController.php?accion=guardar" method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nombre Completo *</label>
                    <input type="text" name="nombre_completo" class="form-control" placeholder="Ej: María González" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Teléfono *</label>
                        <input type="text" name="telefono" class="form-control" placeholder="Ej: 7777-0000" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" placeholder="Ej: cliente@mail.com">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">DUI</label>
                        <input type="text" name="dui" class="form-control" placeholder="Ej: 00000000-0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIT</label>
                        <input type="text" name="nit" class="form-control" placeholder="Ej: 0000-000000-000-0">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Personalidad Jurídica</label>
                    <select name="personalidad_juridica" class="form-select">
                        <option value="0" selected>Persona Natural</option>
                        <option value="1">Persona Jurídica (Empresa/Sociedad)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" placeholder="Ej: Col. Escalón, San Salvador">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddCliente')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cliente</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Editar Cliente -->
<div class="modal-overlay" id="modalEditCliente">
    <div class="modal-box">
        <div class="modal-header">
            <h4><i class="fa-solid fa-user-pen" style="color:var(--warning);"></i> Editar Cliente</h4>
            <button class="modal-close" onclick="closeModal('modalEditCliente')">✕</button>
        </div>
        <form action="/Almacen_In/Capa_Presentacion/Controladores/ClienteController.php?accion=editar" method="POST">
            <input type="hidden" name="id_cliente" id="editClienteId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nombre Completo *</label>
                    <input type="text" name="nombre_completo" id="editClienteNombre" class="form-control" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Teléfono *</label>
                        <input type="text" name="telefono" id="editClienteTel" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" id="editClienteCorreo" class="form-control">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">DUI</label>
                        <input type="text" name="dui" id="editClienteDui" class="form-control" placeholder="Ej: 00000000-0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIT</label>
                        <input type="text" name="nit" id="editClienteNit" class="form-control" placeholder="Ej: 0000-000000-000-0">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Personalidad Jurídica</label>
                    <select name="personalidad_juridica" id="editClienteJuridica" class="form-select">
                        <option value="0">Persona Natural</option>
                        <option value="1">Persona Jurídica (Empresa/Sociedad)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" id="editClienteDir" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEditCliente')">Cancelar</button>
                <button type="submit" class="btn btn-warning"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open');    document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
document.querySelectorAll('.modal-overlay').forEach(o => o.addEventListener('click', e => { if(e.target===o) closeModal(o.id); }));

function openEditCliente(id, nombre, tel, correo, dir, dui, nit, juridica) {
    document.getElementById('editClienteId').value     = id;
    document.getElementById('editClienteNombre').value = nombre;
    document.getElementById('editClienteTel').value    = tel;
    document.getElementById('editClienteCorreo').value = correo;
    document.getElementById('editClienteDir').value    = dir;
    document.getElementById('editClienteDui').value    = dui;
    document.getElementById('editClienteNit').value    = nit;
    document.getElementById('editClienteJuridica').value = juridica;
    openModal('modalEditCliente');
}

function filterClientes(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#clienteTableBody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}

document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => { el.style.transition='opacity 0.5s'; el.style.opacity='0'; setTimeout(()=>el.remove(),500); }, 5000);
});
</script>
</body>
</html>
