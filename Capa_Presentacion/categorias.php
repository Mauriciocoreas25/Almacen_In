<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) { header('Location: login.php'); exit(); }
if (!in_array($_SESSION['id_rol'], [1, 3])) { header('Location: dashboard.php'); exit(); }

require_once __DIR__ . '/../Capa_Negocio/CategoriaNegocio.php';
$categoriaNegocio = new CategoriaNegocio();
$listaCategorias  = $categoriaNegocio->listarCategorias() ?? [];

$flashOk  = $_SESSION['flash_ok']  ?? ''; unset($_SESSION['flash_ok']);
$flashErr = $_SESSION['flash_err'] ?? ''; unset($_SESSION['flash_err']);

$pageTitle  = 'Categorías';
$activePage = 'categorias';
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
                <div class="topbar-title">Categorías</div>
                <div class="topbar-subtitle">Gestión de categorías de productos</div>
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
                <h1><i class="fa-solid fa-tags" style="color:var(--accent);margin-right:8px;"></i>Gestión de Categorías</h1>
                <p><?php echo count($listaCategorias); ?> categoría(s) registradas en el sistema</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('modalAddCat')">
                <i class="fa-solid fa-plus"></i> Nueva Categoría
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-list" style="color:var(--accent);"></i> Listado de Categorías</h2>
                <div class="search-bar">
                    <div class="input-group">
                        <i class="fa-solid fa-search input-icon"></i>
                        <input type="text" id="searchCat" class="form-control" placeholder="Buscar categoría..." oninput="filterTable(this.value)">
                    </div>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="nx-table" id="catTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre de Categoría</th>
                            <th>Descripción</th>
                            <th style="text-align:center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="catTableBody">
                    <?php if (empty($listaCategorias)): ?>
                        <tr id="emptyRow">
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="empty-icon">🏷️</div>
                                    <p>No hay categorías registradas. Crea la primera.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaCategorias as $cat): ?>
                        <tr>
                            <td class="fw"><?php echo $cat->getIdCategoria(); ?></td>
                            <td>
                                <span style="display:flex;align-items:center;gap:8px;">
                                    <span style="width:8px;height:8px;border-radius:50%;background:var(--accent);flex-shrink:0;"></span>
                                    <span class="fw"><?php echo htmlspecialchars($cat->getNombreCategoria()); ?></span>
                                </span>
                            </td>
                            <td class="text-muted"><?php echo htmlspecialchars($cat->getDescripcion() ?? '—'); ?></td>
                            <td style="text-align:center;">
                                <button class="btn btn-warning btn-sm"
                                        title="Editar"
                                        onclick="openEditCat(<?php echo $cat->getIdCategoria(); ?>, '<?php echo htmlspecialchars($cat->getNombreCategoria(), ENT_QUOTES); ?>', '<?php echo htmlspecialchars($cat->getDescripcion() ?? '', ENT_QUOTES); ?>')">
                                    Editar
                                </button>
                                <a href="/Almacen_In/Capa_Presentacion/Controladores/CategoriaController.php?accion=eliminar&id=<?php echo $cat->getIdCategoria(); ?>"
                                   class="btn btn-danger btn-sm"
                                   title="Eliminar"
                                   onclick="return confirm('¿Eliminar esta categoría? Esto puede afectar productos asociados.')">
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

<!-- MODAL: Nueva Categoría -->
<div class="modal-overlay" id="modalAddCat">
    <div class="modal-box">
        <div class="modal-header">
            <h4><i class="fa-solid fa-tag" style="color:var(--accent);"></i> Nueva Categoría</h4>
            <button class="modal-close" onclick="closeModal('modalAddCat')">✕</button>
        </div>
        <form action="/Almacen_In/Capa_Presentacion/Controladores/CategoriaController.php?accion=guardar" method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nombre de la Categoría *</label>
                    <input type="text" name="nombre_categoria" class="form-control" placeholder="Ej: Electrónica, Ropa..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción <span style="color:var(--text-muted);font-weight:400;">(opcional)</span></label>
                    <input type="text" name="descripcion" class="form-control" placeholder="Breve descripción...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddCat')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Editar Categoría -->
<div class="modal-overlay" id="modalEditCat">
    <div class="modal-box">
        <div class="modal-header">
            <h4><i class="fa-solid fa-pen-to-square" style="color:var(--warning);"></i> Editar Categoría</h4>
            <button class="modal-close" onclick="closeModal('modalEditCat')">✕</button>
        </div>
        <form action="/Almacen_In/Capa_Presentacion/Controladores/CategoriaController.php?accion=editar" method="POST">
            <input type="hidden" name="id_categoria" id="editCatId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nombre de la Categoría *</label>
                    <input type="text" name="nombre_categoria" id="editCatNombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <input type="text" name="descripcion" id="editCatDesc" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEditCat')">Cancelar</button>
                <button type="submit" class="btn btn-warning"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open');    document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }
document.querySelectorAll('.modal-overlay').forEach(o => o.addEventListener('click', e => { if(e.target===o) closeModal(o.id); }));

function openEditCat(id, nombre, desc) {
    document.getElementById('editCatId').value     = id;
    document.getElementById('editCatNombre').value = nombre;
    document.getElementById('editCatDesc').value   = desc;
    openModal('modalEditCat');
}

function filterTable(q) {
    const rows = document.querySelectorAll('#catTableBody tr');
    q = q.toLowerCase();
    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}

document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => { el.style.transition='opacity 0.5s'; el.style.opacity='0'; setTimeout(()=>el.remove(),500); }, 5000);
});
</script>
</body>
</html>
