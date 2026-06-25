<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) { header('Location: login.php'); exit(); }
if (!in_array($_SESSION['id_rol'], [1, 3])) { header('Location: dashboard.php'); exit(); }

require_once __DIR__ . '/../Capa_Negocio/ProductoNegocio.php';
require_once __DIR__ . '/../Capa_Negocio/CategoriaNegocio.php';

$productoNegocio  = new ProductoNegocio();
$categoriaNegocio = new CategoriaNegocio();
$listaProductos   = $productoNegocio->listarProductos()   ?? [];
$listaCategorias  = $categoriaNegocio->listarCategorias() ?? [];

// Mapa categoría ID → nombre para mostrar en tabla
$mapCategorias = [];
foreach ($listaCategorias as $cat) {
    $mapCategorias[$cat->getIdCategoria()] = $cat->getNombreCategoria();
}

$flashOk  = $_SESSION['flash_ok']  ?? ''; unset($_SESSION['flash_ok']);
$flashErr = $_SESSION['flash_err'] ?? ''; unset($_SESSION['flash_err']);

$pageTitle  = 'Productos';
$activePage = 'productos';
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
                <div class="topbar-title">Inventario de Productos</div>
                <div class="topbar-subtitle"><?php echo count($listaProductos); ?> producto(s) en el sistema</div>
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
                <h1><i class="fa-solid fa-boxes-stacked" style="color:var(--accent);margin-right:8px;"></i>Inventario de Productos</h1>
            
            </div>
            <button class="btn btn-primary" onclick="openModal('modalAddProd')">
                <i class="fa-solid fa-plus"></i> Nuevo Producto
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-table-list" style="color:var(--accent);"></i> Listado</h2>
                <div class="search-bar">
                    <div class="input-group">
                        <i class="fa-solid fa-search input-icon"></i>
                        <input type="text" id="searchProd" class="form-control" placeholder="Buscar por código, nombre..." oninput="filterProd(this.value)">
                    </div>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="nx-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th style="text-align:center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="prodTableBody">
                    <?php if (empty($listaProductos)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-icon">📦</div>
                                    <p>No hay productos registrados. Agrega el primero.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaProductos as $prod):
                            $stock = $prod->getStock();
                            if ($stock == 0)     { $stockClass = 'stock-zero'; $stockLabel = 'Sin stock'; }
                            elseif ($stock <= 5) { $stockClass = 'stock-low';  $stockLabel = 'Bajo: '.$stock; }
                            else                 { $stockClass = 'stock-ok';   $stockLabel = $stock; }
                            $catNombre = $mapCategorias[$prod->getIdCategoria()] ?? 'Sin cat.';
                        ?>
                        <tr>
                            <td class="fw"><?php echo $prod->getIdProducto(); ?></td>
                            <td><span class="badge badge-neutral"><?php echo htmlspecialchars($prod->getCodigo()); ?></span></td>
                            <td class="fw"><?php echo htmlspecialchars($prod->getNombre()); ?></td>
                            <td><span class="badge badge-accent"><?php echo htmlspecialchars($catNombre); ?></span></td>
                            <td class="fw" style="color:var(--success);">$<?php echo number_format($prod->getPrecio(), 2); ?></td>
                            <td>
                                <span class="badge <?php echo $stockClass; ?>">
                                    <?php if ($stock == 0): ?><i class="fa-solid fa-xmark" style="font-size:9px;"></i><?php elseif ($stock <= 5): ?><i class="fa-solid fa-triangle-exclamation" style="font-size:9px;"></i><?php endif; ?>
                                    <?php echo $stockLabel; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($prod->getEstado()): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px;"></i> Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-circle" style="font-size:7px;"></i> Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <button class="btn btn-warning btn-sm" title="Editar"
                                    onclick="openEditProd(<?php echo $prod->getIdProducto(); ?>, '<?php echo htmlspecialchars($prod->getCodigo(),ENT_QUOTES); ?>', '<?php echo htmlspecialchars($prod->getNombre(),ENT_QUOTES); ?>', <?php echo $prod->getIdCategoria(); ?>, <?php echo $prod->getPrecio(); ?>, <?php echo $prod->getStock(); ?>)">
                                    Editar
                                </button>
                                <a href="/Almacen_In/Capa_Presentacion/Controladores/ProductoController.php?accion=cambiar_estado&id=<?php echo $prod->getIdProducto(); ?>"
                                   class="btn <?php echo $prod->getEstado() ? 'btn-danger' : 'btn-success'; ?> btn-sm"
                                   title="<?php echo $prod->getEstado() ? 'Desactivar' : 'Activar'; ?>"
                                   onclick="return confirm('¿Confirmar cambio de estado?')">
                                    <?php echo $prod->getEstado() ? 'Desactivar' : 'Activar'; ?>
                                </a>
                                <a href="/Almacen_In/Capa_Presentacion/Controladores/ProductoController.php?accion=eliminar&id=<?php echo $prod->getIdProducto(); ?>"
                                   class="btn btn-danger btn-sm"
                                   title="Eliminar"
                                   onclick="return confirm('¿Eliminar este producto permanentemente?')">
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

