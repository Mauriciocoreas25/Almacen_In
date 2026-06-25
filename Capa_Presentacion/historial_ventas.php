<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) { header('Location: login.php'); exit(); }

require_once __DIR__ . '/../Capa_Negocio/VentaNegocio.php';
$ventaNegocio = new VentaNegocio();
$historial    = $ventaNegocio->listarHistorialVentas() ?? [];

$flashOk  = $_SESSION['flash_ok']  ?? ''; unset($_SESSION['flash_ok']);
$flashErr = $_SESSION['flash_err'] ?? ''; unset($_SESSION['flash_err']);

$pageTitle  = 'Historial de Ventas';
$activePage = 'historial';
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
                <div class="topbar-title">Historial de Ventas</div>
                <div class="topbar-subtitle"><?php echo count($historial); ?> transacción(es) registradas</div>
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

        <div class="page-header">
            <div class="page-header-left">
                <h1><i class="fa-solid fa-receipt" style="color:var(--accent);margin-right:8px;"></i>Historial de Ventas</h1>
                <p>Registro completo de todas las transacciones realizadas</p>
            </div>
            <a href="/Almacen_In/Capa_Presentacion/ventas.php" class="btn btn-primary">
                <i class="fa-solid fa-cart-plus"></i> Nueva Venta
            </a>
        </div>

        <!-- Filtro por fechas -->
        <div class="card mb-16" style="margin-bottom:16px;">
            <div class="card-body" style="padding:16px 20px;">
                <div style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">
                    <div>
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" id="fechaInicio" class="form-control" style="width:180px;">
                    </div>
                    <div>
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" id="fechaFin" class="form-control" style="width:180px;">
                    </div>
                    <button class="btn btn-primary" onclick="filtrarPorFecha()">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                    <button class="btn btn-ghost" onclick="limpiarFiltro()">
                        <i class="fa-solid fa-xmark"></i> Limpiar
                    </button>
                    <div style="margin-left:auto;padding:10px 16px;background:var(--accent-subtle);border:1px solid rgba(21,128,61,0.2);border-radius:var(--radius-sm);font-size:13px;">
                        <span style="color:var(--text-muted);">Total período:</span>
                        <span id="totalPeriodo" style="font-weight:700;color:var(--accent);margin-left:8px;">$<?php
                            $totalGlobal = 0;
                            foreach ($historial as $v) { 
                                if ($v->getEstado() !== 'Anulada') {
                                    $totalGlobal += $v->getTotal(); 
                                }
                            }
                            echo number_format($totalGlobal, 2);
                        ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-wrapper">
                <table class="nx-table">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha</th>
                            <th>ID Cliente</th>
                            <th style="text-align:right;">Subtotal</th>
                            <th style="text-align:right;">IVA</th>
                            <th style="text-align:right;">Total</th>
                            <th style="text-align:center;">Estado</th>
                            <th style="text-align:center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="historialBody">
                    <?php if (empty($historial)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">🧾</div>
                                    <p>No hay ventas registradas aún. <a href="ventas.php">Registrar primera venta</a></p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($historial as $venta):
                            $esAnulada = ($venta->getEstado() === 'Anulada');
                        ?>
                        <tr class="venta-row" data-fecha="<?php echo $venta->getFechaVenta(); ?>" data-estado="<?php echo htmlspecialchars($venta->getEstado()); ?>" <?php echo $esAnulada ? 'style="opacity: 0.65; background-color: var(--bg-surface-2);"' : ''; ?>>
                            <td><span class="badge badge-accent">#<?php echo str_pad($venta->getIdVenta(), 4, '0', STR_PAD_LEFT); ?></span></td>
                            <td class="fw"><?php echo date('d/m/Y H:i', strtotime($venta->getFechaVenta())); ?></td>
                            <td>
                                <?php if ($venta->getIdCliente()): ?>
                                    <span class="badge badge-neutral"><i class="fa-solid fa-user" style="font-size:9px;"></i> #<?php echo $venta->getIdCliente(); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">— Directo</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;color:var(--text-secondary);">$<?php echo number_format($venta->getSubtotal(), 2); ?></td>
                            <td style="text-align:right;color:var(--warning);">$<?php echo number_format($venta->getIva(), 2); ?></td>
                            <td style="text-align:right;font-weight:700;color:var(--success);">$<?php echo number_format($venta->getTotal(), 2); ?></td>
                            <td style="text-align:center;">
                                <?php if ($esAnulada): ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-ban" style="font-size:9px;margin-right:4px;"></i>Anulada</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-circle-check" style="font-size:9px;margin-right:4px;"></i>Registrada</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;gap:6px;justify-content:center;">
                                    <a href="/Almacen_In/Capa_Presentacion/comprobante.php?id=<?php echo $venta->getIdVenta(); ?>"
                                       class="btn btn-primary btn-sm" target="_blank" title="Ver Comprobante">
                                        <i class="fa-solid fa-file-invoice"></i> Ver
                                    </a>
                                    <?php if (!$esAnulada): ?>
                                        <a href="/Almacen_In/Capa_Presentacion/Controladores/VentaController.php?accion=anular&id=<?php echo $venta->getIdVenta(); ?>"
                                           class="btn btn-danger btn-sm" title="Anular Venta"
                                           onclick="return confirm('¿Está seguro de anular esta venta? El stock de los productos será devuelto al inventario.')">
                                            <i class="fa-solid fa-trash"></i> Anular
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-neutral btn-sm" disabled style="opacity:0.5;cursor:not-allowed;" title="Venta Anulada">
                                            <i class="fa-solid fa-ban"></i> Anulada
                                        </button>
                                    <?php endif; ?>
                                </div>
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

<script>
function filtrarPorFecha() {
    const inicio = document.getElementById('fechaInicio').value;
    const fin    = document.getElementById('fechaFin').value;
    const rows   = document.querySelectorAll('.venta-row');
    let   total  = 0;

    rows.forEach(row => {
        const fecha = row.dataset.fecha.substring(0, 10);
        const show  = (!inicio || fecha >= inicio) && (!fin || fecha <= fin);
        row.style.display = show ? '' : 'none';
        if (show) {
            // Sumar el total de las filas visibles, ignorando anuladas
            if (row.dataset.estado !== 'Anulada') {
                const celdaTotal = row.cells[5].textContent.replace('$','').replace(',','');
                total += parseFloat(celdaTotal) || 0;
            }
        }
    });
    document.getElementById('totalPeriodo').textContent = '$' + total.toFixed(2);
}

function limpiarFiltro() {
    document.getElementById('fechaInicio').value = '';
    document.getElementById('fechaFin').value    = '';
    const rows = document.querySelectorAll('.venta-row');
    let total = 0;
    rows.forEach(r => {
        r.style.display = '';
        if (r.dataset.estado !== 'Anulada') {
            const celdaTotal = r.cells[5].textContent.replace('$','').replace(',','');
            total += parseFloat(celdaTotal) || 0;
        }
    });
    document.getElementById('totalPeriodo').textContent = '$' + total.toFixed(2);
}

document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => { el.style.transition='opacity 0.5s'; el.style.opacity='0'; setTimeout(()=>el.remove(),500); }, 5000);
});
</script>
</body>
</html>
