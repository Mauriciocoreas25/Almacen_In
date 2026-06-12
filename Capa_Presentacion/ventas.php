<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) { header('Location: login.php'); exit(); }

require_once __DIR__ . '/../Capa_Negocio/ClienteNegocio.php';
require_once __DIR__ . '/../Capa_Negocio/ProductoNegocio.php';

$clienteNegocio  = new ClienteNegocio();
$productoNegocio = new ProductoNegocio();
$listaClientes   = $clienteNegocio->listarClientes()   ?? [];
$listaProductos  = $productoNegocio->listarProductos() ?? [];

// Solo productos activos con stock
$productosDisponibles = array_filter($listaProductos, fn($p) => $p->getStock() > 0 && $p->getEstado());

$flashErr = $_SESSION['flash_err'] ?? ''; unset($_SESSION['flash_err']);

$pageTitle  = 'Nueva Venta';
$activePage = 'ventas';
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
                <div class="topbar-title">Nueva Venta</div>
                <div class="topbar-subtitle">Punto de Venta — POS</div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-date"><i class="fa-regular fa-calendar" style="margin-right:6px;color:var(--accent);"></i><?php echo date('d M Y, H:i'); ?></div>
        </div>
    </header>

    <main class="page-content">
        <?php if ($flashErr): ?>
        <div class="alert alert-danger mb-16"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($flashErr); ?></div>
        <?php endif; ?>

        <div class="pos-grid">

            <!-- ── Columna Izquierda: Productos + Carrito ── -->
            <div>
                <!-- Selección de Cliente -->
                <div class="card mb-16" style="margin-bottom:16px;">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fa-solid fa-user" style="color:var(--accent);"></i> Cliente</h2>
                    </div>
                    <div class="card-body" style="padding:16px 20px;">
                        <select id="clienteSelect" class="form-select" style="max-width:400px;" onchange="updateClienteHidden()">
                            <option value="">-- Sin cliente (venta directa) --</option>
                            <?php foreach ($listaClientes as $cli): ?>
                            <option value="<?php echo $cli->getIdCliente(); ?>">
                                <?php echo htmlspecialchars($cli->getNombreCompleto()); ?>
                                <?php if ($cli->getDui()): ?> (DUI: <?php echo htmlspecialchars($cli->getDui()); ?>)<?php endif; ?>
                                <?php echo $cli->getPersonalidadJuridica() ? ' [Jurídico]' : ' [Natural]'; ?>
                                <?php if ($cli->getTelefono()): ?> · Tel: <?php echo htmlspecialchars($cli->getTelefono()); ?><?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Buscador de Productos -->
                <div class="card mb-16" style="margin-bottom:16px;">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fa-solid fa-boxes-stacked" style="color:var(--accent);"></i> Agregar Productos</h2>
                    </div>
                    <div class="card-body" style="padding:16px 20px;">
                        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
                            <div style="flex:1;min-width:200px;">
                                <label class="form-label">Buscar Producto</label>
                                <div class="input-group">
                                    <i class="fa-solid fa-search input-icon"></i>
                                    <input type="text" id="prodSearch" class="form-control" placeholder="Código o nombre..." oninput="filterProductSearch(this.value)">
                                </div>
                            </div>
                            <div style="min-width:180px;">
                                <label class="form-label">Seleccionar</label>
                                <select id="prodSelect" class="form-select">
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($productosDisponibles as $prod): ?>
                                    <option value="<?php echo $prod->getIdProducto(); ?>"
                                            data-nombre="<?php echo htmlspecialchars($prod->getNombre()); ?>"
                                            data-precio="<?php echo $prod->getPrecio(); ?>"
                                            data-stock="<?php echo $prod->getStock(); ?>"
                                            data-codigo="<?php echo htmlspecialchars($prod->getCodigo()); ?>">
                                        [<?php echo htmlspecialchars($prod->getCodigo()); ?>] <?php echo htmlspecialchars($prod->getNombre()); ?> — $<?php echo number_format($prod->getPrecio(),2); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div style="min-width:90px;">
                                <label class="form-label">Cantidad</label>
                                <input type="number" id="cantidadInput" class="form-control" value="1" min="1" style="width:90px;">
                            </div>
                            <div>
                                <label class="form-label" style="opacity:0;">.</label>
                                <button class="btn btn-success" onclick="agregarAlCarrito()" style="white-space:nowrap;">
                                    <i class="fa-solid fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carrito -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fa-solid fa-cart-shopping" style="color:var(--accent);"></i> Carrito de Compra</h2>
                        <span id="cartCount" class="badge badge-accent">0 ítem(s)</span>
                    </div>
                    <div class="table-wrapper">
                        <table class="nx-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th style="text-align:center;">Cantidad</th>
                                    <th style="text-align:right;">Precio Unit.</th>
                                    <th style="text-align:right;">Subtotal</th>
                                    <th style="text-align:center;">Quitar</th>
                                </tr>
                            </thead>
                            <tbody id="cartTableBody">
                                <tr id="cartEmptyRow">
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <div class="empty-icon">🛒</div>
                                            <p>El carrito está vacío. Agrega productos arriba.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ── Panel Derecho: Total + Procesar ── -->
            <div class="cart-panel">
                <div class="card-header">
                    <h2 class="card-title"><i class="fa-solid fa-calculator" style="color:var(--accent);"></i> Resumen</h2>
                </div>
                <div style="padding:20px;">
                    <div class="cart-total-row">
                        <span class="text-muted">Subtotal</span>
                        <span id="totalSubtotal" class="fw-semibold">$0.00</span>
                    </div>
                    <div class="cart-total-row">
                        <span class="text-muted">IVA (13%)</span>
                        <span id="totalIva" class="fw-semibold">$0.00</span>
                    </div>
                    <div class="cart-total-row grand-total">
                        <span>TOTAL</span>
                        <span id="totalFinal">$0.00</span>
                    </div>

                    <div class="divider"></div>

                    <form id="ventaForm" action="/Almacen_In/Capa_Presentacion/Controladores/VentaController.php?accion=procesar" method="POST">
                        <input type="hidden" name="id_cliente"       id="hiddenCliente"  value="">
                        <input type="hidden" name="carrito_json"     id="hiddenCarrito"  value="">
                        <input type="hidden" name="total_subtotal"   id="hiddenSubtotal" value="0">
                        <input type="hidden" name="total_iva"        id="hiddenIva"      value="0">
                        <input type="hidden" name="total_final"      id="hiddenTotal"    value="0">

                        <button type="button" id="btnProcesar" class="btn btn-success w-100 btn-lg" onclick="procesarVenta()" disabled>
                            <i class="fa-solid fa-cash-register"></i> Procesar Venta
                        </button>
                    </form>

                    <button class="btn btn-danger w-100 mt-8" style="margin-top:8px;" onclick="limpiarCarrito()">
                        <i class="fa-solid fa-trash"></i> Limpiar Carrito
                    </button>

                    <div class="divider"></div>
                    <div style="font-size:12px;color:var(--text-muted);text-align:center;line-height:1.8;">
                        <i class="fa-solid fa-shield-halved" style="color:var(--success);"></i> El stock se actualiza automáticamente<br>
                        <i class="fa-solid fa-file-invoice" style="color:var(--info);"></i> Se genera comprobante al confirmar
                    </div>
                </div>
            </div>

        </div><!-- /.pos-grid -->
    </main>
