<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) { header('Location: login.php'); exit(); }

require_once __DIR__ . '/../Capa_Negocio/ProductoNegocio.php';
require_once __DIR__ . '/../Capa_Negocio/VentaNegocio.php';

$productoNegocio = new ProductoNegocio();
$ventaNegocio    = new VentaNegocio();

// Datos para reportes
$bajoStock   = $productoNegocio->obtenerBajoStock(5) ?? [];

// Fechas por defecto: mes actual
$hoy    = date('Y-m-d');
$inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fin    = $_GET['fecha_fin']    ?? $hoy;

$ventasPeriodo  = $ventaNegocio->generarReporteVentasPorFechas($inicio, $fin) ?? [];
$ingresos       = $ventaNegocio->obtenerIngresosTotales($inicio, $fin) ?? 0;
$topProductos   = $ventaNegocio->generarReporteProductosMasVendidos(10) ?? [];

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
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
                <div class="topbar-title">Dashboard General</div>
                <div class="topbar-subtitle">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?> 👋</div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-date"><i class="fa-regular fa-calendar" style="margin-right:6px;color:var(--accent);"></i><?php echo date('d M Y'); ?></div>
        </div>
    </header>

    <main class="page-content">

        <!-- ═══ KPI: Resumen Período ═══ -->
        <div class="kpi-grid mb-24" style="margin-bottom:24px;">
            <div class="kpi-card success">
                <div class="kpi-value"><?php echo count($bajoStock); ?></div>
                <div class="kpi-label">Productos con bajo stock</div>
            </div>
            <div class="kpi-card success">
                <div class="kpi-value">$<?php echo number_format($ingresos, 2); ?></div>
                <div class="kpi-label">Ingresos en el período</div>
            </div>
            <div class="kpi-card success">
                <div class="kpi-value"><?php echo count($ventasPeriodo); ?></div>
                <div class="kpi-label">Ventas en el período</div>
            </div>
        </div>

        <!-- ═══ Sección 1: Bajo Stock ═══ -->
        <div class="card mb-24" style="margin-bottom:24px;">
            <div class="card-header">
                <h2 class="card-title">
                    Productos con Bajo Stock (≤ 5 unidades)
                </h2>
                <?php if (!empty($bajoStock)): ?>
                <span class="badge badge-warning"><?php echo count($bajoStock); ?> alerta(s)</span>
                <?php endif; ?>
            </div>
            <div class="table-wrapper">
                <table class="nx-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th style="text-align:center;">Stock Actual</th>
                            <th>Estado</th>
                            <th style="text-align:center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($bajoStock)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center;padding:24px;color:var(--text-muted);">
                                Todos los productos tienen stock suficiente.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bajoStock as $prod): ?>
                        <tr>
                            <td><span class="badge badge-neutral"><?php echo htmlspecialchars($prod->getCodigo()); ?></span></td>
                            <td class="fw"><?php echo htmlspecialchars($prod->getNombre()); ?></td>
                            <td style="text-align:center;">
                                <?php $s = $prod->getStock(); ?>
                                <span class="badge <?php echo $s == 0 ? 'stock-zero' : 'stock-low'; ?>" style="font-size:13px;padding:4px 12px;">
                                    <?php echo $s; ?> uds
                                </span>
                            </td>
                            <td>
                                <?php if ($prod->getStock() == 0): ?>
                                    <span class="badge badge-danger">Sin existencias</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Requiere reabastecimiento</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <a href="/Almacen_In/Capa_Presentacion/productos.php" class="btn btn-primary btn-sm">
                                    Actualizar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ═══ Sección 2: Ventas por período ═══ -->
        <div class="card mb-24" style="margin-bottom:24px;">
            <div class="card-header">
                <h2 class="card-title">
                    Historial de Ventas por Rango de Fechas
                </h2>
                <div>
                    <form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                        <div>
                            <label class="form-label" style="margin-bottom:4px;">Desde</label>
                            <input type="date" name="fecha_inicio" value="<?php echo htmlspecialchars($inicio); ?>" class="form-control" style="width:160px;">
                        </div>
                        <div>
                            <label class="form-label" style="margin-bottom:4px;">Hasta</label>
                            <input type="date" name="fecha_fin" value="<?php echo htmlspecialchars($fin); ?>" class="form-control" style="width:160px;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    </form>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="nx-table">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha</th>
                            <th style="text-align:right;">Subtotal</th>
                            <th style="text-align:right;">IVA</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($ventasPeriodo)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-icon">📊</div>
                                    <p>No hay ventas en el período seleccionado.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ventasPeriodo as $v): ?>
                        <tr>
                            <td><span class="badge badge-accent">#<?php echo str_pad($v->getIdVenta(), 4, '0', STR_PAD_LEFT); ?></span></td>
                            <td class="fw"><?php echo date('d/m/Y H:i', strtotime($v->getFechaVenta())); ?></td>
                            <td style="text-align:right;">$<?php echo number_format($v->getSubtotal(), 2); ?></td>
                            <td style="text-align:right;color:var(--warning);">$<?php echo number_format($v->getIva(), 2); ?></td>
                            <td style="text-align:right;font-weight:700;color:var(--success);">$<?php echo number_format($v->getTotal(), 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="background:var(--bg-surface-2);border-top:2px solid var(--accent);">
                            <td colspan="4" style="text-align:right;font-weight:700;padding-right:16px;">Total Acumulado del Período:</td>
                            <td style="text-align:right;font-family:'Outfit',sans-serif;font-size:17px;font-weight:800;color:var(--accent);">$<?php echo number_format($ingresos, 2); ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ═══ Sección 3: Top Productos Más Vendidos ═══ -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    Top Productos Más Vendidos
                </h2>
            </div>
            <div class="table-wrapper">
                <table class="nx-table">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Ranking</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th style="text-align:right;">Unidades Vendidas</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($topProductos)): ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="empty-icon">🏆</div>
                                    <p>Sin datos de ventas para generar el ranking.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($topProductos as $rank => $prod): ?>
                        <tr>
                            <td style="text-align:center;" class="fw">
                                <?php echo $rank + 1; ?>
                            </td>
                            <td><span class="badge badge-neutral"><?php echo htmlspecialchars($prod['codigo']); ?></span></td>
                            <td class="fw"><?php echo htmlspecialchars($prod['nombre']); ?></td>
                            <td style="text-align:right;">
                                <span style="font-family:'Outfit',sans-serif;font-size:16px;font-weight:700;color:var(--accent);">
                                    <?php echo $prod['total_unidades_vendidas']; ?>
                                </span>
                                <span class="text-muted" style="font-size:12px;"> uds</span>
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
</body>
</html>