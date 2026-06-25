<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) { header('Location: login.php'); exit(); }

$idVenta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$idVenta) { header('Location: historial_ventas.php'); exit(); }

require_once __DIR__ . '/../Capa_Negocio/VentaNegocio.php';
require_once __DIR__ . '/../Capa_Datos/DetalleVentaDatos.php';
require_once __DIR__ . '/../Capa_Negocio/ProductoNegocio.php';
require_once __DIR__ . '/../Capa_Negocio/ClienteNegocio.php';

$ventaNegocio   = new VentaNegocio();
$productoNeg    = new ProductoNegocio();
$clienteNeg     = new ClienteNegocio();
$detallesDatos  = new DetalleVentaDatos();

$venta   = $ventaNegocio->obtenerVentaPorId($idVenta);
if (!$venta) { header('Location: historial_ventas.php'); exit(); }

$detalles = $detallesDatos->listarPorVenta($idVenta) ?? [];

$cliente = null;
if ($venta->getIdCliente()) {
    $cliente = $clienteNeg->obtenerPorId($venta->getIdCliente());
}

$pageTitle  = 'Comprobante #' . str_pad($idVenta, 4, '0', STR_PAD_LEFT);
$activePage = 'historial';
require_once __DIR__ . '/includes/header.php';
?>
<style>
.no-print-bar { display: flex; gap: 12px; justify-content: center; margin-bottom: 24px; }
</style>
<body>
<div class="app-shell">
<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

<div class="main-content">
    <header class="topbar no-print">
        <div class="topbar-left">
            <button class="hamburger" id="hamburgerBtn"><span></span><span></span><span></span></button>
            <div>
                <div class="topbar-title">Comprobante de Venta</div>
                <div class="topbar-subtitle"># <?php echo str_pad($idVenta, 4, '0', STR_PAD_LEFT); ?></div>
            </div>
        </div>
    </header>

    <main class="page-content">

        <div class="no-print-bar no-print">
            <a href="historial_ventas.php" class="btn btn-ghost">Volver al Historial</a>
            <button class="btn btn-primary" onclick="window.print()">Imprimir / Guardar PDF</button>
            <a href="ventas.php" class="btn btn-success">Nueva Venta</a>
        </div>

        <div class="receipt-wrapper">
            <div class="receipt-card">

                <!-- Header del comprobante -->
                <div class="receipt-header">
                    <h2>Sistema de Control de Inventario &amp; Ventas</h2>
                    <p>Comprobante Oficial de Transacción</p>
                    <div style="margin-top:14px;padding:8px 20px;background:rgba(255,255,255,0.15);border-radius:20px;display:inline-block;font-size:14px;font-weight:700;letter-spacing:1px;">
                        COMPROBANTE #<?php echo str_pad($idVenta, 4, '0', STR_PAD_LEFT); ?>
                    </div>
                </div>

                <!-- Info de la venta -->
                <div style="padding:24px 28px;border-bottom:1px solid var(--border);">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:4px;">Fecha de Emisión</div>
                            <div class="fw"><?php echo date('d/m/Y H:i:s', strtotime($venta->getFechaVenta())); ?></div>
                        </div>
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:4px;">Atendido por (Usuario ID)</div>
                            <div class="fw"><?php echo '#' . $venta->getIdUsuario(); ?></div>
                        </div>
                        <div>
                            <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:4px;">Cliente</div>
                            <div class="fw">
                                <?php if ($cliente): ?>
                                    <strong><?php echo htmlspecialchars($cliente->getNombreCompleto()); ?></strong><br>
                                    <span style="font-size:12px;color:var(--text-muted);">
                                        Persona: <?php echo $cliente->getPersonalidadJuridica() ? 'Jurídica' : 'Natural'; ?><br>
                                        <?php if (!$cliente->getPersonalidadJuridica() && $cliente->getDui()): ?>DUI: <?php echo htmlspecialchars($cliente->getDui()); ?><br><?php endif; ?>
                                        <?php if ($cliente->getNit()): ?>NIT: <?php echo htmlspecialchars($cliente->getNit()); ?><br><?php endif; ?>
                                        <?php if ($cliente->getPersonalidadJuridica() && $cliente->getNrc()): ?>NRC: <?php echo htmlspecialchars($cliente->getNrc()); ?><br><?php endif; ?>
                                        <?php if ($cliente->getPersonalidadJuridica() && $cliente->getGiro()): ?>Giro: <?php echo htmlspecialchars($cliente->getGiro()); ?><br><?php endif; ?>
                                        Tel: <?php echo htmlspecialchars($cliente->getTelefono()); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:var(--text-muted);">Venta directa (sin cliente registrado)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalle de productos -->
                <div style="padding:0 0 0 0;">
                    <table class="nx-table">
                        <thead>
                            <tr>
                                <th style="padding-left:28px;">Producto</th>
                                <th style="text-align:center;">Cant.</th>
                                <th style="text-align:right;">Precio Unit.</th>
                                <th style="text-align:right;padding-right:28px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($detalles)): ?>
                            <tr><td colspan="4" style="text-align:center;padding:20px;color:var(--text-muted);">Sin detalle disponible.</td></tr>
                        <?php else: ?>
                            <?php foreach ($detalles as $det):
                                $prod = $productoNeg->obtenerPorId($det->getIdProducto());
                                $nombreProd = $prod ? $prod->getNombre() : 'Producto #' . $det->getIdProducto();
                                $sub = $det->getSubtotal();
                            ?>
                            <tr>
                                <td style="padding-left:28px;" class="fw"><?php echo htmlspecialchars($nombreProd); ?></td>
                                <td style="text-align:center;"><?php echo $det->getCantidad(); ?></td>
                                <td style="text-align:right;color:var(--text-secondary);">$<?php echo number_format($det->getPrecioUnitario(), 2); ?></td>
                                <td style="text-align:right;padding-right:28px;color:var(--success);font-weight:600;">$<?php echo number_format($sub, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Totales -->
                <div style="padding:20px 28px;background:var(--bg-surface-2);border-top:1px solid var(--border);">
                    <div class="cart-total-row">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-semibold">$<?php echo number_format($venta->getSubtotal(), 2); ?></span>
                    </div>
                    <div class="cart-total-row">
                        <span class="text-muted">IVA (13%)</span>
                        <span class="fw-semibold" style="color:var(--warning);">$<?php echo number_format($venta->getIva(), 2); ?></span>
                    </div>
                    <div class="cart-total-row grand-total">
                        <span>TOTAL A PAGAR</span>
                        <span>$<?php echo number_format($venta->getTotal(), 2); ?></span>
                    </div>
                </div>

                <!-- Footer del comprobante -->
                <div style="padding:20px 28px;text-align:center;color:var(--text-muted);font-size:12px;border-top:1px solid var(--border);">
                    Transacción completada exitosamente · Sistema de Control de Inventario &amp; Ventas &copy; <?php echo date('Y'); ?><br>
                    <span style="font-size:11px;margin-top:4px;display:block;">¡Gracias por su compra!</span>
                </div>

            </div><!-- /.receipt-card -->
        </div><!-- /.receipt-wrapper -->

    </main>
</div>
</div>
</body>
</html>