</div>
</div>

<!-- Datos de productos para JS -->
<script>
const PRODUCTOS_DATA = <?php
    $prodArr = [];
    foreach ($productosDisponibles as $p) {
        $prodArr[] = [
            'id'     => $p->getIdProducto(),
            'codigo' => $p->getCodigo(),
            'nombre' => $p->getNombre(),
            'precio' => (float)$p->getPrecio(),
            'stock'  => (int)$p->getStock(),
        ];
    }
    echo json_encode($prodArr);
?>;

// ── Carrito State ─────────────────────────────────────
let carrito = []; // [{id, codigo, nombre, precio, cantidad, stock}]

// ── Filtro de búsqueda de producto ───────────────────
function filterProductSearch(q) {
    q = q.toLowerCase();
    const sel = document.getElementById('prodSelect');
    Array.from(sel.options).forEach(opt => {
        if (!opt.value) return;
        opt.style.display = (opt.text.toLowerCase().includes(q) || !q) ? '' : 'none';
    });
}

// ── Agregar al carrito ────────────────────────────────
function agregarAlCarrito() {
    const sel      = document.getElementById('prodSelect');
    const cantidad = parseInt(document.getElementById('cantidadInput').value) || 1;
    if (!sel.value) { showToast('Selecciona un producto primero.', 'danger'); return; }

    const opt    = sel.selectedOptions[0];
    const id     = parseInt(sel.value);
    const nombre = opt.dataset.nombre;
    const precio = parseFloat(opt.dataset.precio);
    const stock  = parseInt(opt.dataset.stock);
    const codigo = opt.dataset.codigo;

    if (cantidad < 1) { showToast('La cantidad debe ser al menos 1.', 'danger'); return; }

    // Verificar si ya está en el carrito
    const existente = carrito.find(item => item.id === id);
    if (existente) {
        const nuevaCant = existente.cantidad + cantidad;
        if (nuevaCant > stock) { showToast(`Stock insuficiente. Disponible: ${stock}`, 'danger'); return; }
        existente.cantidad = nuevaCant;
    } else {
        if (cantidad > stock) { showToast(`Stock insuficiente. Disponible: ${stock}`, 'danger'); return; }
        carrito.push({ id, codigo, nombre, precio, cantidad, stock });
    }

    renderCarrito();
    showToast(`${nombre} agregado al carrito.`, 'success');
    document.getElementById('cantidadInput').value = 1;
}