<!-- MODAL: Nuevo Producto -->
<div class="modal-overlay" id="modalAddProd">
    <div class="modal-box">
        <div class="modal-header">
            <h4><i class="fa-solid fa-box-open" style="color:var(--accent);"></i> Registrar Nuevo Producto</h4>
            <button class="modal-close" onclick="closeModal('modalAddProd')">✕</button>
        </div>
        <form action="/Almacen_In/Capa_Presentacion/Controladores/ProductoController.php?accion=guardar" method="POST">
            <div class="modal-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Código *</label>
                        <input type="text" name="codigo" class="form-control" placeholder="Ej: PROD-001" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Categoría *</label>
                        <select name="id_categoria" class="form-select" required>
                            <option value="" disabled selected>Seleccionar...</option>
                            <?php foreach ($listaCategorias as $cat): ?>
                            <option value="<?php echo $cat->getIdCategoria(); ?>"><?php echo htmlspecialchars($cat->getNombreCategoria()); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre del Producto *</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Laptop Dell Inspiron 15" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Precio ($) *</label>
                        <input type="number" name="precio" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stock inicial *</label>
                        <input type="number" name="stock" class="form-control" placeholder="0" min="0" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalAddProd')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Editar Producto -->
<div class="modal-overlay" id="modalEditProd">
    <div class="modal-box">
        <div class="modal-header">
            <h4><i class="fa-solid fa-pen-to-square" style="color:var(--warning);"></i> Editar Producto</h4>
            <button class="modal-close" onclick="closeModal('modalEditProd')">✕</button>
        </div>
        <form action="/Almacen_In/Capa_Presentacion/Controladores/ProductoController.php?accion=editar" method="POST">
            <input type="hidden" name="id_producto" id="editProdId">
            <div class="modal-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Código *</label>
                        <input type="text" name="codigo" id="editProdCodigo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Categoría *</label>
                        <select name="id_categoria" id="editProdCat" class="form-select" required>
                            <?php foreach ($listaCategorias as $cat): ?>
                            <option value="<?php echo $cat->getIdCategoria(); ?>"><?php echo htmlspecialchars($cat->getNombreCategoria()); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre del Producto *</label>
                    <input type="text" name="nombre" id="editProdNombre" class="form-control" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Precio ($) *</label>
                        <input type="number" name="precio" id="editProdPrecio" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stock *</label>
                        <input type="number" name="stock" id="editProdStock" class="form-control" min="0" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEditProd')">Cancelar</button>
                <button type="submit" class="btn btn-warning"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open');    document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }
document.querySelectorAll('.modal-overlay').forEach(o => o.addEventListener('click', e => { if(e.target===o) closeModal(o.id); }));

function openEditProd(id, codigo, nombre, catId, precio, stock) {
    document.getElementById('editProdId').value     = id;
    document.getElementById('editProdCodigo').value = codigo;
    document.getElementById('editProdNombre').value = nombre;
    document.getElementById('editProdCat').value    = catId;
    document.getElementById('editProdPrecio').value = precio;
    document.getElementById('editProdStock').value  = stock;
    openModal('modalEditProd');
}

function filterProd(q) {
    const rows = document.querySelectorAll('#prodTableBody tr');
    q = q.toLowerCase();
    rows.forEach(row => { row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none'; });
}

document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => { el.style.transition='opacity 0.5s'; el.style.opacity='0'; setTimeout(()=>el.remove(),500); }, 5000);
});
</script>
</body>
</html>