// ── Eliminar del carrito ──────────────────────────────
function quitarDelCarrito(id) {
    carrito = carrito.filter(item => item.id !== id);
    renderCarrito();
}

// ── Cambiar cantidad inline ───────────────────────────
function cambiarCantidad(id, valor) {
    const item = carrito.find(i => i.id === id);
    if (!item) return;
    const nueva = parseInt(valor);
    if (nueva < 1)           { showToast('Mínimo 1 unidad.', 'danger'); return; }
    if (nueva > item.stock)  { showToast(`Stock máximo: ${item.stock}`, 'danger'); return; }
    item.cantidad = nueva;
    renderCarrito();
}

// ── Renderizar tabla del carrito ──────────────────────
function renderCarrito() {
    const tbody      = document.getElementById('cartTableBody');
    const emptyRow   = document.getElementById('cartEmptyRow');
    const btnProcesar= document.getElementById('btnProcesar');
    const countEl    = document.getElementById('cartCount');

    // Limpiar filas previas (excepto emptyRow)
    Array.from(tbody.querySelectorAll('tr.cart-item')).forEach(r => r.remove());

    if (carrito.length === 0) {
        if (emptyRow) emptyRow.style.display = '';
        btnProcesar.disabled = true;
        countEl.textContent = '0 ítem(s)';
        updateTotales();
        return;
    }

    if (emptyRow) emptyRow.style.display = 'none';
    btnProcesar.disabled = false;
    countEl.textContent  = `${carrito.length} ítem(s)`;

    carrito.forEach(item => {
        const subtotal = (item.precio * item.cantidad).toFixed(2);
        const tr = document.createElement('tr');
        tr.className = 'cart-item';
        tr.innerHTML = `
            <td>
                <div>
                    <div class="fw">${item.nombre}</div>
                    <div style="font-size:11px;color:var(--text-muted);">${item.codigo}</div>
                </div>
            </td>
            <td style="text-align:center;">
                <input type="number" value="${item.cantidad}" min="1" max="${item.stock}"
                       class="form-control" style="width:70px;margin:0 auto;text-align:center;"
                       onchange="cambiarCantidad(${item.id}, this.value)">
            </td>
            <td style="text-align:right;color:var(--text-secondary);">$${item.precio.toFixed(2)}</td>
            <td style="text-align:right;color:var(--success);font-weight:600;">$${subtotal}</td>
            <td style="text-align:center;">
                <button class="btn btn-danger btn-sm" onclick="quitarDelCarrito(${item.id})">
                    Quitar
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    updateTotales();
}

// ── Calcular y mostrar totales ────────────────────────
function updateTotales() {
    const subtotal = carrito.reduce((acc, i) => acc + (i.precio * i.cantidad), 0);
    const iva      = subtotal * 0.13;
    const total    = subtotal + iva;

    document.getElementById('totalSubtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('totalIva').textContent      = `$${iva.toFixed(2)}`;
    document.getElementById('totalFinal').textContent    = `$${total.toFixed(2)}`;

    document.getElementById('hiddenSubtotal').value = subtotal.toFixed(2);
    document.getElementById('hiddenIva').value      = iva.toFixed(2);
    document.getElementById('hiddenTotal').value    = total.toFixed(2);
}

// ── Update cliente hidden ─────────────────────────────
function updateClienteHidden() {
    document.getElementById('hiddenCliente').value = document.getElementById('clienteSelect').value;
}

// ── Limpiar carrito ───────────────────────────────────
function limpiarCarrito() {
    if (carrito.length === 0) return;
    if (!confirm('¿Limpiar todo el carrito?')) return;
    carrito = [];
    renderCarrito();
}

// ── Procesar venta ────────────────────────────────────
function procesarVenta() {
    if (carrito.length === 0) { showToast('El carrito está vacío.', 'danger'); return; }
    if (!confirm('¿Confirmar y procesar esta venta?')) return;

    document.getElementById('hiddenCarrito').value  = JSON.stringify(carrito);
    document.getElementById('hiddenCliente').value  = document.getElementById('clienteSelect').value;

    const btn = document.getElementById('btnProcesar');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando...';
    btn.disabled  = true;

    document.getElementById('ventaForm').submit();
}

// ── Toast ─────────────────────────────────────────────
function showToast(msg, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<i class="fa-solid ${type==='success'?'fa-circle-check':'fa-circle-exclamation'}"></i> ${msg}`;
    container.appendChild(toast);
    setTimeout(() => { toast.style.transition='opacity 0.4s'; toast.style.opacity='0'; setTimeout(()=>toast.remove(),400); }, 3500);
}

document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => { el.style.transition='opacity 0.5s'; el.style.opacity='0'; setTimeout(()=>el.remove(),500); }, 5000);
});
</script>
</body>
</html>
